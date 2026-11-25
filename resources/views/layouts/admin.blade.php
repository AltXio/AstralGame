<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') | Administración | Harmonia Alchymica</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&family=Marcellus&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- el fondo va en línea porque app.css fija el del body y pisa las clases de Tailwind --}}
<body class="min-h-screen flex flex-col text-[#e6ebf7] font-sans" style="background-color:#070a22">

    <header class="relative z-20">
        <x-header />
    </header>

    <main class="relative z-10 flex-grow w-full max-w-7xl mx-auto px-4 py-6">
        @yield('contenido')
    </main>

    @stack('scripts')
</body>
</html>
