<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Altas, bajas y modificaciones de los usuarios administrador (solo superadministrador).
 * Solo gestiona usuarios con rol administrador: ni jugadores ni otros superadministradores.
 */
class AdministradorController extends Controller
{
    public function index(): View
    {
        return view('admin.administradores.index', [
            'administradores' => Usuario::where('role_id', Usuario::ROL_ADMIN)->orderBy('name')->get(['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function create(): View
    {
        return view('admin.administradores.form', ['administrador' => new Usuario()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:usuarios,name'],
            'email' => ['required', 'email', 'max:100', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = new Usuario($datos);
        $admin->role_id = Usuario::ROL_ADMIN;
        $admin->save();

        return redirect()->route('admin.administradores.index')->with('ok', "Administrador «{$admin->name}» creado.");
    }

    public function edit(Usuario $administrador): View
    {
        $this->comprobar($administrador);

        return view('admin.administradores.form', ['administrador' => $administrador]);
    }

    public function update(Request $request, Usuario $administrador): RedirectResponse
    {
        $this->comprobar($administrador);

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('usuarios', 'name')->ignore($administrador->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'email')->ignore($administrador->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        if (empty($datos['password'])) {
            unset($datos['password']);          // sin contraseña nueva se conserva la actual
        }

        $administrador->update($datos);

        return redirect()->route('admin.administradores.index')->with('ok', "Administrador «{$administrador->name}» actualizado.");
    }

    public function destroy(Usuario $administrador): RedirectResponse
    {
        $this->comprobar($administrador);
        $nombre = $administrador->name;
        $administrador->delete();

        return redirect()->route('admin.administradores.index')->with('ok', "Administrador «{$nombre}» eliminado.");
    }

    /** Esta sección solo toca usuarios con rol administrador. */
    private function comprobar(Usuario $usuario): void
    {
        abort_unless((int) $usuario->role_id === Usuario::ROL_ADMIN, 404);
    }
}
