<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocarina Celestial | Harmonia Alchymica</title>
    
    <!-- Fuentes Marcellus y Jost -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&family=Marcellus&display=swap" rel="stylesheet">

    <!-- Carga de CSS app y de juegos -->
    @vite(['resources/css/app.css', 'resources/css/musical.css', 'resources/js/app.js'])
</head>
<body class="ocarina-game-viewport text-[#e6ebf7] font-sans bg-transparent">

    <!-- Header reutilizable con transparencia -->
    <header class="relative z-20 shrink-0 bg-transparent">
        <x-header />
    </header>

    <!-- Canvas de fondo fijado al fondo de la pantalla -->
    <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>

    <main class="ocarina-main-layout relative z-10 bg-transparent">
            
        <!-- PANEL LATERAL DE PLANETAS -->
        <aside class="ocarina-sidebar">
            <div class="flex items-center justify-between border-b border-[#93a0bd]/20 pb-2">
                <span class="font-display text-xs uppercase tracking-wider text-[#6f9bff]">Planetas de Prueba</span>
            </div>
            <div id="planet-list" class="flex flex-col gap-2.5"></div>
        </aside>

        <!-- ESPACIO DE TRABAJO -->
        <section class="ocarina-workspace" id="workspace">
                
            <!-- Barra de controles -->
            <div class="ocarina-controls">
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#6f9bff] text-white  hover:text-[#070a22] transition-all cursor-pointer" id="btnAddOrbit">+ Órbita</button>
                <span class="text-xs font-semibold text-[#6f9bff] px-2 font-display" id="orbitCountDisplay">Órbitas: 3 / 15</span>
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#6f9bff] text-white  hover:text-[#070a22] transition-all cursor-pointer" id="btnRemoveOrbit">- Órbita</button>
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#ff8a5c] text-[#ff8a5c] hover:text-white transition-all cursor-pointer ml-2" id="btnClearSystem">Limpiar</button>
            </div>

            <!-- CONTENEDOR DEL SISTEMA SOLAR BASADO EN HTML/DOM -->
            <div id="solarSystem" class="solar-system-dom">
                <div id="orbitsContainer" class="orbits-container"></div>
                
                <!-- Sol Central Reproductor -->
                <div id="sun-center" class="ocarina-sun-core" title="Activar movimiento y reproducir acorde celestial">
                    <div id="playIcon" class="text-xl text-white font-bold leading-none">▶</div>
                    <span id="playText" class="text-[8px] font-bold text-white font-display mt-0.5">PLAY</span>
                </div>
            </div>

        </section>
    </main>

    <!-- LÓGICA DEL JUEGO HTML (SISTEMA DE NODOS HTML) -->
    <script>
        const planetesPrototip = [
            { "id": 1761650000001, "nombre": "Zephyrus", "habitabilidad": 80, "dominante": "aire", "radio": 28 },
            { "id": 1761650000002, "nombre": "Ignis", "habitabilidad": 85, "dominante": "fuego", "radio": 32 },
            { "id": 1761650000003, "nombre": "Aquaria", "habitabilidad": 82, "dominante": "agua", "radio": 30 },
            { "id": 1761650000004, "nombre": "Terra", "habitabilidad": 90, "dominante": "tierra", "radio": 34 }
        ];

        function getColorDominante(dominante) {
            switch(dominante) {
                case "fuego": return "#ff8a5c";
                case "agua": return "#4cc9f0";
                case "tierra": return "#8fcf8a";
                case "aire": return "#c0a9ff";
                default: return "#93a0bd";
            }
        }

        const OCARINA_NOTES = {
            "C-Down": 293.66,
            "A":      349.23,
            "C-Left": 440.00,
            "C-Right": 493.88,
            "C-Up":   587.33
        };

        let audioCtx = null;
        let isPlaying = false;

        function initAudio() {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
        }

        function getAcordOcarinaPlaneta(planeta) {
            let baseFreq = OCARINA_NOTES["C-Down"];
            let noteName = "C-Down (Re4)";

            if (planeta.dominante === "fuego") { baseFreq = OCARINA_NOTES["C-Up"]; noteName = "C-Up (Re5)"; }
            else if (planeta.dominante === "agua") { baseFreq = OCARINA_NOTES["C-Right"]; noteName = "C-Right (Si4)"; }
            else if (planeta.dominante === "tierra") { baseFreq = OCARINA_NOTES["A"]; noteName = "A (Fa4)"; }
            else if (planeta.dominante === "aire") { baseFreq = OCARINA_NOTES["C-Left"]; noteName = "C-Left (La4)"; }

            let isMinor = planeta.habitabilidad < 40;
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

        const solarSystem = document.getElementById('solarSystem');
        const orbitsContainer = document.getElementById('orbitsContainer');

        let maxOrbits = 15;
        let currentOrbitCount = 3;
        let orbitRadii = [];
        let placedPlanets = [];
        let instanceCounter = 0;

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

                // Crear anillo de órbita en el DOM
                const ring = document.createElement('div');
                ring.className = 'orbit-ring';
                ring.style.width = `${r * 2}px`;
                ring.style.height = `${r * 2}px`;
                orbitsContainer.appendChild(ring);
            }

            document.getElementById('orbitCountDisplay').textContent = `Órbitas: ${currentOrbitCount} / ${maxOrbits}`;
        }

        function renderPlacedPlanets() {
            // Eliminar planetas antiguos del DOM
            const oldPlanets = solarSystem.querySelectorAll('.placed-planet');
            oldPlanets.forEach(el => el.remove());

            const center = solarSystem.clientWidth / 2;

            placedPlanets.forEach(item => {
                const r = orbitRadii[item.orbitIndex];
                if (!r) return;

                const x = center + r * Math.cos(item.angle);
                const y = center + r * Math.sin(item.angle);
                const color = getColorDominante(item.planeta.dominante);
                const size = item.planeta.radio || 30;

                // Elemento HTML del Planeta
                const planetEl = document.createElement('div');
                planetEl.className = 'placed-planet';
                planetEl.style.width = `${size}px`;
                planetEl.style.height = `${size}px`;
                planetEl.style.left = `${x}px`;
                planetEl.style.top = `${y}px`;
                planetEl.style.background = `radial-gradient(circle at 35% 35%, #ffffff, ${color} 40%, #070a22)`;
                planetEl.style.boxShadow = `0 0 12px ${color}`;

                planetEl.innerHTML = `<span class="placed-planet-label">${item.planeta.nombre}</span>`;
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

        document.getElementById('btnAddOrbit').addEventListener('click', () => {
            if (currentOrbitCount < maxOrbits) {
                currentOrbitCount++;
                updateOrbitRings();
                renderPlacedPlanets();
            }
        });

        document.getElementById('btnRemoveOrbit').addEventListener('click', () => {
            if (currentOrbitCount > 1) {
                currentOrbitCount--;
                placedPlanets = placedPlanets.filter(p => p.orbitIndex < currentOrbitCount);
                updateOrbitRings();
                renderPlacedPlanets();
            }
        });

        document.getElementById('btnClearSystem').addEventListener('click', () => {
            placedPlanets = [];
            renderPlacedPlanets();
        });

        solarSystem.addEventListener('dragover', (e) => e.preventDefault());

        solarSystem.addEventListener('drop', (e) => {
            e.preventDefault();
            const rawId = e.dataTransfer.getData('text/plain');
            const planeta = planetesPrototip.find(p => String(p.id) === String(rawId));

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

        const sunButton = document.getElementById('sun-center');
        const playIcon = document.getElementById('playIcon');
        const playText = document.getElementById('playText');

        sunButton.addEventListener('click', () => {
            if (placedPlanets.length === 0) {
                alert("Arrastra primero un planeta al sistema solar.");
                return;
            }

            isPlaying = !isPlaying;

            if (isPlaying) {
                playIcon.textContent = "⏸";
                playText.textContent = "PAUSA";
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
                playIcon.textContent = "▶";
                playText.textContent = "PLAY";
                sunButton.classList.remove('playing');
            }
        });

        function renderSidebar() {
            const listContainer = document.getElementById('planet-list');
            if (!listContainer) return;

            listContainer.innerHTML = '';

            planetesPrototip.forEach(planeta => {
                const card = document.createElement('div');
                card.className = 'ocarina-planet-card flex items-center justify-between gap-3';
                card.setAttribute('draggable', 'true');
                card.dataset.id = String(planeta.id);

                const colorDominante = getColorDominante(planeta.dominante);
                const ocarinaData = getAcordOcarinaPlaneta(planeta);

                card.innerHTML = `
                    <div class="ocarina-preview-sphere" style="--glow-color: ${colorDominante};"></div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-0.5">
                            <strong class="font-display text-[#d3dbea] text-xs sm:text-sm">${planeta.nombre}</strong>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase border" style="color: ${colorDominante}; border-color: ${colorDominante}; background-color: ${colorDominante}20;">
                                ${planeta.dominante}
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

        document.addEventListener('DOMContentLoaded', () => {
            updateOrbitRings();
            renderSidebar();
            requestAnimationFrame(updateAnimation);
        });

        window.addEventListener('resize', () => {
            updateOrbitRings();
            renderPlacedPlanets();
        });
    </script>
</body>
</html>