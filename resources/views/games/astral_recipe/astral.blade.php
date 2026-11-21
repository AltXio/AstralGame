<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Astral Recipe</title>
    @vite(['resources/css/astral_recipe.css', 'resources/js/astral_recipe.js'])
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500&family=Marcellus&display=swap"
        rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-slate-100">
    <canvas id="stars"></canvas>

    <!-- Header -->
    <header class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col lg:flex-row items-center gap-6">

            <h1 class="font-title text-4xl text-white">
                ✦ Astral Recipe
            </h1>

            <!-- Niveles de BDD -->
            <nav class="flex gap-6 text-sm text-slate-400">
                @foreach ($niveles as $nivel)
                    <div>
                        <button class="hover:text-white transition">{{ $nivel->nombre }}</button>
                    </div>
                @endforeach
            </nav>

            <div class="lg:ml-auto flex items-center gap-4">
                <span class="text-sm text-slate-400">
                    Puntuació:
                    <span class="text-white font-semibold">0</span>
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
            <section class="flex justify-center">

                <div
                    class="relative w-full max-w-md aspect-square rounded-full border border-indigo-500/30 bg-indigo-950/30 backdrop-blur">

                    <!-- Element esquerra -->
                    <div
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-24 h-24 rounded-full border border-red-400 flex items-center justify-center bg-red-500/10">
                        🔥
                    </div>

                    <!-- Zona drop -->
                    <div
                        class="absolute inset-0 m-auto w-40 h-40 rounded-full border-2 border-dashed border-slate-400 flex items-center justify-center text-center text-slate-400">
                        Arrossega una carta
                    </div>

                    <!-- Metall dreta -->
                    <div
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-24 h-24 rounded-full border border-blue-400 flex items-center justify-center bg-blue-500/10">
                        ☉
                    </div>
                </div>
            </section>

            <!-- Informació -->
            <section>

                <h2 class="font-title text-5xl mb-4">
                    La Corona Solar
                </h2>

                <p class="text-lg text-slate-300 mb-2">
                    Un rei necessita una corona que brilli com el dia.
                </p>

                <p class="text-indigo-300 text-sm mb-6">
                    Fes coincidir el color i el metall amb una carta.
                </p>

                <div class="border-t border-slate-700 pt-6">

                    <h3 class="font-title text-2xl mb-4">
                        Equilibri alquímic
                    </h3>

                    <div class="grid grid-cols-2 gap-4">

                        <div class="p-3 rounded-xl bg-slate-900/60">
                            🔥 Foc
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/60">
                            💧 Aigua
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/60">
                            🌍 Terra
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/60">
                            🌬 Aire
                        </div>

                    </div>

                </div>

            </section>

        </div>

        <!-- Cartes -->
        <section class="mt-16">

            <h3 class="text-center uppercase tracking-[0.3em] text-slate-400 text-sm mb-8">
                Les teves cartes
            </h3>

            <div class="flex flex-wrap justify-center gap-6">

                <div
                    class="w-32 h-44 rounded-2xl border border-red-400 bg-gradient-to-b from-red-500/20 to-red-900/20 flex flex-col items-center justify-between p-4 cursor-pointer hover:-translate-y-2 transition">
                    <span class="text-5xl">♈</span>
                    <span>Àries</span>
                    <span class="text-xs text-slate-400">♂ Ferro</span>
                </div>

                <div
                    class="w-32 h-44 rounded-2xl border border-yellow-400 bg-gradient-to-b from-yellow-500/20 to-yellow-900/20 flex flex-col items-center justify-between p-4 cursor-pointer hover:-translate-y-2 transition">
                    <span class="text-5xl">♌</span>
                    <span>Lleó</span>
                    <span class="text-xs text-slate-400">☉ Or</span>
                </div>

                <div
                    class="w-32 h-44 rounded-2xl border border-blue-400 bg-gradient-to-b from-blue-500/20 to-blue-900/20 flex flex-col items-center justify-between p-4 cursor-pointer hover:-translate-y-2 transition">
                    <span class="text-5xl">♋</span>
                    <span>Càncer</span>
                    <span class="text-xs text-slate-400">☽ Plata</span>
                </div>

            </div>
        </section>
    </main>
    <!-- Modal Guia -->
    @include('games.astral_recipe.modalGuiaAstral')

</body>

</html>
