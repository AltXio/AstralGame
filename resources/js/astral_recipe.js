//Estrellas fondo de pantalla
const canvas = document.getElementById("stars");

if (canvas) {
    const ctx = canvas.getContext("2d");
    let stars = [];
    let W, H;

    function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;

        stars = Array.from({ length: 150 }, () => ({
            x: Math.random() * W,
            y: Math.random() * H,
            r: Math.random() * 1.5,
            phase: Math.random() * Math.PI * 2,
            speed: Math.random() * 0.02,
        }));
    }

    function animate() {
        ctx.clearRect(0, 0, W, H);

        stars.forEach((star) => {
            star.phase += star.speed;

            ctx.globalAlpha = 0.3 + Math.abs(Math.sin(star.phase)) * 0.7;

            ctx.fillStyle = "#fff";
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.r, 0, Math.PI * 2);
            ctx.fill();
        });

        requestAnimationFrame(animate);
    }

    window.addEventListener("resize", resize);

    resize();
    animate();
}

//Modal Guia
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('guia-modal');
    const openBtn = document.getElementById('open-guia');
    const closeBtn = document.getElementById('close-guia');

    if (openBtn && modal) {
        openBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    }
});
