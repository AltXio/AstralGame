@extends('layouts.juego')

@section('titulo', $ficha['nombre'])

@push('estilos')
    @vite('resources/css/astralquimium.css')
@endpush

@section('contenido')
    {{-- Vista 2: ficha del planeta --}}
    <div class="aq" @include('games.astralquimium.partials.datos', ['vista' => 'planeta'])>
        @include('games.astralquimium.partials.barra', ['tab' => 'sistema'])

        <div class="planet-wrap">
            <figure class="dial-fig">
                <div class="dial" id="dial">
                    <div class="dial-core">
                        <div class="disc big" style="--tint:{{ $ficha['tinte'] }}">
                            @include('games.astralquimium.partials.glifo', ['svg' => $ficha['glifo']])
                        </div>
                    </div>

                    @foreach ($ficha['elementos'] as $e)
                        <button class="orb {{ $e['encontrado'] ? 'found' : '' }}" type="button" aria-pressed="false"
                                data-url="{{ $e['urlDescubrir'] }}"
                                data-id="{{ $e['id'] }}" data-sim="{{ $e['simbolo'] }}" data-nombre="{{ $e['nombre'] }}"
                                data-porque="{{ $e['porque'] }}" data-dato="{{ $e['dato'] }}"
                                data-alq="{{ $e['alq'] }}" data-alq-nombre="{{ $e['alqNombre'] }}"
                                data-color="{{ $e['color'] }}"
                                aria-label="{{ $e['nombre'] }}"
                                style="--el:{{ $e['color'] }};{{ $e['pos'] }}">
                            <span class="sym">{{ $e['simbolo'] }}</span><span class="nom">{{ $e['nombre'] }}</span>
                        </button>
                    @endforeach
                </div>
                <figcaption>Símbolo astronómico y astrológico</figcaption>
            </figure>

            <div class="info">
                <a class="back" href="{{ route('game.astralquimium.index') }}">Volver al sistema solar</a>
                <h2 id="p-nombre">{{ $ficha['nombre'] }}</h2>
                <p class="lead">{{ $ficha['descripcion'] }}</p>
                @if ($ficha['capa'])
                    <p class="capa">Elementos más abundantes en su {{ $ficha['capa'] }}</p>
                @endif

                <div class="balance">
                    <h3>Equilibrio alquímico</h3>
                    <ul>
                        @foreach ($ficha['equilibrio'] as $a)
                            <li style="--el:{{ $a['color'] }}">
                                @include('games.astralquimium.partials.alq', ['svg' => $a['svg']])
                                <span class="lbl">{{ $a['nombre'] }}</span>
                                <span class="pips" aria-label="{{ $a['cuenta'] }} de 4">
                                    @foreach (range(1, 4) as $n)
                                        <span class="pip {{ $n <= $a['cuenta'] ? 'on' : '' }}"></span>
                                    @endforeach
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="detail" id="detail" aria-live="polite">
                    <p class="empty">Toca un elemento del círculo para ver su ficha. Cada uno que descubras se guarda en tu inventario.</p>
                </div>
            </div>
        </div>

        <script type="application/json" id="aq-alquimicos">@json($alquimicos)</script>
    </div>

    @push('scripts')
        @vite('resources/js/astralquimium.js')
    @endpush
@endsection
