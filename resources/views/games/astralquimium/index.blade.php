@extends('layouts.juego')

@section('titulo', 'Astralquimium')

@push('estilos')
    @vite('resources/css/astralquimium.css')
@endpush

@section('contenido')
    {{-- Vista 1: sistema solar --}}
    <div class="aq" @include('games.astralquimium.partials.datos', ['vista' => 'sistema'])>
        @include('games.astralquimium.partials.barra', ['tab' => 'sistema'])

        <section class="stage" id="stage" aria-label="Sistema solar">
            @foreach ($orbitantes as $p)
                <div class="orbit" style="--d:{{ $p['orbita']['d'] }};--period:{{ $p['orbita']['periodo'] }}s;--phase:{{ $p['orbita']['fase'] }}">
                    <div class="slot" style="--s:{{ $p['orbita']['tam'] }}">
                        <a class="body disc" href="{{ $p['url'] }}"
                           data-nombre="{{ $p['nombre'] }}" data-resumen="{{ $p['descripcion'] }}"
                           aria-label="{{ $p['nombre'] }}: ver sus elementos" style="--tint:{{ $p['tinte'] }}">
                            @include('games.astralquimium.partials.glifo', ['svg' => $p['glifo']])
                        </a>
                    </div>
                </div>
            @endforeach

            @if ($sol)
                <a class="body disc sun" href="{{ $sol['url'] }}"
                   data-nombre="{{ $sol['nombre'] }}" data-resumen="{{ $sol['descripcion'] }}"
                   aria-label="{{ $sol['nombre'] }}: ver sus elementos">
                    @include('games.astralquimium.partials.glifo', ['svg' => $sol['glifo']])
                </a>
            @endif
        </section>

        <p class="hint" id="hint" data-base="Toca un planeta para ver sus elementos." aria-live="polite">Toca un planeta para ver sus elementos.</p>
    </div>

    @push('scripts')
        @vite('resources/js/astralquimium.js')
    @endpush
@endsection
