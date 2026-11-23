<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmonia Alchymica | Proyecto DAW</title>

    <!-- Fuentes externas (Cinzel) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Carga de Vite en Laravel -->
    @vite(['resources/css/app.css', 'resources/js/app.js']);
</head>

<body
    class="relative min-h-screen flex flex-col justify-between text-slate-200 font-sans bg-slate-950 overflow-x-hidden">

    <!-- ================= NAVEGACIÓN HEADER ================= -->
    <header class="relative z-10">
        <x-header />
    </header>

    <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>

    <!-- ================= SECCIÓN PRINCIPAL MAIN ================= -->
    <main class="relative z-10 grow flex flex-col items-center justify-center px-4 py-8 max-w-6xl mx-auto w-full">
        @if (session('bloqueado'))
            <p class="mb-4 px-4 py-2 rounded-lg border border-amber-400/60 bg-amber-500/10 text-amber-200 text-xs">{{ session('bloqueado') }}</p>
        @endif

        <!-- 1. CONTENEDOR TITULAR (Arriba) -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-5xl font-bold font-alchemy text-slate-100 tracking-widest mb-2 drop-shadow-lg">
                CÍRCULO DE TRANSMUTACIÓN
            </h1>
            <p class="text-xs md:text-sm text-slate-300 max-w-md mx-auto drop-shadow">
                Haz clic en cualquiera de los nodos del emblema para seleccionar el modo de juego.
            </p>
        </div>

        <!-- 2. CONTENEDOR INTERACTIVO (REGLA EN GRID: FUERZA 2 COLUMNAS HORIZONTALES) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; width: 100%; max-width: 900px; align-items: center; justify-items: center;"
            class="mb-12">

            <!-- COLUMNA A: EMBLEMA EN ROMBO -->
            <div id="emblema-rombo"
                style="position: relative; width: 320px; height: 320px; flex-shrink: 0; user-select: none;">

                <!-- SVG LÍNEAS NEÓN EN ROMBO -->
                <svg style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; overflow: visible;"
                    viewBox="0 0 100 100">
                    <defs>
                        <filter id="neon-glow" x="-50%" y="-50%" width="200%" height="200%">
                            <feGaussianBlur stdDeviation="1.5" result="blur1" />
                            <feGaussianBlur stdDeviation="3" result="blur2" />
                            <feMerge>
                                <feMergeNode in="blur2" />
                                <feMergeNode in="blur1" />
                                <feMergeNode in="SourceGraphic" />
                            </feMerge>
                        </filter>
                    </defs>

                    <polygon points="50,18 82,50 50,82 18,50" fill="none" stroke="#3b82f6" stroke-width="1.5"
                        filter="url(#neon-glow)" />

                    <line x1="50" y1="18" x2="50" y2="82" stroke="#60a5fa"
                        stroke-width="0.8" opacity="0.6" stroke-dasharray="2 2" filter="url(#neon-glow)" />
                    <line x1="18" y1="50" x2="82" y2="50" stroke="#60a5fa"
                        stroke-width="0.8" opacity="0.6" stroke-dasharray="2 2" filter="url(#neon-glow)" />
                </svg>

                <!-- NODO CENTRO -->
                <div
                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80px; height: 80px; z-index: 10;">
                    <div
                        class="w-full h-full rounded-full bg-slate-900 border-2 border-blue-400 shadow-[0_0_20px_rgba(59,130,246,0.8)] flex items-center justify-center overflow-hidden relative">
                        <img src="{{ asset('images/nodo-central-bg-r.png') }}" alt="Centro"
                            onerror="this.style.display='none'" class="w-full h-full object-contain">
                    </div>
                    <button onclick="mostrarJuego(event, 'centro')"
                        class="absolute inset-0 rounded-full opacity-0 hover:opacity-100 border-2 border-blue-300 bg-blue-400/20 shadow-[0_0_25px_rgba(147,197,253,0.9)] transition-all duration-300 cursor-pointer z-20"
                        title="Inspeccionar Centro">
                    </button>
                </div>

                <!-- NODO SUPERIOR (ALQUIMIA) -->
                <div
                    style="position: absolute; top: 18%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; z-index: 10;">
                    <div
                        class="w-full h-full rounded-full bg-slate-900 border-2 border-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.7)] flex items-center justify-center overflow-hidden relative">
                        <img src="{{ asset('images/nodo-alquimia-bg-r.png') }}" alt="Alquimia"
                            onerror="this.style.display='none'" class="w-full h-full object-contain">
                    </div>
                    <button onclick="mostrarJuego(event, 'alquimia')"
                        class="absolute inset-0 rounded-full opacity-0 hover:opacity-100 border-2 border-amber-300 bg-amber-500/30 shadow-[0_0_20px_rgba(245,158,11,0.9)] transition-all duration-300 cursor-pointer z-20"
                        title="Modo Alquimia">
                    </button>
                </div>

                <!-- NODO IZQUIERDO (ZODIACO) -->
                <div
                    style="position: absolute; top: 50%; left: 18%; transform: translate(-50%, -50%); width: 60px; height: 60px; z-index: 10;">
                    <div
                        class="w-full h-full rounded-full bg-slate-900 border-2 border-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.7)] flex items-center justify-center overflow-hidden relative">
                        <img src="{{ asset('images/nodo-zodiaco-bg-r.png') }}" alt="Zodiaco"
                            onerror="this.style.display='none'" class="w-full h-full object-contain">
                    </div>
                    <button onclick="mostrarJuego(event, 'zodiaco')"
                        class="absolute inset-0 rounded-full opacity-0 hover:opacity-100 border-2 border-purple-300 bg-purple-500/30 shadow-[0_0_20px_rgba(168,85,247,0.9)] transition-all duration-300 cursor-pointer z-20"
                        title="Modo Zodiaco">
                    </button>
                </div>

                <!-- NODO DERECHO (MÚSICA) -->
                <div
                    style="position: absolute; top: 50%; left: 82%; transform: translate(-50%, -50%); width: 60px; height: 60px; z-index: 10;">
                    <div
                        class="w-full h-full rounded-full bg-slate-900 border-2 border-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.7)] flex items-center justify-center overflow-hidden relative">
                        <img src="{{ asset('images/nodo-musical-bg-r.png') }}" alt="Música"
                            onerror="this.style.display='none'" class="w-full h-full object-contain">
                    </div>
                    <button onclick="mostrarJuego(event, 'musica')"
                        class="absolute inset-0 rounded-full opacity-0 hover:opacity-100 border-2 border-cyan-300 bg-cyan-500/30 shadow-[0_0_20px_rgba(6,182,212,0.9)] transition-all duration-300 cursor-pointer z-20"
                        title="Modo Música">
                    </button>
                </div>

                <!-- NODO INFERIOR (QUINTO ELEMENTO) -->
                <div style="position: absolute; top: 82%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; z-index: 10;">
                    <div class="w-full h-full rounded-full bg-slate-900 border-2 border-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.7)] flex items-center justify-center overflow-hidden relative">
                        <img src="{{ asset('images/nodo-alquimia-bg-r.png') }}" alt="Elemento" onerror="this.style.display='none'" class="w-full h-full object-contain">
                    </div>
                    <button onclick="mostrarJuego(event, 'constelacion')" 
                            class="absolute inset-0 rounded-full opacity-0 hover:opacity-100 border-2 border-emerald-300 bg-emerald-500/30 shadow-[0_0_20px_rgba(16,185,129,0.9)] transition-all duration-300 cursor-pointer z-20"
                            title="Modo Elemento Inferior">
                    </button>
                </div>

            </div>

            <!-- COLUMNA B: TARJETA DE INFORMACIÓN (Aparece a la derecha) -->
            <div id="panel-info" style="display: none; width: 100%; max-width: 360px;"
                class="p-5 rounded-2xl bg-slate-900/90 border border-slate-700/60 backdrop-blur-md shadow-2xl text-left flex-col justify-between">
                <div>
                    <span id="game-badge"
                        class="text-[10px] uppercase tracking-widest font-semibold text-amber-400"></span>
                    <h3 id="game-title" class="text-lg font-bold font-alchemy text-slate-100 my-1"></h3>
                    <p id="game-desc" class="text-xs text-slate-300 leading-relaxed mb-4"></p>
                </div>

                <div class="pt-3 border-t border-slate-800">
                    <a id="game-button" href="#" class="w-full py-2.5 px-4 rounded-xl font-bold text-center text-xs bg-blue-300 hover:bg-blue-400  transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg">
                        <span id="game-button-text">INICIAR JUEGO</span>
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="relative z-10">
        <x-footer />
    </footer>

    <!-- ================= LÓGICA DE INTERACCIÓN ================= -->
    <script>
        const juegos = {
            'centro': {
                badge: 'Modo Admin',
                title: 'NÚCLEO DE GESTIÓN',
                desc: 'El centro neurálgico donde convergen todas las fuerzas elementales.',
                route: '#'
            },
            'alquimia': {
                badge: 'Astronomia Elemental',
                title: 'Astralquimium',
                desc: 'Descubre los secretos del universo alquímico que se ocultan en nuestro sistema solar y crea tus nuevos planetas.',
                route: '{{ route("game.astralquimium.index") }}'
            },
            'zodiaco': {
                badge: 'Astrofísica Mística',
                title: 'Astral Recipe',
                desc: 'Crea diferentes recetas combinando signos astrológicos, elementos quimicos y alquímicos.',
                route: '{{ route("game.astral") }}',
                locked: @json($bloqueado[3])
            },
            'musica': {
                badge: 'Armonía Espacial',
                title: 'Orbital Music',
                desc: 'Ubica los planetas en sus orbitas y descubre sus frecuencias armónicas.',
                route: '{{ route("game.musica") }}',
                locked: @json($bloqueado[2])
            },
            'constelacion': {
                badge: 'Espíritus del Cosmos',
                title: 'Constelaciones',
                desc: 'Descubre las conexiones místicas entre los astros y sus energías.',
                route: '{{ route("game.constelaciones") }}',
                locked: @json($bloqueado[4])
            }
        };

        function mostrarJuego(event, key) {
            // Evita que el evento se extienda al listener global
            event.stopPropagation();

            const panel = document.getElementById('panel-info');
            const info = juegos[key];

            // Inyectar textos
            document.getElementById('game-badge').innerText = info.badge;
            document.getElementById('game-title').innerText = info.title;
            document.getElementById('game-desc').innerText = info.desc;
            const boton = document.getElementById('game-button');
            const textoBoton = document.getElementById('game-button-text');
            const bloqueado = info.locked === true;
            boton.href = bloqueado ? '#' : info.route;
            boton.classList.toggle('opacity-50', bloqueado);
            boton.classList.toggle('pointer-events-none', bloqueado);
            boton.setAttribute('aria-disabled', bloqueado ? 'true' : 'false');
            textoBoton.innerText = bloqueado ? 'BLOQUEADO · COMPLETA EL JUEGO Astralquimium PARA DESBLOQUEAR' : 'INICIAR JUEGO';

            // Abrir panel lateral (style inline asegura que flex se active)
            panel.style.display = 'flex';
        }

        // Clic fuera para ocultar la tarjeta
        document.addEventListener('click', function(event) {
            const panel = document.getElementById('panel-info');
            const emblema = document.getElementById('emblema-rombo');

            if (panel && emblema) {
                if (!panel.contains(event.target) && !emblema.contains(event.target)) {
                    panel.style.display = 'none';
                }
            }
        });
    </script>
</body>

</html>
