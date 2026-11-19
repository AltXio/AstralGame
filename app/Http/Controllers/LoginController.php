<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;


class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login.login');

    }

    public function showRegister()
    {
        return view('auth.login.register');

    }

    public function login(Request $request) {
    //     $usu = Usuario::find(1);
    //     $usu->password = Hash::make('123456');
    //     $usu->save();


        $usuari = Usuario::where('name', $request-> input('name'))-> first();

        if($usuari && Hash::check($request-> input('password'), $usuari->password))
            {
                Auth::login($usuari);
                $response = redirect('/');
            }
            else {
                session()->flash('error', 'contraseña incorrecta macho pon-la bien');
                $response  = redirect()->back()->withInput();
            return back()->withErrors([
            'name' => 'Este usuario no exite ni ha existido ni existira si puedo evitarlo'
                    ]);
            }
        
        return $response;
    }

    public function register(Request $request)
    {
        $user = Usuario::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // role_id usa el valor por defecto de la tabla: 1 = jugador (2 = admin)
        ]);

        Auth::login($user);

        return redirect()->route('home');


    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
