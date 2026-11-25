// Estrellas fondo de pantalla
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
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("guia-modal");
    const openBtn = document.getElementById("open-guia");
    const closeBtn = document.getElementById("close-guia");

    if (openBtn && modal) {
        openBtn.addEventListener("click", () => {
            modal.classList.remove("hidden");
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener("click", () => {
            modal.classList.add("hidden");
        });
    }

    // Logica joc
    if (!window.GAME_DATA) return;

    const {
        recetas,
        signos,
        assetUrl,
        nivelActualId,
        urlCompletar,
        urlSesion,
        urlResultado,
    } = window.GAME_DATA;

    // Variables d'estat del joc i sessió
    let indiceRecetaActual = 0;
    let puntuacion = 0;
    let bloqueado = false;
    let sesionId = null;
    let errores = 0;
    let intentos = 0;

    // Elements del DOM
    const elTitulo = document.getElementById("receta-titulo");
    const elDescripcion = document.getElementById("receta-descripcion");
    const elNodeElement = document.getElementById("node-element");
    const elImgElement = document.getElementById("img-element");
    const elSymbolMetal = document.getElementById("symbol-metal");
    const elDropZone = document.getElementById("drop-zone");
    const elDropText = document.getElementById("drop-zone-text");
    const elCardsContainer = document.getElementById("cards-container");
    const elScore = document.getElementById("score");

    // Mapeig de colors segons l'element alquímic
    const estilosElemento = {
        Fuego: "border-red-400 from-red-500/20 to-red-900/20",
        Tierra: "border-emerald-400 from-emerald-500/20 to-emerald-900/20",
        Aire: "border-amber-300 from-amber-500/20 to-amber-900/20",
        Agua: "border-blue-400 from-blue-500/20 to-blue-900/20",
    };

    function headersJson() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                ?.content,
        };
    }

    // 1. Carregar la recepta actual a la pantalla
    function cargarReceta(indice) {
        if (indice >= recetas.length) {
            mostrarVictoria();
            return;
        }

        bloqueado = false;
        const receta = recetas[indice];
        const idSignoReceta = Number(receta.id_signo);

        const signoCompleto =
            signos.find((s) => Number(s.id ?? s.id_signo) === idSignoReceta) ||
            receta.signo;
        const elementoNombre =
            signoCompleto?.elemento_alquimico?.nombre || "Aire";

        // Actualitzar textos
        if (elTitulo) elTitulo.textContent = receta.titulo;
        if (elDescripcion) elDescripcion.textContent = receta.descripcion;

        // NIVELL ALQUIMISTA (ID 2): Amagar l'element al cercle esquerre
        if (elImgElement && elNodeElement) {
            const prevUnknown = elNodeElement.querySelector(".unknown-element");
            if (prevUnknown) prevUnknown.remove();

            if (Number(nivelActualId) === 2) {
                elImgElement.classList.add("hidden");
                const spanUnknown = document.createElement("span");
                spanUnknown.className =
                    "unknown-element text-[clamp(2rem,7vw,3.2rem)] leading-none text-indigo-100";
                spanUnknown.textContent = "?";
                elNodeElement.prepend(spanUnknown);
            } else {
                elImgElement.classList.remove("hidden");
                elImgElement.src = `${assetUrl}images/elementos/${elementoNombre.toLowerCase()}.svg`;
            }
        }

        // Metall al cercle dret
        if (elSymbolMetal) {
            elSymbolMetal.textContent =
                receta.elemento_quimico?.simbolo ||
                signoCompleto?.simbolo_metal ||
                "?";
        }

        // Reset zona central
        if (elDropText) elDropText.textContent = "Zona de tu receta";
        if (elDropZone) {
            elDropZone.classList.remove(
                "border-emerald-400",
                "bg-emerald-500/20",
                "border-red-500",
            );
        }

        generarCartas(signoCompleto);
    }

    // 2. Generar la carta correcta + 4 d'aleatòries (5 cartes en total)
    function generarCartas(signoCorrecto) {
        if (!elCardsContainer || !signoCorrecto) return;
        elCardsContainer.innerHTML = "";

        const idCorrecto = Number(signoCorrecto.id ?? signoCorrecto.id_signo);

        const otrosSignos = signos
            .filter((s) => Number(s.id ?? s.id_signo) !== idCorrecto)
            .sort(() => Math.random() - 0.5)
            .slice(0, 4);

        const cartasRonda = [signoCorrecto, ...otrosSignos].sort(
            () => Math.random() - 0.5,
        );

        // NIVELL MAESTRO (ID 3): No mostrar els metalls a les cartes
        const esNivelMaestro = Number(nivelActualId) === 3;

        cartasRonda.forEach((signo) => {
            const idActual = Number(signo.id ?? signo.id_signo);
            const elemNombre = signo.elemento_alquimico?.nombre || "";
            const colorClasses =
                estilosElemento[elemNombre] ||
                "border-indigo-400 from-indigo-500/20 to-indigo-900/20";

            const card = document.createElement("div");
            card.draggable = true;
            card.dataset.signoId = idActual;
            card.className = `w-32 h-44 rounded-2xl border ${colorClasses} bg-gradient-to-b flex flex-col items-center ${esNivelMaestro ? "justify-center gap-4" : "justify-between"} p-4 cursor-grab active:cursor-grabbing hover:-translate-y-2 transition select-none`;

            const metalHTML = esNivelMaestro
                ? ""
                : `<span class="text-xs text-slate-300 pointer-events-none">${signo.simbolo_metal || ""} ${signo.nombre_metal || ""}</span>`;

            card.innerHTML = `
                <img src="${assetUrl}images/signos/${signo.nombre}.svg" 
                    alt="${signo.nombre}" 
                    class="w-14 h-14 object-contain pointer-events-none brightness-0 invert">
                <span class="pointer-events-none font-medium">${signo.nombre}</span>
                ${metalHTML}
            `;

            // Events d'arrossegar
            card.addEventListener("dragstart", (e) => {
                if (bloqueado) {
                    e.preventDefault();
                    return;
                }
                e.dataTransfer.effectAllowed = "move";
                e.dataTransfer.setData("text/plain", String(idActual));
                card.classList.add("opacity-50");
            });

            card.addEventListener("dragend", () => {
                card.classList.remove("opacity-50");
            });

            elCardsContainer.appendChild(card);
        });
    }

    // 3. Configurar la zona central (Drop Zone)
    if (elDropZone) {
        elDropZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = "move";
            if (!bloqueado) {
                elDropZone.classList.add("scale-105", "border-amber-300");
            }
        });

        elDropZone.addEventListener("dragleave", () => {
            elDropZone.classList.remove("scale-105", "border-amber-300");
        });

        elDropZone.addEventListener("drop", (e) => {
            e.preventDefault();
            elDropZone.classList.remove("scale-105", "border-amber-300");

            if (bloqueado) return;

            const idSignoSoltado = parseInt(
                e.dataTransfer.getData("text/plain"),
                10,
            );
            if (!isNaN(idSignoSoltado)) {
                comprobarRespuesta(idSignoSoltado);
            }
        });
    }

    // 4. Comprovar si la carta és la correcta
    function comprobarRespuesta(idSignoSoltado) {
        const recetaActual = recetas[indiceRecetaActual];
        const idEsperado = Number(recetaActual.id_signo);
        intentos++;

        if (Number(idSignoSoltado) === idEsperado) {
            // ENCERT!
            bloqueado = true;
            puntuacion += 100;
            if (elScore) elScore.textContent = puntuacion;

            guardarReceta(recetaActual.id);

            elDropZone.classList.add("border-emerald-400", "bg-emerald-500/20");
            elDropText.innerHTML = `<span class="text-emerald-300 font-semibold">¡Correcto!</span>`;

            setTimeout(() => {
                indiceRecetaActual++;
                cargarReceta(indiceRecetaActual);
            }, 1200);
        } else {
            // ERROR: resta 10 punts (sense baixar de 0) i suma 1 error
            errores++;
            puntuacion = Math.max(0, puntuacion - 10);
            if (elScore) elScore.textContent = puntuacion;

            elDropZone.classList.add("border-red-500", "animate-bounce");
            elDropText.innerHTML = `<span class="text-red-400">Elementos inestables... -10</span>`;

            setTimeout(() => {
                elDropZone.classList.remove("border-red-500", "animate-bounce");
                elDropText.textContent = "Zona de tu receta";
            }, 1000);
        }
    }

    function mostrarVictoria() {
        enviarResultado(true);
        if (elTitulo) elTitulo.textContent = "¡Nivel Completado!";
        if (elDescripcion)
            elDescripcion.textContent = `Has transmutado todas las recetas con éxito. Puntuación final: ${puntuacion}`;
        if (elCardsContainer) elCardsContainer.innerHTML = "";
        if (elDropText) elDropText.textContent = "✦ Maestría Alquímica ✦";
    }

    // Guardar recepta completada a la BDD
    function guardarReceta(idReceta) {
        fetch(urlCompletar, {
            method: "POST",
            headers: headersJson(),
            body: JSON.stringify({
                id_receta: idReceta,
                id_nivel: nivelActualId,
            }),
        }).catch((err) =>
            console.error("No s'ha pogut guardar la recepta", err),
        );
    }

    // Iniciar sessió de joc a la BDD
    async function iniciarSesion() {
        try {
            const res = await fetch(urlSesion, {
                method: "POST",
                headers: headersJson(),
                body: JSON.stringify({ id_juego: 3, id_nivel: nivelActualId }),
            });
            const data = await res.json();
            sesionId = data.id;
        } catch (err) {
            console.error("No s'ha pogut iniciar la sessió", err);
        }
    }

    // Avís d'activitat cada minut per mantenir duracion_segundos actualitzada
    setInterval(() => {
        if (!sesionId) return;
        fetch(`${urlSesion}/${sesionId}`, {
            method: "PATCH",
            headers: headersJson(),
        }).catch(() => {});
    }, 60000);

    // Enviar resultat final de la partida
    function enviarResultado(completada) {
        if (!sesionId) return;
        fetch(`${urlResultado}/${sesionId}`, {
            method: "PATCH",
            headers: headersJson(),
            body: JSON.stringify({
                puntuacion: puntuacion,
                errores: errores,
                intentos: intentos,
                completada: completada,
            }),
        }).catch((err) =>
            console.error("No s'ha pogut guardar el resultat", err),
        );
    }

    // Iniciar el joc només si hi ha receptes disponibles
    if (recetas && recetas.length > 0) {
        iniciarSesion();
        cargarReceta(0);
    } else if (elTitulo) {
        elTitulo.textContent = "No hay recetas disponibles";
    }
});