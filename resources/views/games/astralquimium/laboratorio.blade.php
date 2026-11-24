@extends('layouts.juego')

@section('titulo', 'Laboratorio')

@push('estilos')
    @vite('resources/css/astralquimium.css')
@endpush

@section('contenido')
    {{-- Vista 3: laboratorio --}}
    <div class="aq" @include('games.astralquimium.partials.datos', ['vista' => 'laboratorio'])>
        @include('games.astralquimium.partials.barra', ['tab' => 'laboratorio'])

        @if ($inventario->isEmpty())
            <div class="lab-vacio">
                <h2>Laboratorio</h2>
                <p class="empty">Aún no tienes elementos. Entra en un planeta del sistema solar y toca sus elementos para descubrirlos.</p>
                <p><a class="back" href="{{ route('game.astralquimium.index') }}">Ir al sistema solar</a></p>
            </div>
        @else
            <div class="lab-wrap" id="lab"
                 data-max="{{ $maxPartes }}" data-umbral="{{ $umbral }}"
                 data-evaluar="{{ route('game.astralquimium.evaluar') }}"
                 data-guardar="{{ route('game.astralquimium.planeta.guardar') }}"
                 data-eliminar="{{ route('game.astralquimium.planeta.eliminar', '__ID__') }}">
                <div class="lab-left">
                    <div class="lab-top">
                        <div class="disc big lab-disc vacio" id="lab-disc" role="img" aria-label="Vista previa de tu planeta"><span class="interro">?</span></div>
                        <div class="meter" id="meter" role="img" aria-label="Habitabilidad"><span id="meter-val">0 %</span></div>
                        <div class="lab-state">
                            <p class="verdict" id="verdict">Mezcla vacía</p>
                            <p class="dom" id="dom">Sin elemento alquímico predominante</p>
                        </div>
                    </div>
                    <ul class="ind" id="ind">
                        @foreach (['atmosfera' => 'Atmósfera', 'agua' => 'Agua', 'suelo' => 'Suelo', 'temperatura' => 'Temperatura', 'energia' => 'Energía'] as $k => $nombre)
                            <li data-k="{{ $k }}"><span>{{ $nombre }}</span>
                                <span class="track"><span class="fill"></span></span>
                                <span class="val">0</span></li>
                        @endforeach
                    </ul>
                    <p class="tips" id="tips" aria-live="polite"></p>
                </div>

                <div class="lab-right">
                    <h2>Laboratorio</h2>
                    <p class="lead">Mezcla hasta {{ $maxPartes }} partes con los elementos que has descubierto.</p>
                    <div class="cuenta-fila">
                        <span id="cuenta">0 de {{ $maxPartes }} partes</span>
                        <button class="link" type="button" id="vaciar-mezcla">Vaciar mezcla</button>
                    </div>

                    <ul class="mix" id="mix">
                        @foreach ($inventario as $e)
                            <li class="mix-row" data-id="{{ $e['id'] }}" style="--el:{{ $e['color'] }}">
                                <span class="tile sm">{{ $e['simbolo'] }}</span>
                                <span class="mix-name">{{ $e['nombre'] }}
                                    <small>
                                        @if ($e['alqSvg'])
                                            @include('games.astralquimium.partials.alq', ['svg' => $e['alqSvg']]){{ $e['alqNombre'] }}
                                        @endif
                                    </small>
                                </span>
                                <button class="step menos" type="button" aria-label="Quitar una parte de {{ $e['nombre'] }}" disabled>−</button>
                                <span class="count" aria-live="polite">0</span>
                                <button class="step mas" type="button" aria-label="Añadir una parte de {{ $e['nombre'] }}">+</button>
                            </li>
                        @endforeach
                    </ul>

                    <label class="campo"><span>Nombre del planeta</span>
                        <input id="nombre-planeta" type="text" maxlength="24" autocomplete="off" placeholder="Por ejemplo, Aurora"></label>
                    <button class="boton" type="button" id="guardar" disabled>Crear planeta</button>
                    <p class="msg" id="msg" aria-live="polite"></p>

                    <h3>Tus planetas</h3>
                    <ul class="creados" id="creados">
                        @forelse ($creados as $p)
                            <li data-id="{{ $p['id'] }}" @if ($p['color']) style="--el:{{ $p['color'] }}" @endif>
                                @if ($p['svg']) @include('games.astralquimium.partials.alq', ['svg' => $p['svg']]) @endif
                                <span class="nombre">{{ $p['nombre'] }}</span>
                                <span class="estado">{{ $p['estado'] }}</span>
                                <button type="button" class="link quitar" aria-label="Quitar {{ $p['nombre'] }}">Quitar</button>
                            </li>
                        @empty
                            <li class="progreso" id="creados-vacio">Todavía no has creado ninguno.</li>
                        @endforelse
                    </ul>
                    <p class="progreso" id="progreso">{{ $progreso }}</p>
                </div>
            </div>
        @endif

        <script type="application/json" id="aq-alquimicos">@json($alquimicos)</script>
    </div>

    @push('scripts')
        @vite('resources/js/astralquimium.js')
    @endpush
@endsection
