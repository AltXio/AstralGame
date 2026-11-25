@extends('layouts.admin')

@section('titulo', 'Métricas')

@section('contenido')
    @include('admin._nav', ['seccion' => 'metricas'])

    <h1 class="font-display text-3xl text-[#d3dbea] mb-1">Métricas de los juegos</h1>
    <p class="text-sm text-[#93a0bd] mb-6">Datos de las partidas registradas en la tabla de sesiones. «—» indica que el juego no mide esa métrica.</p>

    {{-- Análisis en Python (Pyodide): lo orquesta resources/js/admin-metricas.js --}}
    <section id="metricas-python" class="mb-10" aria-label="Análisis en Python"
             data-pyodide="{{ $pyodideUrl }}"
             data-datos="{{ route('admin.metricas.datos') }}"
             data-script="{{ asset('python/metricas.py') }}">
        <h2 class="font-display text-xl text-[#d3dbea] mb-2">Análisis de sesiones</h2>
        <p id="py-estado" class="text-sm text-[#93a0bd] mb-3" role="status" aria-live="polite">Preparando…</p>
        <div id="py-salida"></div>
    </section>

    {{-- Detalle de las métricas capturadas y derivadas --}}
    <section class="mb-10" aria-label="Detalle de las métricas">
        <h2 class="font-display text-xl text-[#d3dbea] mb-3">Detalle de las métricas</h2>
        <div class="grid gap-8 md:grid-cols-2">
            @foreach (['capturadas' => 'Capturadas por sesión', 'derivadas' => 'Derivadas'] as $clave => $titulo)
                <div>
                    <h3 class="text-xs uppercase tracking-wider text-[#93a0bd] mb-2">{{ $titulo }}</h3>
                    <dl class="text-sm space-y-1.5">
                        @foreach ($detalle[$clave] as $nombre => $descripcion)
                            <div class="flex gap-3">
                                <dt class="w-44 shrink-0 font-mono text-[#d3dbea]">{{ $nombre }}</dt>
                                <dd class="text-[#93a0bd]">{{ $descripcion }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Resumen por juego --}}
    <section class="mb-10 overflow-x-auto" aria-label="Resumen por juego">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider text-[#93a0bd] border-b border-[#93a0bd]/30">
                <tr>
                    <th class="py-2 pr-4">Juego</th>
                    <th class="py-2 pr-4">Partidas</th>
                    <th class="py-2 pr-4">Jugadores</th>
                    <th class="py-2 pr-4">Duración media</th>
                    <th class="py-2 pr-4">Puntuación media</th>
                    <th class="py-2 pr-4">Completadas</th>
                    <th class="py-2 pr-4">Intentos</th>
                    <th class="py-2 pr-4">Errores</th>
                    <th class="py-2">Ayudas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resumen as $j)
                    <tr class="border-b border-[#93a0bd]/10 {{ $idJuego === $j['id'] ? 'bg-[#2d3548]/60' : '' }}">
                        <td class="py-2 pr-4"><a class="underline underline-offset-2 hover:text-white" href="{{ route('admin.index', ['juego' => $j['id']]) }}">{{ $j['nombre'] }}</a></td>
                        <td class="py-2 pr-4">{{ $j['sesiones'] }}</td>
                        <td class="py-2 pr-4">{{ $j['jugadores'] }}</td>
                        <td class="py-2 pr-4">{{ $j['duracion'] }}</td>
                        <td class="py-2 pr-4">{{ $j['puntuacion'] }}</td>
                        <td class="py-2 pr-4">{{ $j['completadas'] }}</td>
                        <td class="py-2 pr-4">{{ $j['intentos'] }}</td>
                        <td class="py-2 pr-4">{{ $j['errores'] }}</td>
                        <td class="py-2">{{ $j['ayudas'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- Resumen por nivel del juego elegido --}}
    @if ($porNivel->isNotEmpty())
        <section class="mb-10 overflow-x-auto" aria-label="Resumen por nivel">
            <h2 class="font-display text-xl text-[#d3dbea] mb-2">Por nivel</h2>
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wider text-[#93a0bd] border-b border-[#93a0bd]/30">
                    <tr>
                        <th class="py-2 pr-4">Nivel</th>
                        <th class="py-2 pr-4">Partidas</th>
                        <th class="py-2 pr-4">Jugadores</th>
                        <th class="py-2 pr-4">Duración media</th>
                        <th class="py-2 pr-4">Puntuación media</th>
                        <th class="py-2 pr-4">Completadas</th>
                        <th class="py-2 pr-4">Intentos</th>
                        <th class="py-2 pr-4">Errores</th>
                        <th class="py-2">Ayudas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($porNivel as $n)
                        <tr class="border-b border-[#93a0bd]/10">
                            <td class="py-2 pr-4">{{ $n['nombre'] }}</td>
                            <td class="py-2 pr-4">{{ $n['sesiones'] }}</td>
                            <td class="py-2 pr-4">{{ $n['jugadores'] }}</td>
                            <td class="py-2 pr-4">{{ $n['duracion'] }}</td>
                            <td class="py-2 pr-4">{{ $n['puntuacion'] }}</td>
                            <td class="py-2 pr-4">{{ $n['completadas'] }}</td>
                            <td class="py-2 pr-4">{{ $n['intentos'] }}</td>
                            <td class="py-2 pr-4">{{ $n['errores'] }}</td>
                            <td class="py-2">{{ $n['ayudas'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    {{-- Partidas --}}
    <section aria-label="Partidas">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-3">
            <h2 class="font-display text-xl text-[#d3dbea]">Partidas</h2>
            <form method="GET" action="{{ route('admin.index') }}" class="flex flex-wrap items-end gap-3 text-sm">
                <label class="flex flex-col text-xs text-[#93a0bd]">Juego
                    <select name="juego" class="mt-1 px-3 py-1.5 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
                        <option value="">Todos</option>
                        @foreach ($juegos as $j)
                            <option value="{{ $j->id }}" @selected($idJuego === $j->id)>{{ $j->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                @if ($niveles->isNotEmpty())
                    <label class="flex flex-col text-xs text-[#93a0bd]">Nivel
                        <select name="nivel" class="mt-1 px-3 py-1.5 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
                            <option value="">Todos</option>
                            @foreach ($niveles as $n)
                                <option value="{{ $n->id }}" @selected($idNivel === $n->id)>{{ $n->nombre }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
                <button type="submit" class="px-4 py-1.5 rounded-full border border-[#6f9bff]/60 bg-[#16205f] text-[#d3dbea] hover:shadow-[0_0_15px_rgba(111,155,255,0.4)]">Filtrar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-wider text-[#93a0bd] border-b border-[#93a0bd]/30">
                    <tr>
                        <th class="py-2 pr-4">Sesión</th>
                        <th class="py-2 pr-4">Usuario</th>
                        <th class="py-2 pr-4">Juego</th>
                        <th class="py-2 pr-4">Nivel</th>
                        <th class="py-2 pr-4">Inicio</th>
                        <th class="py-2 pr-4">Duración</th>
                        <th class="py-2 pr-4">Puntuación</th>
                        <th class="py-2 pr-4">Completada</th>
                        <th class="py-2 pr-4">Intentos</th>
                        <th class="py-2 pr-4">Errores</th>
                        <th class="py-2">Ayudas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sesiones as $s)
                        <tr class="border-b border-[#93a0bd]/10">
                            <td class="py-2 pr-4">{{ $s['id'] }}</td>
                            <td class="py-2 pr-4">{{ $s['usuario'] }}</td>
                            <td class="py-2 pr-4">{{ $s['juego'] }}</td>
                            <td class="py-2 pr-4">{{ $s['nivel'] }}</td>
                            <td class="py-2 pr-4">{{ $s['fecha'] }}</td>
                            <td class="py-2 pr-4">{{ $s['duracion'] }}</td>
                            <td class="py-2 pr-4">{{ $s['puntuacion'] }}</td>
                            <td class="py-2 pr-4">{{ $s['completada'] ? 'Sí' : 'No' }}</td>
                            <td class="py-2 pr-4">{{ $s['intentos'] }}</td>
                            <td class="py-2 pr-4">{{ $s['errores'] }}</td>
                            <td class="py-2">{{ $s['ayudas'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="py-6 text-center text-[#93a0bd]">Todavía no hay partidas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-sm">{{ $sesiones->links() }}</div>
    </section>
@endsection

@push('scripts')
    {{-- Pyodide desde el CDN (versión fijada en config/metricas.php); define loadPyodide() --}}
    <script src="{{ $pyodideUrl }}pyodide.js"></script>
    @vite('resources/js/admin-metricas.js')
@endpush
