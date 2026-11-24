<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Astral Recipe</title>
    @vite(['resources/css/astral_recipe.css', 'resources/js/astral_recipe.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nodo-zodiaco-bg-r.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500&family=Marcellus&display=swap"
        rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-slate-100">
    <header class="relative z-20 shrink-0 bg-transparent">
        <x-header />
    </header>
    <canvas id="stars" class="fixed inset-0 -z-10 pointer-events-none"></canvas>

    <!-- Header -->
    <header class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col lg:flex-row items-center gap-6">

            <h1 class="font-title text-4xl text-white">
                ✦ Astral Recipe
            </h1>

            <!-- Niveles de BDD -->
            <nav class="flex gap-6 text-sm text-slate-400">
                @foreach ($niveles as $nivel)
                    <div class="nivel-item relative">
                        <a href="?nivel={{ $nivel->id }}"
                            class="hover:text-white transition {{ $nivelActualId == $nivel->id ? 'text-white font-semibold' : '' }}">
                            {{ $nivel->nombre }}
                        </a>

                        <!-- Tooltip en fer hover -->
                        <div
                            class="nivel-tooltip pointer-events-none absolute left-1/2 top-full z-50 mt-2.5 w-52 -translate-x-1/2 rounded-xl border border-indigo-400/30 bg-slate-950/95 px-3 py-2 text-center text-xs text-slate-200 shadow-[0_0_20px_rgba(0,0,0,0.6)]">
                            {{ $nivel->descripcion }}

                            <!-- Petita fletxa superior del tooltip -->
                            <div
                                class="absolute -top-1 left-1/2 size-2 -translate-x-1/2 rotate-45 border-l border-t border-indigo-400/30 bg-slate-950">
                            </div>
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="lg:ml-auto flex items-center gap-4">
                <span class="text-sm text-slate-400">
                    Puntuación:
                    <span id="score" class="text-white font-semibold">0</span>
                </span>

                <button id="open-guia" type="button"
                    class="px-4 py-2 rounded-full border border-slate-600 hover:bg-slate-800 transition">
                    Guia
                </button>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="max-w-7xl mx-auto px-6 pb-12">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Zona Joc -->
            <section class="flex flex-col items-center px-14">
                <div class="relative aspect-square w-full max-w-120 rounded-full border border-indigo-300/20">

                    <!-- Rombe inscrit al cercle -->
                    <svg class="pointer-events-none absolute inset-0 size-full overflow-visible" viewBox="0 0 100 100"
                        aria-hidden="true">
                        <polygon points="50,0 100,50 50,100 0,50" class="fill-none stroke-indigo-300/20"
                            stroke-width="1" vector-effect="non-scaling-stroke" />
                    </svg>

                    <!-- Punt que gira pel cercle gran -->
                    <div
                        class="pointer-events-none absolute inset-0 animate-[spin_24s_linear_infinite] motion-reduce:animate-none">
                        <span
                            class="absolute left-1/2 top-0 size-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-300 shadow-[0_0_12px_#a5b4fc]"></span>
                    </div>

                    <!-- Element (esquerra) -->
                    <div id="node-element"
                        class="absolute left-0 top-1/2 z-10 flex aspect-square w-[23%] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-indigo-200 bg-slate-950/85 shadow-[0_0_26px_rgba(199,210,254,0.45)]">
                        <img id="img-element" src="" alt="Elemento" class="size-[62%] object-contain">

                        <div
                            class="pointer-events-none absolute -inset-3.5 animate-[spin_6s_linear_infinite] motion-reduce:animate-none">
                            <span
                                class="absolute left-1/2 top-0 size-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-200 shadow-[0_0_12px_#c7d2fe]"></span>
                        </div>
                    </div>

                    <!-- Zona de transmutació -->
                    <div id="drop-zone"
                        class="absolute left-1/2 top-1/2 z-1 flex aspect-square w-2/5 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-white/40 bg-[radial-gradient(circle,rgba(255,255,255,0.14),rgba(255,255,255,0.03)_70%)] text-center text-[15px] leading-tight text-indigo-300/80 transition">
                        <span id="drop-zone-text">Zona de tu receta</span>
                    </div>

                    <!-- Metall (dreta) -->
                    <div id="node-metal"
                        class="absolute left-full top-1/2 z-10 flex aspect-square w-[23%] -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-indigo-200 bg-slate-950/85 shadow-[0_0_26px_rgba(199,210,254,0.45)]">
                        <span id="symbol-metal"
                            class="text-[clamp(2rem,7vw,3.2rem)] leading-none text-indigo-100">?</span>

                        <div
                            class="pointer-events-none absolute -inset-3.5 animate-[spin_8s_linear_infinite] [animation-direction:reverse] motion-reduce:animate-none">
                            <span
                                class="absolute left-1/2 top-0 size-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-indigo-200 shadow-[0_0_12px_#c7d2fe]"></span>
                        </div>
                    </div>
                </div>

                <p class="mt-8 text-sm text-indigo-300/70">Arrastra una carta al círculo central</p>
            </section>

            <!-- Informació -->
            <section>

                <!-- Títol recepta-->
                <h2 id="receta-titulo" class="font-title text-5xl mb-4">
                    Carregant...
                </h2>

                <!-- Descripció -->
                <p id="receta-descripcion" class="text-lg text-slate-300 mb-2">
                    Carregant...
                </p>

                <!-- Guia basica permanent -->
                <p class="text-indigo-300 text-sm mb-6">
                    Haz coincidir el color i el metal con una carta.
                </p>
            </section>

        </div>

        <!-- Cartes -->
        <section class="mt-16">

            <h3 class="text-center uppercase tracking-[0.3em] text-slate-400 text-sm mb-8">
                Tus cartas
            </h3>

            <div id="cards-container" class="flex flex-wrap justify-center gap-6">
            </div>
        </section>
    </main>
    <!-- Modal Guia -->
    @include('games.astral_recipe.modalGuiaAstral')

    <script>
        window.GAME_DATA = {
            recetas: @json($recetas),
            signos: @json($signos),
            assetUrl: "{{ asset('') }}",
            nivelActualId: Number({{ $nivelActualId }})
        };
    </script>
</body>

</html>
