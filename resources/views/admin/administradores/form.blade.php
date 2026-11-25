@extends('layouts.admin')

@section('titulo', $administrador->exists ? 'Editar administrador' : 'Nuevo administrador')

@section('contenido')
    @include('admin._nav', ['seccion' => 'administradores'])

    <h1 class="font-display text-3xl text-[#d3dbea] mb-4">{{ $administrador->exists ? 'Editar administrador' : 'Nuevo administrador' }}</h1>

    <form method="POST"
          action="{{ $administrador->exists ? route('admin.administradores.update', $administrador) : route('admin.administradores.store') }}"
          class="max-w-md space-y-4">
        @csrf
        @if ($administrador->exists)
            @method('PUT')
        @endif

        <label class="block text-sm text-[#93a0bd]">Nombre de usuario
            <input type="text" name="name" value="{{ old('name', $administrador->name) }}" maxlength="80" required
                   class="mt-1 w-full px-3 py-2 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
            @error('name') <span class="text-xs text-[#ff8a5c]">{{ $message }}</span> @enderror
        </label>

        <label class="block text-sm text-[#93a0bd]">Correo electrónico
            <input type="email" name="email" value="{{ old('email', $administrador->email) }}" maxlength="100" required
                   class="mt-1 w-full px-3 py-2 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
            @error('email') <span class="text-xs text-[#ff8a5c]">{{ $message }}</span> @enderror
        </label>

        <label class="block text-sm text-[#93a0bd]">Contraseña @if ($administrador->exists) <span class="text-xs">(déjala en blanco para no cambiarla)</span> @endif
            <input type="password" name="password" minlength="8" autocomplete="new-password" @required(! $administrador->exists)
                   class="mt-1 w-full px-3 py-2 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
            @error('password') <span class="text-xs text-[#ff8a5c]">{{ $message }}</span> @enderror
        </label>

        <label class="block text-sm text-[#93a0bd]">Repetir contraseña
            <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" @required(! $administrador->exists)
                   class="mt-1 w-full px-3 py-2 rounded-lg bg-[#10153f] border border-[#93a0bd]/40 text-white">
        </label>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2 rounded-full text-sm text-[#d3dbea] border border-[#6f9bff]/60 bg-[#16205f] hover:shadow-[0_0_15px_rgba(111,155,255,0.4)] cursor-pointer">Guardar</button>
            <a href="{{ route('admin.administradores.index') }}" class="text-sm text-[#93a0bd] hover:text-[#d3dbea]">Cancelar</a>
        </div>
    </form>
@endsection
