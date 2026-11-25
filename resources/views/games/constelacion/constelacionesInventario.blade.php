<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario</title>
    @vite(['resources/css/constelacion.css', 'resources/js/constelacionJuego.js', 'resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header class="relative z-20 shrink-0 bg-transparent">
        <x-header />
    </header>

    <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>
    <main>

        <div class="header">
            <h2>Constelaciones</h2>

            <div class="tabs">
                <button class="on" id="jugar"
                    onclick="window.location.href='{{ route('game.constelaciones') }}'">Jugar</button>
                <button id="inventario"
                    onclick="window.location.href='{{ route('game.constelaciones.inventario') }}'">Inventario</button>
            </div>
        </div>

        <div class="panel">
            <h1>Inventario</h1>
            <p class="sub">Completa una constelación en el juego para desbloquearla.</p>
            <div id="lista" class="grid"></div>
        </div>
    </main>

    <footer class="relative z-10">
        <x-footer />
    </footer>
</body>

</html>
