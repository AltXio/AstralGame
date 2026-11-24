<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocarina Celestial | Harmonia Alchymica</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&family=Marcellus&display=swap" rel="stylesheet">

    <!-- Carga de CSS y el JS separado -->
    @vite(['resources/css/app.css', 'resources/css/musical.css', 'resources/js/app.js', 'resources/js/ocarina-game.js'])
</head>
<body class="ocarina-game-viewport text-[#e6ebf7] font-sans bg-transparent">

    <header class="relative z-20 shrink-0 bg-transparent">
        <x-header />
    </header>

    <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>

    <main class="ocarina-main-layout relative z-10 bg-transparent">
            
        <!-- PANEL LATERAL IZQUIERDO: PLANETAS -->
        <aside class="ocarina-sidebar">
            <div class="flex items-center justify-between border-b border-[#93a0bd]/20 pb-2">
                <span class="font-display text-xs uppercase tracking-wider text-[#6f9bff]">Mis Planetas</span>
            </div>
            <div id="planet-list" class="flex flex-col gap-2.5"></div>
        </aside>

        <!-- ESPACIO DE TRABAJO CENTRAL -->
        <section class="ocarina-workspace" id="workspace">
                
            <!-- Barra de controles con nombre de melodía y botón Guardar -->
            <div class="ocarina-controls flex flex-wrap items-center justify-center gap-2">
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#6f9bff] text-white hover:text-[#070a22] transition-all cursor-pointer" id="btnAddOrbit">+ Órbita</button>
                <span class="text-xs font-semibold text-[#6f9bff] px-2 font-display" id="orbitCountDisplay">Órbitas: 3 / 15</span>
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#6f9bff] text-white hover:text-[#070a22] transition-all cursor-pointer" id="btnRemoveOrbit">- Órbita</button>
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#ff8a5c] text-[#ff8a5c] hover:text-white transition-all cursor-pointer" id="btnClearSystem">Limpiar</button>

                <input 
                    type="text" 
                    id="melodyNameInput" 
                    maxlength="100"
                    placeholder="Nombre de la melodía..." 
                    class="px-3 py-1 rounded-full text-xs bg-[#070a22]/80 border border-[#6f9bff]/50 text-[#e6ebf7] placeholder-[#93a0bd] focus:outline-none focus:border-[#8fcf8a]"
                >
                <button class="px-3 py-1 rounded-full text-xs font-medium border border-[#8fcf8a] bg-[#8fcf8a]/20 text-[#8fcf8a] hover:bg-[#8fcf8a] hover:text-[#070a22] transition-all cursor-pointer" id="btnSaveSystem">Guardar Melodía</button>
            </div>

            <!-- CONTENEDOR DEL SISTEMA SOLAR BASADO EN HTML/DOM -->
            <div id="solarSystem" class="solar-system-dom">
                <div id="orbitsContainer" class="orbits-container"></div>
                
                <div id="sun-center" class="ocarina-sun-core" title="Activar movimiento y reproducir acorde celestial">
                    <div id="playIcon" class="text-xl text-white font-bold leading-none">▶</div>
                    <span id="playText" class="text-[8px] font-bold text-white font-display mt-0.5">PLAY</span>
                </div>
            </div>

        </section>

        <!-- PANEL LATERAL DERECHO: MELODÍAS GUARDADAS -->
        <aside class="ocarina-sidebar" id="melody-sidebar">
            <div class="flex items-center justify-between border-b border-[#93a0bd]/20 pb-2">
                <span class="font-display text-xs uppercase tracking-wider text-[#8fcf8a]">Mis Melodías</span>
                <button type="button" id="btnNewMelody" class="text-[10px] text-[#6f9bff] hover:text-white cursor-pointer" title="Crear nueva melodía desde cero">+ Nueva</button>
            </div>
            <div id="melody-list" class="flex flex-col gap-2.5"></div>
        </aside>

    </main>

    <!-- Puente de datos desde Laravel hacia resources/js/ocarina-game.js -->
    <script>
        window.OCARINA_DATA = {
            planetasDisponibles: @json($planetasDisponibles),
            melodiasGuardadas: @json($melodiasGuardadas),
            saveUrl: "{{ route('orbitas.guardar') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
</body>
</html>