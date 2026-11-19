{{-- Atributos data-* que lee astralquimium.js. Recibe $vista --}}
data-vista="{{ $vista }}"
data-sesion="{{ $sesionId }}"
data-sesion-aviso="{{ route('sesiones.finalizar', '__ID__') }}"
