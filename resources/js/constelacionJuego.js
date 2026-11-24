const coordenadasConstelacion = [
    {
        nombre: "Orión",
        posicion: [5.588, -5.39],
        lineas: [
            [7, 0], [7, 1],                    // cabeza -> hombros
            [0, 4], [1, 2],                    // hombros -> cinturón
            [2, 3], [3, 4],                    // cinturón
            [4, 5], [2, 6], [5, 6],            // piernas
            [3, 15],                           // espada (Hatysa)
            [1, 8], [8, 9], [9, 10], [10, 11], // escudo
            [0, 12], [12, 13], [13, 14]        // brazo con el garrote
        ]
    },
    {
        nombre: "Osa Mayor",
        posicion: [11.062, 61.75],
        lineas: [
            [0, 1], [1, 2], [2, 3], [3, 0],        // cuenco
            [3, 4], [4, 5], [5, 6],                // mango
            [0, 8], [8, 7],                        // cabeza (Dubhe -> Upsilon -> Muscida)
            [1, 9], [9, 10], [10, 11], [11, 12],   // pata delantera
            [2, 16], [16, 15], [15, 14], [14, 13], // pata trasera
            [15, 17], [17, 18]                     // Alulas
        ]
    },
    {
        nombre: "Casiopea",
        posicion: [0.153, 59.15],
        lineas: [
            [0, 1], [1, 2], [2, 3], [3, 4]         // la W
        ]
    },
    {
        nombre: "Leo",
        posicion: [10.139, 11.97],
        lineas: [
            [0, 5], [5, 1], [1, 6], [6, 7], [7, 4], // la Hoz
            [1, 2], [2, 3], [2, 8], [8, 3]          // lomo, cola y triángulo trasero
        ]
    }
];

function devolverElementosAlquimicos(nombre) {

    let color = '';
    let dibujo = '';
    let valores = [];

    if (nombre === 'Fuego') {
        color = 'var(--fuego)';
        dibujo = '<polygon points="50,12 90,84 10,84"/>';
        valores = [color, dibujo];

    } else if (nombre === 'Aire') {
        color = 'var(--aire)';
        dibujo = '<polygon points="50,12 90,84 10,84"/><path d="M23 60h54"/>';
        valores = [color, dibujo];

    } else if (nombre === 'Agua') {
        color = 'var(--agua)';
        dibujo = '<polygon points="10,16 90,16 50,88"/>';
        valores = [color, dibujo];

    } else if (nombre === 'Tierra') {
        color = 'var(--tierra)';
        dibujo = '<polygon points="10,16 90,16 50,88"/><path d="M26 44h48"/>';
        valores = [color, dibujo];
    }
    return valores;
}

function valoresElementoAlquimico(elementoQuimicoPrincipal) {
    let valor = [];

    for (const nombre of nombresElementosAlquimicos) {
        if ((elementoQuimicoPrincipal === "Hidrógeno") && nombre === "Agua") {

            valor = devolverElementosAlquimicos(nombre);
        } else if ((elementoQuimicoPrincipal === "Titanio" || elementoQuimicoPrincipal === "Hierro" || elementoQuimicoPrincipal === "Calcio") && nombre === "Tierra") {

            valor = devolverElementosAlquimicos(nombre);
        } else if (elementoQuimicoPrincipal === "Helio" && nombre === "Aire") {

            valor = devolverElementosAlquimicos(nombre);
        } else if (elementoQuimicoPrincipal === "Cromo" && nombre === "Fuego") {

            valor = devolverElementosAlquimicos(nombre);
        }
    }
    return valor;
}

function colorCuerpoCeleste(temperatura) {
    if (temperatura < 3700) return "#ff8a5c";
    if (temperatura < 5200) return "#ffb56b";
    if (temperatura < 6000) return "#ffe4a1";
    if (temperatura < 7500) return "#fff4e8";
    if (temperatura < 10000) return "#f8f7ff";
    if (temperatura < 30000) return "#cad8ff";
    return "#9bb0ff";
}

function esCuerpoCelestePrincipal(temperatura) {
    return temperatura !== 0;
}

function radio(magnitud) {
    let tamano = 12 - magnitud * 2.5;
    if (tamano < 3) {
        tamano = 3;
    }
    return tamano;
}

function parsearNumero(valor) {
    return Number(String(valor).replace(",", ".").trim());
}

function normalizarNombre(texto) {
    if (!texto) {
        return "";
    }
    return String(texto)
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
}

function buscarDatosCoordenadas(nombreConstelacion) {
    const objetivo = normalizarNombre(nombreConstelacion);

    for (let i = 0; i < coordenadasConstelacion.length; i++) {
        const item = coordenadasConstelacion[i];
        if (normalizarNombre(item.nombre) === objetivo) {
            return item;
        }
    }
    return null;
}

function proyectarCuerpos(cuerposCelestes, posicionCentro, ancho, alto, margenX, margenY) {
    if (!cuerposCelestes || cuerposCelestes.length === 0) {
        return [];
    }

    const validos = [];
    for (let i = 0; i < cuerposCelestes.length; i++) {
        const ar = parsearNumero(cuerposCelestes[i].ascension);
        const dec = parsearNumero(cuerposCelestes[i].declinacion);
        if (Number.isFinite(ar) && Number.isFinite(dec)) {
            validos.push({ indice: i, ar: ar, dec: dec });
        }
    }

    if (validos.length === 0) {
        return [];
    }

    // Centro de referencia: usa posicion [AR, Dec] de coordenadasConstelacion si existe
    const tieneCentro = Array.isArray(posicionCentro)
        && Number.isFinite(Number(posicionCentro[0]))
        && Number.isFinite(Number(posicionCentro[1]));

    const raCentro = tieneCentro
        ? Number(posicionCentro[0])
        : validos.reduce((acc, p) => acc + p.ar, 0) / validos.length;

    const decCentro = tieneCentro
        ? Number(posicionCentro[1])
        : validos.reduce((acc, p) => acc + p.dec, 0) / validos.length;

    const k = Math.cos((decCentro * Math.PI) / 180) || 1;

    // Proyección tangencial local con ajuste de cruce 0h / 24h
    const puntos = new Array(cuerposCelestes.length).fill(null);
    let xMin = Infinity;
    let xMax = -Infinity;
    let yMin = Infinity;
    let yMax = -Infinity;

    for (let i = 0; i < validos.length; i++) {
        const item = validos[i];
        let deltaAr = item.ar - raCentro;
        if (deltaAr > 12) deltaAr -= 24;
        if (deltaAr < -12) deltaAr += 24;

        // En el cielo, la Ascensión Recta crece hacia el Este (izquierda en pantalla)
        const x = -deltaAr * 15 * k;
        const y = -(item.dec - decCentro);

        puntos[item.indice] = { x: x, y: y };

        if (x < xMin) xMin = x;
        if (x > xMax) xMax = x;
        if (y < yMin) yMin = y;
        if (y > yMax) yMax = y;
    }

    const anchoFigura = xMax - xMin;
    const altoFigura = yMax - yMin;

    const areaUtilX = Math.max(ancho - 2 * margenX, 1);
    const areaUtilY = Math.max(alto - 2 * margenY, 1);

    let escala = 1;
    if (anchoFigura > 0 && altoFigura > 0) {
        escala = Math.min(areaUtilX / anchoFigura, areaUtilY / altoFigura);
    } else if (anchoFigura > 0) {
        escala = areaUtilX / anchoFigura;
    } else if (altoFigura > 0) {
        escala = areaUtilY / altoFigura;
    }

    const offsetX = anchoFigura > 0 ? (ancho - anchoFigura * escala) / 2 : ancho / 2;
    const offsetY = altoFigura > 0 ? (alto - altoFigura * escala) / 2 : alto / 2;

    const posiciones = new Array(cuerposCelestes.length).fill(null);
    for (let i = 0; i < puntos.length; i++) {
        if (!puntos[i]) continue;
        posiciones[i] = {
            x: Number((offsetX + (puntos[i].x - xMin) * escala).toFixed(2)),
            y: Number((offsetY + (puntos[i].y - yMin) * escala).toFixed(2))
        };
    }

    return posiciones;
}

function dibujarFormaConstelacion(constelacion, colocadas) {
    const ancho = 600;
    const alto = 440;
    const cuerpos = constelacion.cuerposCelestes || [];
    const lineas = constelacion.lineas || [];
    const estadoColocadas = colocadas || {};

    const posiciones = proyectarCuerpos(cuerpos, constelacion.posicion, ancho, alto, 80, 45);
    if (posiciones.length === 0) {
        return "";
    }

    let svg = '<svg viewBox="0 0 ' + ancho + ' ' + alto + '" width="100%" height="100%">';

    // 1. Líneas entre pares de cuerpos celestes [origen, destino]
    for (let i = 0; i < lineas.length; i++) {
        const par = lineas[i];
        const origen = Number(par[0]);
        const destino = Number(par[1]);

        if (!posiciones[origen] || !posiciones[destino] || !cuerpos[origen] || !cuerpos[destino]) {
            continue;
        }

        const listoOrigen = !cuerpos[origen].esPrincipal || estadoColocadas[origen] === true;
        const listoDestino = !cuerpos[destino].esPrincipal || estadoColocadas[destino] === true;

        if (listoOrigen && listoDestino) {
            svg += '<line x1="' + posiciones[origen].x + '" y1="' + posiciones[origen].y + '"'
                + ' x2="' + posiciones[destino].x + '" y2="' + posiciones[destino].y + '"'
                + ' stroke="#e8cf8a" stroke-width="1.6" opacity="0.9"/>';
        } else {
            svg += '<line x1="' + posiciones[origen].x + '" y1="' + posiciones[origen].y + '"'
                + ' x2="' + posiciones[destino].x + '" y2="' + posiciones[destino].y + '"'
                + ' stroke="#3a4468" stroke-width="1" opacity="0.5" stroke-dasharray="3 4"/>';
        }
    }

    // 2. Puntos / estrellas en sus posiciones proyectadas
    for (let i = 0; i < cuerpos.length; i++) {
        if (!posiciones[i]) continue;

        const cuerpo = cuerpos[i];
        const x = posiciones[i].x;
        const y = posiciones[i].y;
        const tamano = radio(cuerpo.magnitud);

        // Estrellas secundarias de estructura (temperatura === 0)
        if (!cuerpo.esPrincipal) {
            svg += '<circle cx="' + x + '" cy="' + y + '" r="' + Math.max(tamano * 0.55, 2) + '"'
                + ' fill="#cfd8f5" opacity="0.8"/>';
            continue;
        }

        // Estrellas principales interactivas
        svg += '<g class="node" data-i="' + i + '" data-id="' + (cuerpo.id || "") + '" style="cursor:pointer">';
        if (estadoColocadas[i] === true) {
            const colorEstrella = colorCuerpoCeleste(cuerpo.temperatura) || "#ffffff";
            svg += '<circle cx="' + x + '" cy="' + y + '" r="' + tamano + '" fill="' + colorEstrella + '"/>';
            svg += '<text x="' + (x + tamano + 10) + '" y="' + (y + 3) + '" fill="#e8ecff" font-size="12">'
                + (cuerpo.nombre || "") + '</text>';
        } else {
            svg += '<circle class="hueco" cx="' + x + '" cy="' + y + '" r="' + (tamano + 8) + '"'
                + ' fill="transparent" stroke="#aab8e8" stroke-dasharray="3 3"/>';
            svg += '<circle cx="' + x + '" cy="' + y + '" r="2.5" fill="#aab8e8"/>';
        }
        svg += '</g>';
    }

    svg += '</svg>';
    return svg;
}

const CLAVE_GUARDADO = "constelaciones_guardadas";
let guardadas = JSON.parse(localStorage.getItem(CLAVE_GUARDADO) || "{}");

function mostrarPistas(indiceI, cont, constelacion) {
    const target = constelacion || constelacionesData[indiceActual];
    if (!target || !target.cuerposCelestes[indiceI]) return;

    const cuerpoCeleste = target.cuerposCelestes[indiceI];
    const contenedorPistas = target.elementoDOM
        ? target.elementoDOM.querySelector(".fb")
        : document.querySelector(".fb");
    if (!contenedorPistas) return;

    let elementoPrincipal = "";
    let elementoSecundario = "";
    let elementoTerciario = "";

    (cuerpoCeleste.elementos || []).forEach(function (item) {
        const orden = item.pivot ? Number(item.pivot.orden) : null;
        if (orden === 1) elementoPrincipal = item.nombre;
        else if (orden === 2) elementoSecundario = item.nombre;
        else if (orden === 3) elementoTerciario = item.nombre;
    });

    const descubrio = cuerpoCeleste.descubridor
        || (cuerpoCeleste.elementoDOM && cuerpoCeleste.elementoDOM.dataset.descubridor)
        || "";
    const fechaDescubierta = cuerpoCeleste.anyoDescubierto
        || (cuerpoCeleste.elementoDOM && (cuerpoCeleste.elementoDOM.dataset.anyodescubierto || cuerpoCeleste.elementoDOM.dataset.anyoDescubierto))
        || "";

    const val = valoresElementoAlquimico(elementoPrincipal);
    const paso = ((cont - 1) % 6) + 1;

    if (paso === 1) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Su elemento principal y secundario son: ${elementoPrincipal} -- ${elementoSecundario}</span>`;
    } else if (paso === 2) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Fue descubierto por: ${descubrio} en el año ${fechaDescubierta}</span>`;
    } else if (paso === 3) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Su elemento secundario y principal son: ${elementoSecundario} -- ${elementoPrincipal}</span>`;
    } else if (paso === 4) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Su temperatura es: ${cuerpoCeleste.temperatura} y su elemento Alquímico es: ${val[2] || ""}</span>`;
    } else if (paso === 5) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Su elemento terciario y principal son: ${elementoTerciario} -- ${elementoPrincipal}</span>`;
    } else if (paso === 6) {
        contenedorPistas.innerHTML =
            `<h2>Pista</h2>
             <span class="texto">Su elemento secundario y terciario son: ${elementoSecundario} -- ${elementoTerciario}</span>`;
    }
}

function comprobarVictoria(totalPrincipales, constelacion) {
    const target = constelacion || constelacionesData[indiceActual];
    if (!target) return;

    const colocadas = target.colocadas || {};

    if (Object.keys(colocadas).length === totalPrincipales) {
        const descripcion = target.descripcionMitologica
            || (target.elementoDOM && (target.elementoDOM.dataset.descripcionMitologica || target.elementoDOM.dataset.descripcion))
            || "";

        // Copia serializable (sin referencias al DOM)
        guardadas[target.id] = {
            id: target.id,
            nombre: target.nombre,
            descripcion: descripcion,
            posicion: target.posicion,
            lineas: target.lineas,
            cuerposCelestes: target.cuerposCelestes.map(function (c) {
                return {
                    id: c.id,
                    nombre: c.nombre,
                    temperatura: c.temperatura,
                    declinacion: c.declinacion,
                    ascension: c.ascension,
                    magnitud: c.magnitud,
                    esPrincipal: c.esPrincipal,
                    elementos: c.elementos,
                    anyoDescubierto: c.anyoDescubierto,
                    descubridor: c.descubridor
                };
            })
        };

        if (target.elementoDOM) {
            target.elementoDOM.dataset.completada = "true";
        }

        try {
            localStorage.setItem(CLAVE_GUARDADO, JSON.stringify(guardadas));
        } catch (error) {
            console.error("Error al guardar:", error);
        }

        const contenedorPistas = target.elementoDOM ? target.elementoDOM.querySelector(".fb") : document.querySelector(".fb");
        if (contenedorPistas) {
            contenedorPistas.innerHTML = `<h2>Sabías que:</h2><p>${descripcion}</p>`;
        }

        const btnNext = target.elementoDOM ? target.elementoDOM.querySelector(".next") : document.querySelector(".next");

        if (btnNext) {
            btnNext.disabled = false;

            function reiniciarJuego() {
                console.log("Juego reiniciado");
            }

            // const btnReset = document.querySelector(".reset");
            // btnNext.addEventListener("click", reiniciarJuego);

            btnNext.onclick = function () {
                target.elementoDOM.style.display = "none";
                indiceActual++;

                if (indiceActual < constelacionesData.length) {
                    const siguiente = constelacionesData[indiceActual];
                    siguiente.elementoDOM.style.display = "";
                    const nivel = siguiente.elementoDOM.querySelector(".texto");
                    if (nivel) nivel.textContent = "Nivel " + (indiceActual + 1);
                    renderizarConstelacion(siguiente);
                } else {
                    const lista = document.getElementById("lista-constelaciones");
                    if (lista) {
                        lista.innerHTML = "<div class='panel'><h2>¡Enhorabuena!</h2><p>Has completado todas las constelaciones.</p></div>";
                    }
                }
            }
            btnNext.addEventListener("click", c);

            btnNext.onclick = function () {
                target.elementoDOM.style.display = "none";
                indiceActual++;

                if (indiceActual < constelacionesData.length) {
                    const siguiente = constelacionesData[indiceActual];
                    siguiente.elementoDOM.style.display = "";
                    const nivel = siguiente.elementoDOM.querySelector(".texto");
                    if (nivel) nivel.textContent = "Nivel " + (indiceActual + 1);
                    renderizarConstelacion(siguiente);
                } else {
                    const lista = document.getElementById("lista-constelaciones");
                    if (lista) {
                        lista.innerHTML = "<div class='panel'><h2>¡Enhorabuena!</h2><p>Has completado todas las constelaciones.</p></div>";
                    }
                }
            };
        }
    }
}

function renderizarConstelacion(constelacion) {
    if (!constelacion || !constelacion.elementoDOM) return;

    const contenedorSky = constelacion.elementoDOM.querySelector(".sky");
    const contenedorFb = constelacion.elementoDOM.querySelector(".fb");
    if (!contenedorSky) return;

    if (!constelacion.colocadas) {
        constelacion.colocadas = {};
    }

    contenedorSky.innerHTML = dibujarFormaConstelacion(constelacion, constelacion.colocadas);

    const totalPrincipales = constelacion.cuerposCelestes.filter(c => c.esPrincipal).length;
    const puntos = contenedorSky.querySelectorAll(".node");
    let cont = 0;

    for (const nodo of puntos) {
        const indice = Number(nodo.dataset.i);

        if (constelacion.colocadas[indice]) continue;

        nodo.ondragover = function (e) {
            e.preventDefault();
        };

        nodo.ondrop = function (e) {
            e.preventDefault();
            const piezaIndice = Number(e.dataTransfer.getData("text/plain"));

            if (piezaIndice === indice) {
                constelacion.colocadas[indice] = true;

                const cuerpoDOM = constelacion.cuerposCelestes[piezaIndice].elementoDOM;
                const tarjeta = cuerpoDOM.closest(".card_cuerpo_celeste") || cuerpoDOM;
                tarjeta.remove();

                renderizarConstelacion(constelacion);

                if (contenedorFb) {
                    contenedorFb.innerHTML = "<h2>Genial</h2><p>¡Vamos a por otra!</p>";
                }

                comprobarVictoria(totalPrincipales, constelacion);
            } else if (contenedorFb) {
                contenedorFb.innerHTML = "<h2>Ups</h2><p>Esa estrella no va ahí. ¡Prueba con otra!</p>";
            }
        };

        nodo.onclick = function () {
            cont++;

            const puntoSeleccionado = contenedorSky.querySelector(".node.seleccionado");
            if (puntoSeleccionado !== null) {
                puntoSeleccionado.classList.remove("seleccionado");
            }

            nodo.classList.add("seleccionado");
            mostrarPistas(indice, cont, constelacion);
        };
    }
}

const nombresElementosAlquimicos = [];

document.querySelectorAll('.elemento_alquimico').forEach(function (elementAlquimico) {
    const nombre = elementAlquimico.dataset.nombre;
    nombresElementosAlquimicos.push(nombre);
    let valores = devolverElementosAlquimicos(nombre);
    elementAlquimico.style.setProperty('--color', valores[0]);

    const iconoSvg = elementAlquimico.querySelector('.elemento-icono');
    if (iconoSvg) {
        iconoSvg.innerHTML = valores[1];
    }
});

const constelacionesData = [];
const nombreConstelaciones = [];
const constelacionItems = document.querySelectorAll('.constelacion-item');
const indexConstelacion = [];

constelacionItems.forEach(function (constelacionItem, indiceBucle) {

    const idConstelacion = constelacionItem.dataset.id;
    const nombreConstelacion = constelacionItem.dataset.nombre;
    const indiceConstelacion = Number(constelacionItem.dataset.indice);
    indexConstelacion.push(indiceConstelacion);

    if (nombreConstelacion && nombreConstelaciones.indexOf(nombreConstelacion) === -1) {
        nombreConstelaciones.push(nombreConstelacion);
    }

    const cuerposDeConstelacion = [];
    const cuerposCelestes = constelacionItem.querySelectorAll('.cuerpo_celeste');

    if (indiceBucle === 0) {
        constelacionItem.style.display = "";
    } else {
        constelacionItem.style.display = "none";
    }

    cuerposCelestes.forEach(function (cuerpoCeleste, indiceCuerpo) {

        const temperaturaTexto = cuerpoCeleste.dataset.temperatura;
        const declinacionTexto = cuerpoCeleste.dataset.declinacion;
        const ascensionTexto = cuerpoCeleste.dataset.ascension;
        const magnitudTexto = cuerpoCeleste.dataset.magnitud;
        const anyoDescubierto = cuerpoCeleste.dataset.anyodescubierto;
        const descubridor = cuerpoCeleste.dataset.descubridor;

        const declinacion = Number(declinacionTexto)
        const ascension = Number(ascensionTexto);
        const magnitud = Number(magnitudTexto);

        const temperatura = Number(temperaturaTexto);
        const nombre = cuerpoCeleste.dataset.nombre;
        const id = cuerpoCeleste.dataset.id;

        const esPrincipal = esCuerpoCelestePrincipal(temperatura);
        const card = cuerpoCeleste.closest('.card_cuerpo_celeste');

        cuerpoCeleste.draggable = true;

        let elementos = [];
        if (cuerpoCeleste.dataset.elementos) {
            elementos = JSON.parse(cuerpoCeleste.dataset.elementos);
        }

        cuerposDeConstelacion.push({
            id: id,
            nombre: nombre,
            temperatura: temperatura,
            declinacion: declinacion,
            ascension: ascension,
            magnitud: magnitud,
            esPrincipal: esPrincipal,
            elementos: elementos,
            elementoDOM: cuerpoCeleste
        });


        if (esPrincipal) {

            const piezaArrastrable = card || cuerpoCeleste;
            piezaArrastrable.draggable = true;

            piezaArrastrable.ondragstart = function (e) {
                e.dataTransfer.setData("text/plain", String(indiceCuerpo));
            };


            const info = cuerpoCeleste.querySelector('.small');

            const orb = cuerpoCeleste.querySelector('.orb');

            if (orb) {
                const temperaturaColor = colorCuerpoCeleste(temperatura);
                orb.style.setProperty('--c', temperaturaColor);
            }

            if (info) {
                let nombreQuimicoPrincipal = '';
                let nombreQuimicoSecundario = '';
                let nombreQuimicoTerciario = '';
                let valor = [];
                elementos.forEach(function (item) {
                    const orden = item.pivot ? Number(item.pivot.orden) : null;


                    if (orden === 1) {
                        nombreQuimicoPrincipal = item.nombre;

                        valor = valoresElementoAlquimico(nombreQuimicoPrincipal);


                    } else if (orden === 2) {
                        nombreQuimicoSecundario = item.nombre;
                    } else if (orden === 3) {
                        nombreQuimicoTerciario = item.nombre;
                    }

                    info.innerHTML = `<h3> ${nombre}  </h3>
                                  <h3>${temperatura}</h3>
                                  <h3>${nombreQuimicoPrincipal}</h3>
                                  <svg viewBox="0 0 100 100" width="50" height="50" stroke="currentColor" style='color:${valor[0] || ""}' fill="none" stroke-width="6">${valor[1] || ""}</svg>`;

                    let clicki = false;

                    cuerpoCeleste.onclick = function () {
                        const infoExtra = cuerpoCeleste.querySelector('.info-extra');

                        if (!clicki) {
                            infoExtra.innerHTML = `<span class="">Datos extra: <br>Elemento principal: ${nombreQuimicoPrincipal},  secundario: ${nombreQuimicoSecundario}, terciario: ${nombreQuimicoTerciario}, descubierto en: ${anyoDescubierto}, por ${descubridor}</span>`;
                            clicki = true;
                        } else {
                            infoExtra.innerHTML = "";
                            clicki = false;
                        }
                    };
                }
                );
            }
        } else {
            if (card) {
                card.style.display = 'none';
            }
        }
    });

    const datosCoord = buscarDatosCoordenadas(nombreConstelacion);

    const objetoConstelacion = {
        id: idConstelacion,
        nombre: nombreConstelacion,
        indice: indiceConstelacion,
        posicion: datosCoord ? datosCoord.posicion : null,
        lineas: datosCoord ? datosCoord.lineas : [],
        cuerposCelestes: cuerposDeConstelacion,
        elementoDOM: constelacionItem
    };

    constelacionesData.push(objetoConstelacion);


});

let indiceActual = 0;
if (constelacionesData.length > 0) {
    renderizarConstelacion(constelacionesData[indiceActual]);
}