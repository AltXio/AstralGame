@extends('layouts.admin')

@section('titulo', 'Administradores')

@section('contenido')
    @include('admin._nav', ['seccion' => 'administradores'])

    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="font-display text-3xl text-[#d3dbea]">Administradores</h1>
            <p class="text-sm text-[#93a0bd]">Altas, bajas y modificaciones de los usuarios con rol administrador.</p>
        </div>
        <a href="{{ route('admin.administradores.create') }}"
           class="px-5 py-2 rounded-full text-sm text-[#d3dbea] border border-[#6f9bff]/60 bg-[#16205f] hover:shadow-[0_0_15px_rgba(111,155,255,0.4)]">Nuevo administrador</a>
    </div>

    @if (session('ok'))
        <p class="mb-4 px-4 py-2 rounded-lg border border-emerald-400/50 bg-emerald-500/10 text-emerald-200 text-sm" role="status">{{ session('ok') }}</p>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider text-[#93a0bd] border-b border-[#93a0bd]/30">
                <tr>
                    <th class="py-2 pr-4">Usuario</th>
                    <th class="py-2 pr-4">Correo</th>
                    <th class="py-2 pr-4">Alta</th>
                    <th class="py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($administradores as $a)
                    <tr class="border-b border-[#93a0bd]/10">
                        <td class="py-2 pr-4">{{ $a->name }}</td>
                        <td class="py-2 pr-4">{{ $a->email }}</td>
                        <td class="py-2 pr-4">{{ $a->created_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="py-2 text-right whitespace-nowrap">
                            <a href="{{ route('admin.administradores.edit', $a) }}" class="underline underline-offset-2 text-[#d3dbea] hover:text-white mr-3">Editar</a>
                            <form method="POST" action="{{ route('admin.administradores.destroy', $a) }}" class="inline"
                                  onsubmit="return confirm('¿Eliminar al administrador {{ $a->name }}? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="underline underline-offset-2 text-[#ff8a5c] hover:text-white cursor-pointer">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-[#93a0bd]">No hay administradores todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
