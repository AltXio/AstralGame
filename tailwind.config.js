/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // 1. FONDOS CÓSMICOS (Cosmic)
        cosmic: {
          bg: '#03050a',       // Fondo profundo de la pantalla
          mid: '#0b1220',      // Capa intermedia (secciones/paneles)
          light: '#111a2e',    // Tono claro del gradiente o tarjetas
          panel: '#0a101e',    // Fondos de paneles laterales o tarjetas
        },

        // 2. ACENTOS MÍSTICOS / ALQUIMIA (Alchemy)
        alchemy: {
          gold: '#f59e0b',     // Dorado/Ámbar (resplandores, nodos principales, enlaces)
          cyan: '#00aeff',     // Cian (bordes, botones activos, efectos lumínicos)
          purple: '#a855f7',   // Púrpura (nodos místicos o modo zodiaco)
        },

        // 3. ELEMENTOS ALQUÍMICOS (Element)
        element: {
          fuego: '#ff5722',    // Rojo/Naranja de fuego[cite: 2, 3]
          agua: '#2196f3',     // Azul de agua[cite: 2, 3]
          tierra: '#4caf50',   // Verde de tierra[cite: 2, 3]
          aire: '#ffeb3b',     // Amarillo de aire[cite: 2, 3]
        },

        // 4. INTERFAZ Y ESTADOS
        slate: {
          950: '#030712',      // Fondo ultra oscuro para capas sobrepuestas
        }
      },
      fontFamily: {
        alchemy: ['Cinzel', 'serif'], // Fuente mística del proyecto
      }
    },
  },
  plugins: [],
}