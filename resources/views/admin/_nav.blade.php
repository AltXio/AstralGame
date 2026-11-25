{{-- Navegación del panel de administración. Recibe $seccion (metricas|administradores) --}}
<nav class="flex flex-wrap items-center gap-5 mb-6 text-sm text-[#93a0bd] border-b border-[#93a0bd]/20 pb-3" aria-label="Administración">
    <span class="font-display text-lg text-[#d3dbea] mr-2">Administración</span>
    <a href="{{ route('admin.index') }}"
       class="pb-1 border-b-2 {{ $seccion === 'metricas' ? 'text-[#d3dbea] border-[#6f9bff]' : 'border-transparent hover:text-[#d3dbea]' }}">Métricas</a>
    @if (auth()->user()->esSuperadmin())
        <a href="{{ route('admin.administradores.index') }}"
           class="pb-1 border-b-2 {{ $seccion === 'administradores' ? 'text-[#d3dbea] border-[#6f9bff]' : 'border-transparent hover:text-[#d3dbea]' }}">Administradores</a>
    @endif
    <a href="{{ route('home') }}" class="ml-auto hover:text-[#d3dbea]">Volver al inicio</a>
</nav>
