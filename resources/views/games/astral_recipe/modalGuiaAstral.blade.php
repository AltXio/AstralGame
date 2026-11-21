<div id="guia-modal" class="hidden fixed inset-0 z-50">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- Modal -->
    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div
            class="w-full max-w-[600px] max-h-[90vh] overflow-y-auto rounded-3xl border 
        border-indigo-400/20 bg-[#0f0c46] p-7 shadow-2xl">

            <h2 class="font-title mb-5 text-center text-3xl text-white">
                Guia de l’alquimista
            </h2>

            <table class="w-full text-[15px] text-slate-200">
                <tbody>

                    @foreach ($signos as $signo)
                        <tr class="border-b border-white/10">

                            <td class="py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ asset('images/signos/' . strtolower($signo->nombre) . '.svg') }}"
                                        class="guia-signo-icon" alt="{{ $signo->nombre }}">
                                    {{ $signo->nombre }}
                                </div>
                            </td>

                            <td class="py-2.5">
                                @switch($signo->id_elemento_alquimico)
                                    @case(1)
                                        <div class="flex items-center gap-2 text-red-400">
                                            <img src="{{ asset('images/elementos/fuego.svg') }}" alt="Foc"
                                                class="guia-elemento-icon">
                                            <span>Foc</span>
                                        </div>
                                    @break

                                    @case(2)
                                        <div class="flex items-center gap-2 text-blue-400">
                                            <img src="{{ asset('images/elementos/agua.svg') }}" alt="Aigua"
                                                class="guia-elemento-icon">
                                            <span>Aigua</span>
                                        </div>
                                    @break

                                    @case(3)
                                        <div class="flex items-center gap-2 text-green-400">
                                            <img src="{{ asset('images/elementos/tierra.svg') }}" alt="Terra"
                                                class="guia-elemento-icon">
                                            <span>Terra</span>
                                        </div>
                                    @break

                                    @case(4)
                                        <div class="flex items-center gap-2 text-yellow-300">
                                            <img src="{{ asset('images/elementos/aire.svg') }}" alt="Aire"
                                                class="guia-elemento-icon">
                                            <span>Aire</span>
                                        </div>
                                    @break
                                @endswitch
                            </td>

                            <td class="py-2.5">
                                <span>
                                    {{ $signo->metal }}
                                </span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-5 flex justify-center">
                <button id="close-guia"
                    class="rounded-full border border-slate-300 px-8 py-3 transition hover:bg-white/10">
                    Tancar
                </button>
            </div>
        </div>
    </div>
</div>