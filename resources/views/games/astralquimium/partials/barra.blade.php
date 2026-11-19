{{-- Pestañas e inventario. Recibe $tab (sistema|laboratorio) y $inventario --}}
<div class="bar">
    <nav class="tabs" aria-label="Secciones">
        <a href="{{ route('game.astralquimium.index') }}" @if ($tab === 'sistema') aria-current="page" @endif>Sistema solar</a>
        <a href="{{ route('game.astralquimium.laboratorio') }}" @if ($tab === 'laboratorio') aria-current="page" @endif>Laboratorio</a>
    </nav>
    <div class="inv">
        <span>Inventario</span>
        <ul id="inv-list" class="inv-list">
            @forelse ($inventario as $e)
                <li class="chip-sym" title="{{ $e['nombre'] }}" style="--el:{{ $e['color'] }}">{{ $e['simbolo'] }}</li>
            @empty
                <li class="inv-empty" id="inv-empty">Vacío. Descubre elementos en cada planeta.</li>
            @endforelse
        </ul>
    </div>
</div>
