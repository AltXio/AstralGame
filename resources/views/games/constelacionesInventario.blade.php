<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header>
        <div class="logo">Constela<span>ciones</span></div>
        <button id="juego">← Volver al juego</button>
    </header>

    <main>
        <div class="panel">
            <h1>Inventario</h1>
            <p class="sub">Completa una constelación en el juego para desbloquearla.</p>
            <div id="lista" class="grid"></div>
        </div>

        <div class="panel" id="detalle" hidden style="margin-top:16px">
            <h1 id="invTitulo"></h1>
            <p class="sub" id="invDescripcion"></p>
            <div id="invFigura" class="sky"></div>

            <h3>Cuerpos celestes</h3>
            <div id="invCuerpos" class="pieces"></div>
        </div>
    </main>

   
    
</body>
</html>