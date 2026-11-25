<?php

/*
 * Sección "Métricas": el análisis se hace en Python (pandas, matplotlib, scikit-learn)
 * ejecutado en el navegador con Pyodide.
 */
return [
    // Carpeta del CDN de Pyodide (debe acabar en "/"). Fijamos la versión para que no cambie sola.
    'pyodide_url' => 'https://cdn.jsdelivr.net/pyodide/v314.0.8/full/',

    // Descripción de las métricas para la vista "Métricas" (el enunciado pide una descripción concisa de cada una)
    'detalle' => [
        'capturadas' => [
            'username' => 'Nombre de usuario del jugador.',
            'session_id' => 'Identificador único de la sesión (partida).',
            'date_time' => 'Fecha y hora de inicio de la sesión.',
            'session_length' => 'Duración de la sesión en segundos, actualizada mientras hay actividad.',
            'final_score' => 'Mejor habitabilidad (0 a 100) conseguida en la sesión.',
            'n_attempts' => 'Planetas creados en la sesión.',
            'errors_made' => 'Planetas creados que no son habitables (menos del 50 % de habitabilidad).',
            'help_clicks' => 'Fichas de elemento consultadas en la sesión.',
            'completed' => '1 si el jugador ha alcanzado el mínimo de planetas para terminar el juego; 0 si no.',
        ],
        'derivadas' => [
            'session_minutes' => 'Duración de la sesión convertida a minutos.',
            'player_count' => 'Número de jugadores únicos.',
            'returning_player' => '1 si el jugador tiene 2 o más sesiones (vuelve); 0 si solo tiene una (abandona).',
            'retention_rate' => 'Porcentaje de jugadores que vuelven (2 o más sesiones).',
            'churn_rate' => 'Porcentaje de jugadores que abandonan (una sola sesión).',
            'average_session_length' => 'Duración media de las sesiones, en minutos.',
            'DAU' => 'Jugadores únicos activos por día.',
            'MAU' => 'Jugadores únicos activos por mes.',
        ],
    ],
];
