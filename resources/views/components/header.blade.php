<header class="w-full py-4 px-4 sm:px-8 border-b border-[#93a0bd]/20 bg-transparent backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        
        <!-- BRAND LOGO (Fuente Marcellus) -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2 text-[#d3dbea] hover:text-white transition-colors">
            <span class="font-display text-lg sm:text-2xl tracking-wider uppercase">Harmonia Alchymica</span>
        </a>

        <!-- MENÚ DE ESCRITORIO CON RESALTE GRIS GALÁCTICO (#2d3548) -->
        <nav class="hidden md:flex items-center space-x-2 text-sm text-[#93a0bd]">
            <a href="{{ route('home') }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ request()->routeIs('home') ? 'bg-[#2d3548] text-[#d3dbea] font-medium border border-[#6f9bff]/40 shadow-[0_0_10px_rgba(111,155,255,0.2)]' : 'hover:text-[#d3dbea]' }}">
               Inicio
            </a>
            
            @if (Route::has('login'))
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-1.5 rounded-full text-xs text-[#ff8a5c] border border-[#ff8a5c]/40 hover:bg-[#ff8a5c]/10 transition-all cursor-pointer">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-1.5 rounded-full transition-all {{ request()->routeIs('login') ? 'bg-[#2d3548] text-[#d3dbea] font-medium border border-[#6f9bff]/40 shadow-[0_0_10px_rgba(111,155,255,0.2)]' : 'hover:text-[#d3dbea]' }}">
                       Iniciar Sesión
                    </a>
                    
                    <a href="{{ route('register') }}" 
                       class="px-4 py-1.5 rounded-full text-xs transition-all {{ request()->routeIs('register') ? 'bg-[#2d3548] text-[#d3dbea] font-medium border border-[#6f9bff] shadow-[0_0_12px_rgba(111,155,255,0.4)]' : 'text-[#d3dbea] border border-[#6f9bff]/60 bg-[#16205f] hover:shadow-[0_0_15px_rgba(111,155,255,0.4)]' }}">
                       Registrarse
                    </a>
                @endauth
            @endif
        </nav>

        <!-- BOTÓN MÓVIL HAMBURGUESA -->
        <button id="mobile-menu-btn" class="md:hidden p-2 text-[#d3dbea] hover:text-white focus:outline-none" aria-label="Abrir menú">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- MENÚ MÓVIL -->
    <div id="mobile-menu" class="hidden md:hidden pt-4 pb-2 px-2 flex-col space-y-2 text-sm text-[#93a0bd] border-t border-[#93a0bd]/20 mt-3">
        <a href="{{ route('home') }}" class="py-1.5 px-3 rounded-lg {{ request()->routeIs('home') ? 'bg-[#2d3548] text-[#d3dbea]' : 'hover:text-[#d3dbea]' }}">Inicio</a>
        @if (Route::has('login'))
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left py-1.5 px-3 text-[#ff8a5c]">Cerrar Sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="py-1.5 px-3 rounded-lg {{ request()->routeIs('login') ? 'bg-[#2d3548] text-[#d3dbea]' : 'hover:text-[#d3dbea]' }}">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="py-1.5 px-3 rounded-lg {{ request()->routeIs('register') ? 'bg-[#2d3548] text-[#d3dbea]' : 'hover:text-[#d3dbea]' }}">Registrarse</a>
            @endauth
        @endif
    </div>
</header>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
        menu.classList.toggle('flex');
    });
</script>