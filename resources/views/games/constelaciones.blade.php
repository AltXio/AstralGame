<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/constelacion.css', 'resources/js/constelacionJuego.js','resources/css/app.css','resources/js/app.js'])
    <title>Constelaciones</title>
</head>

<body>

    <body>

        <header class="relative z-20 shrink-0 bg-transparent">
            <x-header />
        </header>

        <!-- Canvas de fondo fijado al fondo de la pantalla -->
        <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>


        <div class="header">
            <h2>Constelaciones</h2>

            <div class="tabs"><button class="on" id="jugar">Jugar</button>
                <button id="inventario"> Inventario</button>
            </div>
        </div>
        
        <main>
            <div class="top">
                <div class="small">
                    <h2 id="title"></h2>
                    <p id="descripcionCientifica" class="descripcionCientifica"></p>

                </div>
                <div class="count">Nivel 1</div>
            </div>

            <div id="play" class="layout">
                <section class="panel">
                    <div class="sub" id="sub">Toca un punto del cielo para recibir una pista y elige el cuerpo
                        celeste que
                        encaja.
                    </div>

                    <div class="small" id="ins"></div>
                    <div class="sky" id="sky"></div>
                    <div class="actions">
                        <button id="reset">Reiniciar</button><button id="next" class="primary"
                            disabled>Siguiente
                            →</button>
                    </div>
                    <div class="fb" id="fb">

                    </div>
                </section>
                <aside class="panel">
                    <h2>Cuerpos celestes</h2>
                    <div class="small">Cada estrella tiene un mensaje: haz clic para ver su mensaje (otro clic lo
                        oculta) y
                        arrástrala al punto que coincida con la pista.
                    </div>
                    <div id="elementos">

                    </div>
                    <div class="clue" id="clue"></div>
                    <div class="pieces" id="pieces"></div>
                    <div class="small" style="margin-top:10px">E</div>
                </aside>
            </div>

            <section class="panel" id="inv" style="display:none">
                <h2>Mi inventario celeste</h2>
                <div class="small">Toca una constelación completada para ver su historia y sus estrellas.</div>
                <div class="grid" id="saved"></div>
                <div id="detail" style="display:none">
                    <div class="head" style="margin-top:18px">
                        <h2 id="dTitle"></h2><span style="display:flex;gap:8px"><button id="dArt">Ocultar
                                dibujo</button><button id="dMel">♫ Melodía</button></span>
                    </div>
                    <div class="small" id="dSub"></div>
                    <div class="big" id="dFig"></div>
                    <div class="info" id="dStar">
                        <h4>Toca una estrella de la figura</h4>
                        <p>Verás su elemento clásico, su temperatura en °C, su elemento predominante y un dato curioso.
                        </p>
                    </div>
                    <div class="info">
                        <h4>¿Por qué se llama así?</h4>
                        <p id="dHist"></p>
                    </div>
                    <div class="info">
                        <h4>La constelación</h4>
                        <p id="dInfo"></p>
                    </div>
                    <h3>Estrellas principales</h3>
                    <div class="stars" id="dStars"></div>
                </div>
            </section>
        </main>
        <footer class="relative z-10">
            <x-footer />
        </footer>

    </body>

</html>
