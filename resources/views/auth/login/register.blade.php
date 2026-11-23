<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Harmonia Alchymica</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&family=Marcellus&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen flex flex-col justify-between text-[#e6ebf7] font-sans overflow-x-hidden">

    <x-header />

    <canvas id="cosmic-bg-canvas" class="fixed inset-0 -z-10 w-full h-full pointer-events-none"></canvas>

    <main class="relative z-10 flex-grow flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-md p-6 sm:p-8 rounded-3xl bg-transparent border border-[#d3dbea]/40 backdrop-blur-md shadow-[0_0_30px_rgba(7,10,34,0.85)]">
            
            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-display text-[#d3dbea] tracking-wider mb-1">
                    REGISTRO DE INICIADO
                </h2>
                <p class="text-xs text-[#93a0bd]">Crea tu perfil para comenzar a desbloquear el conocimiento del universo</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-3 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-xs uppercase tracking-wider font-medium text-[#d3dbea] mb-1.5">
                        Nombre de Jugador
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-[#070a22]/80 border border-[#93a0bd]/40 text-white placeholder-[#93a0bd]/60 text-sm focus:outline-none focus:border-[#6f9bff] focus:ring-1 focus:ring-[#6f9bff] transition-all"
                           placeholder="Astraeus">
                </div>

                <div>
                    <label for="email" class="block text-xs uppercase tracking-wider font-medium text-[#d3dbea] mb-1.5">
                        Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-[#070a22]/80 border border-[#93a0bd]/40 text-white placeholder-[#93a0bd]/60 text-sm focus:outline-none focus:border-[#6f9bff] focus:ring-1 focus:ring-[#6f9bff] transition-all"
                           placeholder="alquimista@astralgame.com">
                </div>

                <div>
                    <label for="password" class="block text-xs uppercase tracking-wider font-medium text-[#d3dbea] mb-1.5">
                        Contraseña
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-[#070a22]/80 border border-[#93a0bd]/40 text-white placeholder-[#93a0bd]/60 text-sm focus:outline-none focus:border-[#6f9bff] focus:ring-1 focus:ring-[#6f9bff] transition-all"
                           placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs uppercase tracking-wider font-medium text-[#d3dbea] mb-1.5">
                        Confirmar Contraseña
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2.5 sm:py-3 rounded-xl bg-[#070a22]/80 border border-[#93a0bd]/40 text-white placeholder-[#93a0bd]/60 text-sm focus:outline-none focus:border-[#6f9bff] focus:ring-1 focus:ring-[#6f9bff] transition-all"
                           placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 px-6 rounded-full font-medium text-center text-xs tracking-wider uppercase text-white bg-transparent border border-[#d3dbea] hover:shadow-[0_0_18px_rgba(111,155,255,0.45)] transition-all duration-300 cursor-pointer">
                        CREAR CUENTA
                    </button>
                </div>
            </form>

            <div class="mt-6 sm:mt-8 pt-5 border-t border-[#93a0bd]/20 text-center text-xs text-[#93a0bd]">
                ¿Ya posees una cuenta registrada? 
                <a href="{{ route('login') }}" class="text-[#6f9bff] hover:text-[#d3dbea] font-medium underline underline-offset-4 transition-colors">
                    Inicia sesión aquí
                </a>
            </div>

        </div>
    </main>

    <footer class="relative z-10 border-t border-[#93a0bd]/20 py-4 text-center text-xs text-[#93a0bd]">
        <p>&copy; Harmonia Alchymica - Proyecto DAW</p>
    </footer>

</body>
</html>