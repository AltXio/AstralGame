document.addEventListener('DOMContentLoaded', () => {
    const solarSystem = document.getElementById('solarSystem');
    const orbitsContainer = document.getElementById('orbitsContainer');
    if (!solarSystem || !orbitsContainer) return;

    // 1. Datos inyectados desde Laravel (BD)
    const {
        planetasDisponibles: planetasBD = [],
        melodiasGuardadas: melodiasIniciales = [],
        saveUrl = '',
        csrfToken = ''
    } = window.OCARINA_DATA || {};

    let melodiasBD = [...melodiasIniciales];
    let melodiaActivaId = null;

    // 4 planetas de prueba para usuarios sin planetas creados aún en la BD
    const planetasPrototip = [
        { id: 1761650000001, nombre: 'Zephyrus', habitabilidad: 80, dominante: 'aire', radio: 28, esPrueba: true },
        { id: 1761650000002, nombre: 'Ignis', habitabilidad: 85, dominante: 'fuego', radio: 32, esPrueba: true },
        { id: 1761650000003, nombre: 'Aquaria', habitabilidad: 82, dominante: 'agua', radio: 30, esPrueba: true },
        { id: 1761650000004, nombre: 'Terra', habitabilidad: 90, dominante: 'tierra', radio: 34, esPrueba: true }
    ];

    const usandoPlanetasPrueba = planetasBD.length === 0;
    const planetasDisponibles = usandoPlanetasPrueba ? planetasPrototip : planetasBD;
   
    function obtenerDominante(planeta) {
        if (planeta.dominante) return planeta.dominante.toLowerCase();
        if (planeta.elemento_dominante && planeta.elemento_dominante.nombre) {
            return planeta.elemento_dominante.nombre.toLowerCase();
        }
        return 'aire';
    }

    function getColorDominante(dominante) {
        switch (dominante) {
            case 'fuego': return '#ff8a5c';
            case 'agua': return '#4cc9f0';
            case 'tierra': return '#8fcf8a';
            case 'aire': return '#c0a9ff';
            default: return '#93a0bd';
        }
    }

    const OCARINA_NOTES = {
        'C-Down': 293.66,
        'A':      349.23,
        'C-Left': 440.00,
        'C-Right': 493.88,
        'C-Up':   587.33
    };

    let audioCtx = null;
    let delayNode = null;
    let feedbackGain = null;
    let isPlaying = false;

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            
            delayNode = audioCtx.createDelay();
            delayNode.delayTime.value = 0.18;

            feedbackGain = audioCtx.createGain();
            feedbackGain.gain.value = 0.25;

            const wetFilter = audioCtx.createBiquadFilter();
            wetFilter.type = 'lowpass';
            wetFilter.frequency.value = 1200;

            delayNode.connect(wetFilter);
            wetFilter.connect(feedbackGain);
            feedbackGain.connect(delayNode);
            wetFilter.connect(audioCtx.destination);
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    function getAcordOcarinaPlaneta(planeta) {
        const dominante = obtenerDominante(planeta);
        let baseFreq = OCARINA_NOTES['C-Down'];
        let noteName = 'C-Down (Re4)';

        if (dominante === 'fuego') { baseFreq = OCARINA_NOTES['C-Up']; noteName = 'C-Up (Re5)'; }
        else if (dominante === 'agua') { baseFreq = OCARINA_NOTES['C-Right']; noteName = 'C-Right (Si4)'; }
        else if (dominante === 'tierra') { baseFreq = OCARINA_NOTES['A']; noteName = 'A (Fa4)'; }
        else if (dominante === 'aire') { baseFreq = OCARINA_NOTES['C-Left']; noteName = 'C-Left (La4)'; }

        return {
            noteLabel: noteName,
            freqs: [baseFreq]
        };
    }

    function tocarOcarinaSound(freqsArray, durada = 0.65) {
        initAudio();
        const now = audioCtx.currentTime;
        const freq = Array.isArray(freqsArray) ? freqsArray[0] : freqsArray;

        const oscMain = audioCtx.createOscillator();
        oscMain.type = 'sine';
        oscMain.frequency.setValueAtTime(freq * 0.985, now);
        oscMain.frequency.exponentialRampToValueAtTime(freq, now + 0.035);

        const oscBody = audioCtx.createOscillator();
        oscBody.type = 'triangle';
        oscBody.frequency.setValueAtTime(freq, now);

        const bodyGain = audioCtx.createGain();
        bodyGain.gain.setValueAtTime(0.04, now);

        const vibrato = audioCtx.createOscillator();
        const vibratoGain = audioCtx.createGain();
        vibrato.frequency.setValueAtTime(5.5, now);

        vibratoGain.gain.setValueAtTime(0, now);
        vibratoGain.gain.linearRampToValueAtTime(0, now + 0.18);
        vibratoGain.gain.linearRampToValueAtTime(freq * 0.01, now + 0.35);

        vibrato.connect(vibratoGain);
        vibratoGain.connect(oscMain.frequency);
        vibratoGain.connect(oscBody.frequency);

        const mainGain = audioCtx.createGain();
        mainGain.gain.setValueAtTime(0.0001, now);
        mainGain.gain.linearRampToValueAtTime(0.28, now + 0.04);
        mainGain.gain.setValueAtTime(0.24, now + durada - 0.12);
        mainGain.gain.exponentialRampToValueAtTime(0.0001, now + durada);

        oscMain.connect(mainGain);
        oscBody.connect(bodyGain);
        bodyGain.connect(mainGain);

        mainGain.connect(audioCtx.destination);
        mainGain.connect(delayNode);

        oscMain.start(now);
        oscBody.start(now);
        vibrato.start(now);

        oscMain.stop(now + durada);
        oscBody.stop(now + durada);
        vibrato.stop(now + durada);
    }

    // Estado del sistema solar DOM
    const maxOrbits = 15;
    let currentOrbitCount = 3;
    let orbitRadii = [];
    let placedPlanets = [];
    let instanceCounter = 0;

    // 2. Cargar una melodía concreta en el sistema solar
    function cargarMelodia(melodia) {
        if (!melodia) return;

        melodiaActivaId = melodia.id;
        const nameInput = document.getElementById('melodyNameInput');
        if (nameInput) nameInput.value = melodia.nombre;

        const orbitas = melodia.orbitas || [];
        if (orbitas.length > 0) {
            const maxOrbitaBD = Math.max(...orbitas.map(o => o.numero_orbita));
            currentOrbitCount = Math.min(maxOrbits, Math.max(3, maxOrbitaBD));
        }

        placedPlanets = orbitas
            .filter(registro => registro.planeta)
            .map(registro => {
                const orbitIndex = registro.numero_orbita - 1;
                return {
                    instanceId: ++instanceCounter,
                    orbitIndex: orbitIndex,
                    angle: parseFloat(registro.angulo) || 0,
                    speed: 0.018 / (orbitIndex + 1),
                    planeta: registro.planeta
                };
            });

        updateOrbitRings();
        renderPlacedPlanets();
        renderMelodySidebar();
    }

    function updateOrbitRings() {
        orbitsContainer.innerHTML = '';
        orbitRadii = [];

        const systemSize = solarSystem.clientWidth;
        const minRadius = 45;
        const maxRadius = (systemSize / 2) - 20;
        const step = (maxRadius - minRadius) / Math.max(1, currentOrbitCount - 1);

        for (let i = 0; i < currentOrbitCount; i++) {
            const r = currentOrbitCount === 1 ? 100 : minRadius + (i * step);
            orbitRadii.push(r);

            const ring = document.createElement('div');
            ring.className = 'orbit-ring';
            ring.style.width = `${r * 2}px`;
            ring.style.height = `${r * 2}px`;
            orbitsContainer.appendChild(ring);
        }

        document.getElementById('orbitCountDisplay').textContent = `Órbitas: ${currentOrbitCount} / ${maxOrbits}`;
    }

    function renderPlacedPlanets() {
        const oldPlanets = solarSystem.querySelectorAll('.placed-planet');
        oldPlanets.forEach(el => el.remove());

        const center = solarSystem.clientWidth / 2;

        placedPlanets.forEach(item => {
            const r = orbitRadii[item.orbitIndex];
            if (!r || !item.planeta) return;

            const x = center + r * Math.cos(item.angle);
            const y = center + r * Math.sin(item.angle);
            const dominante = obtenerDominante(item.planeta);
            const color = getColorDominante(dominante);
            const size = item.planeta.radio || 30;

            const planetEl = document.createElement('div');
            planetEl.className = 'placed-planet';
            planetEl.style.width = `${size}px`;
            planetEl.style.height = `${size}px`;
            planetEl.style.left = `${x}px`;
            planetEl.style.top = `${y}px`;
            planetEl.style.background = `radial-gradient(circle at 35% 35%, #ffffff, ${color} 40%, #070a22)`;
            planetEl.style.boxShadow = `0 0 12px ${color}`;
            planetEl.title = 'Doble clic para quitar de la órbita';

            planetEl.innerHTML = `<span class="placed-planet-label">${item.planeta.nombre}</span>`;

            planetEl.addEventListener('dblclick', () => {
                placedPlanets = placedPlanets.filter(p => p.instanceId !== item.instanceId);
                renderPlacedPlanets();
            });

            solarSystem.appendChild(planetEl);
        });
    }

    function updateAnimation() {
        if (isPlaying) {
            placedPlanets.forEach(item => {
                item.angle += item.speed;
            });
            renderPlacedPlanets();
        }
        requestAnimationFrame(updateAnimation);
    }

    // Controles de órbitas
    document.getElementById('btnAddOrbit')?.addEventListener('click', () => {
        if (currentOrbitCount < maxOrbits) {
            currentOrbitCount++;
            updateOrbitRings();
            renderPlacedPlanets();
        }
    });

    document.getElementById('btnRemoveOrbit')?.addEventListener('click', () => {
        if (currentOrbitCount > 1) {
            currentOrbitCount--;
            placedPlanets = placedPlanets.filter(p => p.orbitIndex < currentOrbitCount);
            updateOrbitRings();
            renderPlacedPlanets();
        }
    });

    document.getElementById('btnClearSystem')?.addEventListener('click', () => {
        placedPlanets = [];
        renderPlacedPlanets();
    });

    // Botón "+ Nueva" en el panel lateral derecho
    document.getElementById('btnNewMelody')?.addEventListener('click', () => {
        melodiaActivaId = null;
        placedPlanets = [];
        const nameInput = document.getElementById('melodyNameInput');
        if (nameInput) {
            nameInput.value = '';
            nameInput.focus();
        }
        renderPlacedPlanets();
        renderMelodySidebar();
    });

    // 3. Guardar o actualizar melodía en la Base de Datos
    document.getElementById('btnSaveSystem')?.addEventListener('click', async () => {
        if (usandoPlanetasPrueba) {
            alert('Estás usando planetas de prueba. Crea tus propios planetas en el laboratorio (Juego 1) para poder guardar tus melodías.');
            return;
        }

        const nameInput = document.getElementById('melodyNameInput');
        const nombreMelodia = nameInput?.value.trim() || '';

        if (!nombreMelodia) {
            alert('Escribe un nombre para tu melodía antes de guardar.');
            nameInput?.focus();
            return;
        }

        if (placedPlanets.length === 0) {
            alert('Coloca al menos un planeta en una órbita antes de guardar la melodía.');
            return;
        }

        const btn = document.getElementById('btnSaveSystem');
        const originalText = btn.textContent;
        btn.textContent = 'Guardando...';
        btn.disabled = true;

        const payload = {
            id_melodia: melodiaActivaId,
            nombre_melodia: nombreMelodia,
            orbitas: placedPlanets.map(item => ({
                id_planeta_creado: item.planeta.id,
                numero_orbita: item.orbitIndex + 1,
                angulo: item.angle
            }))
        };

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (response.ok && data.melodia) {
                melodiaActivaId = data.melodia.id;

                const idx = melodiasBD.findIndex(m => m.id === data.melodia.id);
                if (idx >= 0) {
                    melodiasBD[idx] = data.melodia;
                } else {
                    melodiasBD.unshift(data.melodia);
                }

                renderMelodySidebar();
                btn.textContent = '¡Guardado!';
                setTimeout(() => { btn.textContent = originalText; }, 1500);
            } else {
                alert('Error al guardar: ' + (data.message || 'Revisa los datos'));
                btn.textContent = originalText;
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión con el servidor');
            btn.textContent = originalText;
        } finally {
            btn.disabled = false;
        }
    });

    // Drag & Drop
    solarSystem.addEventListener('dragover', (e) => e.preventDefault());

    solarSystem.addEventListener('drop', (e) => {
        e.preventDefault();
        const rawId = e.dataTransfer.getData('text/plain');
        const planeta = planetasDisponibles.find(p => String(p.id) === String(rawId));

        if (!planeta) return;

        const rect = solarSystem.getBoundingClientRect();
        const center = rect.width / 2;
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        const dx = clickX - center;
        const dy = clickY - center;
        const dist = Math.sqrt(dx * dx + dy * dy);

        let closestOrbit = 0;
        let minDiff = Math.abs(dist - orbitRadii[0]);

        for (let i = 1; i < orbitRadii.length; i++) {
            const diff = Math.abs(dist - orbitRadii[i]);
            if (diff < minDiff) {
                minDiff = diff;
                closestOrbit = i;
            }
        }

        const angle = Math.atan2(dy, dx);
        const speed = 0.018 / (closestOrbit + 1);

        placedPlanets.push({
            instanceId: ++instanceCounter,
            orbitIndex: closestOrbit,
            angle: angle,
            speed: speed,
            planeta: planeta
        });

        renderPlacedPlanets();
        const ocarinaData = getAcordOcarinaPlaneta(planeta);
        tocarOcarinaSound(ocarinaData.freqs, 0.65);
    });

    // Botón Sol (Play / Pausa)
    const sunButton = document.getElementById('sun-center');
    const playIcon = document.getElementById('playIcon');
    const playText = document.getElementById('playText');

    sunButton?.addEventListener('click', () => {
        if (placedPlanets.length === 0) {
            alert('Arrastra primero un planeta al sistema solar.');
            return;
        }

        isPlaying = !isPlaying;

        if (isPlaying) {
            playIcon.textContent = '⏸';
            playText.textContent = 'PAUSA';
            sunButton.classList.add('playing');

            const sorted = [...placedPlanets].sort((a, b) => a.orbitIndex - b.orbitIndex);
            sorted.forEach((item, index) => {
                setTimeout(() => {
                    if (isPlaying) {
                        const data = getAcordOcarinaPlaneta(item.planeta);
                        tocarOcarinaSound(data.freqs, 0.65);
                    }
                }, index * 650);
            });
        } else {
            playIcon.textContent = '▶';
            playText.textContent = 'PLAY';
            sunButton.classList.remove('playing');
        }
    });

    // Renderizar barra lateral izquierda con los planetas del usuario desde la BD
    function renderSidebar() {
        const listContainer = document.getElementById('planet-list');
        if (!listContainer) return;

        listContainer.innerHTML = '';

        if (planetasDisponibles.length === 0) {
            listContainer.innerHTML = `<p class="text-xs text-[#93a0bd] italic">No tienes planetas creados aún.</p>`;
            return;
        }

        planetasDisponibles.forEach(planeta => {
            const card = document.createElement('div');
            card.className = 'ocarina-planet-card flex items-center justify-between gap-3';
            card.setAttribute('draggable', 'true');
            card.dataset.id = String(planeta.id);

            const dominante = obtenerDominante(planeta);
            const colorDominante = getColorDominante(dominante);
            const ocarinaData = getAcordOcarinaPlaneta(planeta);

            card.innerHTML = `
                <div class="ocarina-preview-sphere" style="--glow-color: ${colorDominante};"></div>
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-0.5">
                        <strong class="font-display text-[#d3dbea] text-xs sm:text-sm">${planeta.nombre}</strong>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase border" style="color: ${colorDominante}; border-color: ${colorDominante}; background-color: ${colorDominante}20;">
                            ${dominante}
                        </span>
                    </div>
                    <div class="text-[10px] font-semibold text-[#6f9bff]">Nota: ${ocarinaData.noteLabel}</div>
                </div>
                <button class="btn-play-card w-6 h-6 rounded-full bg-[#6f9bff] text-[#070a22] flex items-center justify-center text-[10px] font-bold hover:bg-white transition-colors cursor-pointer" type="button" title="Escuchar nota">▶</button>
            `;

            card.querySelector('.btn-play-card').addEventListener('click', (e) => {
                e.stopPropagation();
                tocarOcarinaSound(ocarinaData.freqs, 0.65);
            });

            card.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', String(planeta.id));
            });

            listContainer.appendChild(card);
        });
    }

    // Renderizar barra lateral derecha con las melodías guardadas (ID + Nombre)
    function renderMelodySidebar() {
        const melodyList = document.getElementById('melody-list');
        if (!melodyList) return;

        melodyList.innerHTML = '';

        if (melodiasBD.length === 0) {
            melodyList.innerHTML = `<p class="text-xs text-[#93a0bd] italic">No tienes melodías guardadas aún.</p>`;
            return;
        }

        melodiasBD.forEach(melodia => {
            const isSelected = melodia.id === melodiaActivaId;
            const totalPlanetas = melodia.orbitas ? melodia.orbitas.length : 0;

            const card = document.createElement('div');
            card.className = `ocarina-planet-card flex items-center justify-between gap-2 !cursor-pointer ${isSelected ? '!border-[#8fcf8a] !bg-[#8fcf8a]/10' : ''}`;
            card.title = 'Haz clic para cargar esta melodía en el sistema solar';

            card.innerHTML = `
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 mb-0.5">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#6f9bff]/20 text-[#6f9bff] border border-[#6f9bff]/40">
                            #${melodia.id}
                        </span>
                        <strong class="font-display text-[#d3dbea] text-xs sm:text-sm truncate">${melodia.nombre}</strong>
                    </div>
                    <div class="text-[10px] text-[#93a0bd]">${totalPlanetas} planeta(s) en órbita</div>
                </div>
                <button class="w-6 h-6 rounded-full bg-[#8fcf8a] text-[#070a22] flex items-center justify-center text-[10px] font-bold hover:bg-white transition-colors cursor-pointer shrink-0" type="button" title="Cargar melodía">
                    ▶
                </button>
            `;

            card.addEventListener('click', () => {
                cargarMelodia(melodia);
            });

            melodyList.appendChild(card);
        });
    }

    // Inicialización: si tiene melodías guardadas, carga la más reciente por defecto
    if (melodiasBD.length > 0) {
        cargarMelodia(melodiasBD[0]);
    } else {
        updateOrbitRings();
        renderPlacedPlanets();
        renderMelodySidebar();
    }

    renderSidebar();
    requestAnimationFrame(updateAnimation);

    window.addEventListener('resize', () => {
        updateOrbitRings();
        renderPlacedPlanets();
    });
});


/////sesions/////
let sesionActualId = null;
let intentosJuego = 0;
let erroresJuego = 0;
let ayudasJuego = 0;
let heartbeatInterval = null;

// 1. Iniciar sesión al abrir el juego
async function iniciarSesionJuego() {
    if (!window.SESION_CONFIG) return;

    try {
        const response = await fetch(window.SESION_CONFIG.urlIniciar, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.SESION_CONFIG.csrfToken
            },
            body: JSON.stringify({
                id_juego: window.SESION_CONFIG.idJuego
                // id_nivel: null (opcional si el juego no tiene niveles)
            })
        });

        if (response.ok) {
            const data = await response.json();
            sesionActualId = data.id;

            // Enviar aviso de actividad cada 60s para actualizar duracion_segundos
            // y evitar el timeout de 10 minutos de SesionJuego
            heartbeatInterval = setInterval(() => {
                actualizarSesion(false);
            }, 60000);
        }
    } catch (error) {
        console.error('Error al iniciar la sesión del juego:', error);
    }
}

// 2. Actualizar actividad o finalizar sesión
function actualizarSesion(completada = false, puntuacion = null, useBeacon = false) {
    if (!sesionActualId || !window.SESION_CONFIG) return;

    const url = `${window.SESION_CONFIG.urlFinalizarBase}/${sesionActualId}/finalizar`;
    const payload = {
        intentos: intentosJuego,
        errores: erroresJuego,
        ayudas: ayudasJuego,
        completada: completada
    };

    if (puntuacion !== null) {
        payload.puntuacion = puntuacion;
    }

    // Si el usuario cierra la pestaña o navega fuera, sendBeacon garantiza el envío
    if (useBeacon && navigator.sendBeacon) {
        const formData = new FormData();
        formData.append('_token', window.SESION_CONFIG.csrfToken);
        Object.entries(payload).forEach(([key, value]) => {
            if (value !== null) {
                formData.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value);
            }
        });
        navigator.sendBeacon(url, formData);
        return;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.SESION_CONFIG.csrfToken
        },
        body: JSON.stringify(payload)
    }).catch(err => console.error('Error actualizando sesión:', err));
}

// Arrancar la sesión al cargar el DOM
document.addEventListener('DOMContentLoaded', iniciarSesionJuego);

// Guardar progreso de tiempo al cerrar o cambiar de página
window.addEventListener('beforeunload', () => {
    clearInterval(heartbeatInterval);
    actualizarSesion(false, null, true);
});