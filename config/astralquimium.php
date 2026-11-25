<?php

/*
 * Datos de presentación del Juego 1 (Astralquimium).
 * Colores, iconos y órbitas NO van en la base de datos: se asocian por el
 * nombre en minúsculas y sin tildes (Str::slug) del registro correspondiente.
 */
return [
    // Reglas del laboratorio
    'max_partes' => 12,
    'umbral_desbloqueo' => 4,          // planetas creados para terminar el juego 1 y desbloquear los demás
    'juegos_desbloqueados' => [2, 3, 4],  // ids de la tabla juegos: al terminar el juego 1 se abren todos
    'umbral_habitable' => 50,          // % de habitabilidad desde el que un planeta cuenta como habitable (el resto cuenta como error)
    'id_juego' => 1,
    'inactividad_segundos' => 600,     // 10 minutos sin actividad cierran la sesión

    // Elementos alquímicos (por nombre)
    'alquimicos' => [
        'fuego'  => ['nombre' => 'Fuego',  'color' => '#ff8a5c', 'svg' => '<polygon points="50,12 90,84 10,84"/>'],
        'agua'   => ['nombre' => 'Agua',   'color' => '#4cc9f0', 'svg' => '<polygon points="10,16 90,16 50,88"/>'],
        'tierra' => ['nombre' => 'Tierra', 'color' => '#8fcf8a', 'svg' => '<polygon points="10,16 90,16 50,88"/><path d="M26 44h48"/>'],
        'aire'   => ['nombre' => 'Aire',   'color' => '#c0a9ff', 'svg' => '<polygon points="50,12 90,84 10,84"/><path d="M23 60h54"/>'],
    ],

    // Símbolo químico para mostrar (por nombre del elemento químico)
    'simbolos' => [
        'hidrogeno' => 'H', 'helio' => 'He', 'carbono' => 'C', 'nitrogeno' => 'N',
        'oxigeno' => 'O', 'magnesio' => 'Mg', 'aluminio' => 'Al', 'silicio' => 'Si',
        'azufre' => 'S', 'hierro' => 'Fe',
    ],

    // Planetas (por nombre): tinte, capa estudiada, glifo SVG y órbita.
    // orbita: d = diámetro (% del escenario), tam = tamaño (%), periodo = segundos por vuelta, fase = 0..1
    'planetas' => [
        'sol' => ['tinte' => '#ffb347', 'capa' => 'fotosfera',
            'glifo' => '<circle cx="50" cy="50" r="34"/><circle cx="50" cy="50" r="5" fill="currentColor" stroke="none"/>'],
        'mercurio' => ['tinte' => '#9aa5b8', 'capa' => 'corteza', 'orbita' => ['d' => 22, 'tam' => 5.5, 'periodo' => 18, 'fase' => .05],
            'glifo' => '<path d="M34 14a16 16 0 0 0 32 0"/><circle cx="50" cy="44" r="14"/><path d="M50 58v32M37 76h26"/>'],
        'venus' => ['tinte' => '#e3a857', 'capa' => 'atmósfera', 'orbita' => ['d' => 32, 'tam' => 7, 'periodo' => 28, 'fase' => .62],
            'glifo' => '<circle cx="50" cy="36" r="20"/><path d="M50 56v34M36 74h28"/>'],
        'tierra' => ['tinte' => '#3f7be0', 'capa' => 'corteza', 'orbita' => ['d' => 43, 'tam' => 7.2, 'periodo' => 38, 'fase' => .31],
            'glifo' => '<circle cx="50" cy="50" r="32"/><path d="M50 18v64M18 50h64"/>'],
        'marte' => ['tinte' => '#c8553d', 'capa' => 'corteza', 'orbita' => ['d' => 54, 'tam' => 6, 'periodo' => 50, 'fase' => .84],
            'glifo' => '<circle cx="42" cy="58" r="24"/><path d="M59 41L84 16M62 16h22v22"/>'],
        'jupiter' => ['tinte' => '#c98f5f', 'capa' => 'atmósfera', 'orbita' => ['d' => 66, 'tam' => 11, 'periodo' => 70, 'fase' => .47],
            'glifo' => '<path d="M22 28C22 12 36 8 46 14C56 22 52 36 42 44C34 50 26 54 20 58H80"/><path d="M62 32V90"/>'],
        'saturno' => ['tinte' => '#d9c27a', 'capa' => 'atmósfera y anillos', 'orbita' => ['d' => 76, 'tam' => 10, 'periodo' => 90, 'fase' => .12],
            'glifo' => '<path d="M30 20h24M42 8v44"/><path d="M42 34C54 24 70 30 70 46C70 62 54 70 54 82C54 90 62 94 70 90"/>'],
        'urano' => ['tinte' => '#5fd0d6', 'capa' => 'atmósfera y hielos', 'orbita' => ['d' => 85, 'tam' => 8, 'periodo' => 115, 'fase' => .73],
            'glifo' => '<path d="M28 14v42M72 14v42M28 35h44M50 35v34"/><circle cx="50" cy="80" r="10"/>'],
        'neptuno' => ['tinte' => '#4a5be8', 'capa' => 'atmósfera', 'orbita' => ['d' => 93, 'tam' => 8, 'periodo' => 140, 'fase' => .55],
            'glifo' => '<path d="M24 12v18c0 16 12 24 26 24s26-8 26-24V12M50 8v82M36 74h28"/><path d="M16 22L24 12L32 22M68 22L76 12L84 22"/>'],
    ],
];
