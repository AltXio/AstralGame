<!-- resources/views/components/header.blade.php -->
<header class="w-full py-5 px-6 border-b border-slate-800/60 bg-cosmic-panel/60 backdrop-blur-md sticky top-0 z-40">
    <div class="max-w-5xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold font-alchemy text-alchemy-gold tracking-wider">HARMONIA ALCHYMICA</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-900/40 text-alchemy-cyan border border-alchemy-cyan/30">Proyecto DAW</span>
        </div>
        <nav class="flex items-center space-x-6 text-sm text-slate-300">
            <a href="#modos" class="hover:text-alchemy-gold transition-colors">Modos</a>
            <a href="#acerca" class="hover:text-alchemy-gold transition-colors">Info Proyecto</a>
            
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-alchemy-gold transition-all border border-slate-700">Mi Panel</a>
                @else
                    <a href="{{ route('login') }}" class="text-xs hover:text-alchemy-gold">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-xs text-alchemy-gold border border-alchemy-gold/40 transition-all">Registrarse</a>
                @endauth
            @endif
        </nav>
    </div>
</header>