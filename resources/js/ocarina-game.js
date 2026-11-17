document.addEventListener('DOMContentLoaded', () => {
    const solarSystem = document.getElementById('solarSystem');
    const orbitsContainer = document.getElementById('orbitsContainer');
    if (!solarSystem || !orbitsContainer) return;

    // 1. Datos inyectados desde Laravel (BD)
    const {
        planetasDisponibles: planetasBD = [],
        orbitasGuardadas = [],
        saveUrl = '',
        csrfToken = ''
    } = window.OCARINA_DATA || {};

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
    let isPlaying = false;

    function initAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }

    function getAcordOcarinaPlaneta(planeta) {
        const dominante = obtenerDominante(planeta);
        let baseFreq = OCARINA_NOTES['C-Down'];
        let noteName = 'C-Down (Re4)';

        if (dominante === 'fuego') { baseFreq = OCARINA_NOTES['C-Up']; noteName = 'C-Up (Re5)'; }
        else if (dominante === 'agua') { baseFreq = OCARINA_NOTES['C-Right']; noteName = 'C-Right (Si4)'; }
        else if (dominante === 'tierra') { baseFreq = OCARINA_NOTES['A']; noteName = 'A (Fa4)'; }
        else if (dominante === 'aire') { baseFreq = OCARINA_NOTES['C-Left']; noteName = 'C-Left (La4)'; }

        const isMinor = planeta.habitabilidad === false || planeta.habitabilidad === 0;
        const multiplier2 = isMinor ? 1.2 : 1.25;
        const multiplier3 = 1.5;

        return {
            noteLabel: noteName,
            freqs: [baseFreq, baseFreq * multiplier2, baseFreq * multiplier3]
        };
    }

    function tocarOcarinaSound(freqsArray, durada = 1.2) {
        initAudio();
        freqsArray.forEach(freq => {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);

            gain.gain.setValueAtTime(0, audioCtx.currentTime);
            gain.gain.linearRampToValueAtTime(0.12, audioCtx.currentTime + 0.08);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + durada);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + durada);
        });
    }

    // Estado del sistema solar DOM
    const maxOrbits = 15;
    let currentOrbitCount = 3;
    let orbitRadii = [];
    let placedPlanets = [];
    let instanceCounter = 0;

    // 2. Cargar órbitas iniciales desde la Base de Datos
    function cargarOrbitasIniciales() {
        if (!orbitasGuardadas.length) return;

        // Ajustar el número de anillos si hay planetas guardados en órbitas superiores a 3
        const maxOrbitaBD = Math.max(...orbitasGuardadas.map(o => o.numero_orbita));
        if (maxOrbitaBD > currentOrbitCount) {
            currentOrbitCount = Math.min(maxOrbits, maxOrbitaBD);
        }

        placedPlanets = orbitasGuardadas.map(registro => {
            const orbitIndex = registro.numero_orbita - 1;
            return {
                instanceId: ++instanceCounter,
                orbitIndex: orbitIndex,
                angle: parseFloat(registro.angulo) || 0,
                speed: 0.018 / (orbitIndex + 1),
                planeta: registro.planeta
            };
        });
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

            // Permitir quitar un planeta concreto con doble clic
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

    // 3. Guardar en Base de Datos al pulsar el botón "Guardar"
    document.getElementById('btnSaveSystem')?.addEventListener('click', async () => {
        if (usandoPlanetasPrueba) {
            alert('Estás usando planetas de prueba. Crea tus propios planetas en el laboratorio (Juego 1) para poder guardar tus órbitas.');
            return;
        }

        const btn = document.getElementById('btnSaveSystem');
        const originalText = btn.textContent;
        btn.textContent = 'Guardando...';
        btn.disabled = true;

        const payload = placedPlanets.map(item => ({
            id_planeta_creado: item.planeta.id,
            numero_orbita: item.orbitIndex + 1,
            angulo: item.angle
        }));

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ orbitas: payload })
            });

            const data = await response.json();
            if (response.ok) {
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

    // Drag & Drop (Permite varios planetas en la misma órbita)
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
        tocarOcarinaSound(ocarinaData.freqs);
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
                        tocarOcarinaSound(data.freqs, 1.4);
                    }
                }, index * 700);
            });
        } else {
            playIcon.textContent = '▶';
            playText.textContent = 'PLAY';
            sunButton.classList.remove('playing');
        }
    });

    // Renderizar barra lateral con los planetas del usuario desde la BD
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
                <button class="btn-play-card w-6 h-6 rounded-full bg-[#6f9bff] text-[#070a22] flex items-center justify-center text-[10px] font-bold hover:bg-white transition-colors cursor-pointer" type="button" title="Escuchar acorde">▶</button>
            `;

            card.querySelector('.btn-play-card').addEventListener('click', (e) => {
                e.stopPropagation();
                tocarOcarinaSound(ocarinaData.freqs);
            });

            card.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', String(planeta.id));
            });

            listContainer.appendChild(card);
        });
    }

    // Inicialización
    cargarOrbitasIniciales();
    updateOrbitRings();
    renderPlacedPlanets();
    renderSidebar();
    requestAnimationFrame(updateAnimation);

    window.addEventListener('resize', () => {
        updateOrbitRings();
        renderPlacedPlanets();
    });
});