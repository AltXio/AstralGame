import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('cosmic-bg-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let stars = [];
    const numStars = 300; // Un poco más de estrellas para mejor visibilidad

    // PALETA DE COLORES EXTRAÍDA DE TU TAILWIND CONFIG
    const PALETTE = {
        cosmicBg: '#03050a',      // cosmic.bg
        cosmicMid: '#0b1220',     // cosmic.mid
        gold: '#f59e0b',          // alchemy.gold
        cyan: '#00aeff',          // alchemy.cyan
        purple: '#a855f7',        // alchemy.purple
        starWhite: '#ffffff'
    };

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        initStars();
    }

    function initStars() {
        stars = [];
        for (let i = 0; i < numStars; i++) {
            // Asignación de colores usando los acentos de Tailwind (Alchemy Gold, Cyan y Blanco)
            let colorChoice = PALETTE.starWhite;
            const rand = Math.random();
            if (rand > 0.85) {
                colorChoice = PALETTE.cyan;  // #00aeff
            } else if (rand > 0.7) {
                colorChoice = PALETTE.gold;  // #f59e0b
            }

            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: Math.random() * 1.8 + 0.4, // Estrellas ligeramente más visibles
                alpha: Math.random(),
                speed: Math.random() * 0.012 + 0.003,
                color: colorChoice
            });
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // 1. PASO 1: Dibujar el fondo base ultra oscuro (cosmic.bg)
        ctx.fillStyle = PALETTE.cosmicBg;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 2. PASO 2: Dibujar el resplandor nebular central
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2.5;
        const radius = Math.max(canvas.width, canvas.height) * 0.65;

        const gradient = ctx.createRadialGradient(centerX, centerY, 10, centerX, centerY, radius);
        gradient.addColorStop(0, 'rgba(49, 36, 89, 0.4)');   // Violeta alquímico místico
        gradient.addColorStop(0.45, 'rgba(11, 18, 32, 0.3)'); // cosmic.mid (#0b1220) con opacidad
        gradient.addColorStop(1, 'rgba(3, 5, 10, 0.95)');     // Gradiente hacia cosmic.bg

        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 3. PASO 3: Dibujar las estrellas AL FINAL (para que nada las tape)
        stars.forEach(star => {
            star.alpha += star.speed;
            if (star.alpha > 1 || star.alpha < 0) {
                star.speed = -star.speed;
            }

            ctx.save();
            // Aseguramos una opacidad mínima visible (no menor a 0.15)
            ctx.globalAlpha = Math.max(0.15, Math.abs(star.alpha));
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fillStyle = star.color;
            ctx.fill();
            ctx.restore();
        });

        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    animate();
});