<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/constelacion.css', 'resources/js/constelacionJuego.js', 'resources/css/app.css', 'resources/js/app.js'])
    <title>Constelaciones</title>
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
                <button class="on" id="jugar" onclick="window.location.href='{{ route('game.constelaciones') }}'">Jugar</button>
                <button id="inventario" onclick="window.location.href='{{ route('game.constelaciones.inventario') }}'">Inventario</button>
            </div>
        </div>

        <div id="lista-constelaciones" class="constelaciones">
            @foreach ($constelaciones as $index => $constelacion)
                <div class="constelacion-item" data-id="{{ $constelacion->id }}"
                    data-nombre="{{ $constelacion->nombre }}"
                    data-descripcion-mitologica="{{ $constelacion->descripcion_mitologica }}"
                    data-indice="{{ $index }}" data-completada="false">

                    <div class="top">
                        <div class="small">
                            <h2>{{ $constelacion->nombre }}</h2>
                            <span class="texto">
                                {{ $constelacion->descripcion_cientifica }}
                            </span>
                        </div>

                    </div>

                    <div class="layout play">
                        <section class="panel">
                            <div class="sub">
                                @if ($constelacion->nivel)
                                    <div class="texto" data-id="{{ $constelacion->nivel->id }}"
                                        data-nombre="{{ $constelacion->nivel->nombre }}">
                                        <h2> Nivel {{ $index + 1 }} - {{ $constelacion->nivel->nombre }}</h2>
                                    </div>
                                @endif
                                <span class="texto"> Toca un punto del cielo para recibir una pista y elige el cuerpo
                                    celeste que encaja.
                                </span>
                            </div>

                            <div class="small ins"></div>
                            <div class="sky"></div>

                            <div class="actions">
                                <button type="button" class="primary next" disabled>Siguiente →</button>
                            </div>

                            <div class="fb"></div>
                        </section>

                        <aside class="panel">
                            <h2>Cuerpos celestes</h2>

                            <div class="elementos">
                                <div class="elementos_alquimicos">
                                    @foreach ($elementosAlquimicos as $elemento)
                                        <div class="elemento_alquimico" data-id="{{ $elemento->id }}"
                                            data-nombre="{{ $elemento->nombre }}">

                                            <svg class="elemento-icono" viewBox="0 0 100 100" fill="none"
                                                stroke="currentColor" stroke-width="4"></svg>
                                            <span>{{ $elemento->nombre }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="texto">
                                Cada estrella tiene un mensaje: haz clic para ver su mensaje (otro clic lo oculta) y
                                arrástrala al punto que coincida con la pista.
                            </div>

                            <div class="cuerpos_celestes">
                                @foreach ($constelacion->cuerposCelestes as $cuerpo)
                                    <div class="card_cuerpo_celeste">
                                        <div class="cuerpo_celeste" data-id="{{ $cuerpo->id }}"
                                            data-temperatura="{{ $cuerpo->temperatura }}"
                                            data-nombre="{{ $cuerpo->nombre }}"
                                            data-magnitud="{{ $cuerpo->magnitud }}"
                                            data-anyodescubierto="{{ $cuerpo->anyo_descubierta }}"
                                            data-descubridor="{{ $cuerpo->descubridor }}"
                                            data-declinacion="{{ $cuerpo->declinacion }}"
                                            data-ascension="{{ $cuerpo->ascension_recta }}"
                                            data-elementos="{{ json_encode($cuerpo->elementosQuimicos) }}">

                                            <div class="orb"></div>
                                            <div class="small">

                                            </div>
                                            <div class="info-extra"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </aside>
                    </div>

                </div>
            @endforeach
        </div>
    </main>

    <footer class="relative z-10">
        <x-footer />
    </footer>
</body>

</html>
