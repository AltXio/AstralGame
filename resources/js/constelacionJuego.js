const elemtosAlquimicos = [
    { nombre: "Fuego", color: "var(--fuego)", svg: '<polygon points="50,12 90,84 10,84"/>' },
    { nombre: "Agua", color: "var(--agua)", svg: '<polygon points="10,16 90,16 50,88"/>' },
    { nombre: "Tierra", color: "var(--tierra)", svg: '<polygon points="10,16 90,16 50,88"/><path d="M26 44h48"/>' },
    { nombre: "Aire", color: "var(--aire)", svg: '<polygon points="50,12 90,84 10,84"/><path d="M23 60h54"/>' }];

const constelaciones = [
    {
        id: "orion",
        nombre: "Orión",
        alias: "El cazador",
        "descripcion mitologica": "Según una versión del mito griego, Orión era un cazador gigante que presumía de poder matar a cualquier animal de la Tierra. Gea, la Tierra, envió un escorpión que lo picó. Zeus colocó a ambos en el cielo en lados opuestos: cuando Escorpio sale por el este, Orión se esconde por el oeste, como si huyera de él.",
        "descripcion cientifica": "Su cinturón de tres estrellas alineadas es de lo más fácil de reconocer. Debajo cuelga la espada con la Nebulosa de Orión, una guardería de estrellas visible a simple vista. El brazo levantado sostiene el garrote y el arco de la izquierda es el escudo.",
        posicion: [5.588, -5.39],
        cuerpoCelestes: [
            { nombre: "Betelgeuse", ascensionRecta: 5.919, declinacion: 7.41, magnitud: 0.5, temperatura: 3600, elementoPrincipal: "Titanio", elementoSecundario: "Oxígeno", elementoTerciario: "Vanadio", fechaDescubierta: "1836", descubrio: "John Herschel", descripcion: "Es una supergigante roja tan grande que, en el lugar del Sol, su superficie llegaría más allá de Marte. Algún día explotará como supernova." },
            { nombre: "Bellatrix", ascensionRecta: 5.419, declinacion: 6.35, magnitud: 1.64, temperatura: 22000, elementoPrincipal: "Helio", elementoSecundario: "Silicio", elementoTerciario: "Oxígeno", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Su nombre significa «la guerrera» en latín. Es una gigante azul muy caliente." },
            { nombre: "Mintaka", ascensionRecta: 5.533, declinacion: -0.30, magnitud: 2.25, temperatura: 29500, elementoPrincipal: "Helio", elementoSecundario: "Carbono", elementoTerciario: "Nitrógeno", fechaDescubierta: "1900", descubrio: "Johannes Hartmann", descripcion: "Está casi sobre el ecuador celeste, por eso se ve desde casi todo el planeta. En realidad es un sistema de varias estrellas." },
            { nombre: "Alnilam", ascensionRecta: 5.604, declinacion: -1.20, magnitud: 1.69, temperatura: 27500, elementoPrincipal: "Helio", elementoSecundario: "Nitrógeno", elementoTerciario: "Silicio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es la estrella central del cinturón: una supergigante azul a unos 2 000 años luz." },
            { nombre: "Alnitak", ascensionRecta: 5.679, declinacion: -1.94, magnitud: 1.74, temperatura: 33000, elementoPrincipal: "Helio", elementoSecundario: "Hidrógeno", elementoTerciario: "Carbono", fechaDescubierta: "1819", descubrio: "William Herschel", descripcion: "Es un sistema triple. Cerca de ella están la Nebulosa de la Llama y la de la Cabeza de Caballo." },
            { nombre: "Saiph", ascensionRecta: 5.796, declinacion: -9.67, magnitud: 2.09, temperatura: 26500, elementoPrincipal: "Helio", elementoSecundario: "Magnesio", elementoTerciario: "Silicio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Su nombre árabe significa «espada del gigante». Es una supergigante azul." },
            { nombre: "Rigel", ascensionRecta: 5.242, declinacion: -8.20, magnitud: 0.13, temperatura: 12100, elementoPrincipal: "Hidrógeno", elementoSecundario: "Magnesio", elementoTerciario: "Hierro", fechaDescubierta: "1831", descubrio: "F. G. W. Struve", descripcion: "Es una supergigante azul decenas de miles de veces más luminosa que el Sol." },
            { nombre: "Meissa", ascensionRecta: 5.585, declinacion: 9.93, magnitud: 3.54, temperatura: 36000, elementoPrincipal: "Helio", elementoSecundario: "Hidrógeno", elementoTerciario: "Nitrógeno", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Forma la cabeza del cazador. Es la más caliente de la constelación." },
            { nombre: "Pi3 Orionis", ascensionRecta: 4.830, declinacion: 6.96, magnitud: 3.19, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Pi4 Orionis", ascensionRecta: 4.853, declinacion: 5.61, magnitud: 3.69, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Pi5 Orionis", ascensionRecta: 4.904, declinacion: 2.44, magnitud: 3.7, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Pi6 Orionis", ascensionRecta: 4.976, declinacion: 1.71, magnitud: 4.47, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Mu Orionis", ascensionRecta: 6.040, declinacion: 9.64, magnitud: 4.12, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Nu Orionis", ascensionRecta: 6.126, declinacion: 14.77, magnitud: 4.42, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Xi Orionis", ascensionRecta: 6.199, declinacion: 14.21, magnitud: 4.48, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Hatysa", ascensionRecta: 5.590, declinacion: -5.91, magnitud: 2.77, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." }
        ],
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
        id: "uma",
        nombre: "Osa Mayor",
        alias: "La gran osa",
        "descripcion mitologica": "En el mito griego, Calisto era una ninfa seguidora de Artemisa. Zeus se enamoró de ella y la celosa Hera la convirtió en osa. Años después, su hijo Arcas, ya cazador, estuvo a punto de matarla sin reconocerla. Zeus lo impidió y puso a los dos en el cielo: ella como la Osa Mayor y él como el Boyero.",
        "descripcion cientifica": "El Carro (siete estrellas) es solo una parte de la osa: el cuenco es el cuerpo, el mango es la cola, y las estrellas tenues forman la cabeza y las patas. Dubhe y Merak apuntan hacia la Estrella Polar.",
        posicion: [11.062, 61.75],
        cuerpoCelestes: [
            { nombre: "Dubhe", ascensionRecta: 11.062, declinacion: 61.75, magnitud: 1.79, temperatura: 4660, elementoPrincipal: "Calcio", elementoSecundario: "Hierro", elementoTerciario: "Magnesio", fechaDescubierta: "1889", descubrio: "Hermann Carl Vogel", descripcion: "Es una gigante naranja. Su nombre significa «oso» en árabe. Con Merak señala a la Estrella Polar." },
            { nombre: "Merak", ascensionRecta: 11.031, declinacion: 56.38, magnitud: 2.37, temperatura: 9380, elementoPrincipal: "Hidrógeno", elementoSecundario: "Magnesio", elementoTerciario: "Calcio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Forma parte de un grupo de estrellas que viajan juntas por el espacio, junto con Phecda, Megrez, Alioth y Mizar." },
            { nombre: "Phecda", ascensionRecta: 11.897, declinacion: 53.69, magnitud: 2.44, temperatura: 9500, elementoPrincipal: "Hidrógeno", elementoSecundario: "Calcio", elementoTerciario: "Hierro", fechaDescubierta: "1804", descubrio: "William Herschel", descripcion: "Su nombre árabe significa «muslo» del oso." },
            { nombre: "Megrez", ascensionRecta: 12.257, declinacion: 57.03, magnitud: 3.31, temperatura: 9480, elementoPrincipal: "Hidrógeno", elementoSecundario: "Silicio", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es la más tenue del Carro y une el cuenco con el mango." },
            { nombre: "Alioth", ascensionRecta: 12.9, declinacion: 55.96, magnitud: 1.77, temperatura: 9400, elementoPrincipal: "Cromo", elementoSecundario: "Europio", elementoTerciario: "Hierro", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es la más brillante de la Osa Mayor. Tiene un campo magnético fuerte que concentra cromo en su superficie." },
            { nombre: "Mizar", ascensionRecta: 13.399, declinacion: 54.93, magnitud: 2.04, temperatura: 9000, elementoPrincipal: "Hidrógeno", elementoSecundario: "Calcio", elementoTerciario: "Titanio", fechaDescubierta: "1617", descubrio: "Benedetto Castelli / Galileo", descripcion: "Con sua vecina Alcor sirve desde hace siglos para probar la vista. Fue la primera estrella doble descubierta con telescopio." },
            { nombre: "Alkaid", ascensionRecta: 13.792, declinacion: 49.31, magnitud: 1.86, temperatura: 15500, elementoPrincipal: "Helio", elementoSecundario: "Hidrógeno", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es una estrella azul joven. A diferencia de las demás del mango, no viaja con el grupo." },
            { nombre: "Muscida", ascensionRecta: 8.504, declinacion: 60.72, magnitud: 3.36, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Upsilon UMa", ascensionRecta: 9.848, declinacion: 59.04, magnitud: 3.8, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Phi UMa", ascensionRecta: 9.868, declinacion: 54.06, magnitud: 4.55, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Theta UMa", ascensionRecta: 9.547, declinacion: 51.68, magnitud: 3.17, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Iota UMa", ascensionRecta: 8.987, declinacion: 48.04, magnitud: 3.14, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Kappa UMa", ascensionRecta: 9.06, declinacion: 47.16, magnitud: 3.6, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Tania Borealis", ascensionRecta: 10.285, declinacion: 42.91, magnitud: 3.45, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Tania Australis", ascensionRecta: 10.373, declinacion: 41.5, magnitud: 3.05, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Psi UMa", ascensionRecta: 11.161, declinacion: 44.5, magnitud: 3.01, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Chi UMa", ascensionRecta: 11.767, declinacion: 47.78, magnitud: 3.71, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Alula Borealis", ascensionRecta: 11.307, declinacion: 33.09, magnitud: 3.48, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Alula Australis", ascensionRecta: 11.303, declinacion: 31.53, magnitud: 3.79, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." }
        ],
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
        id: "cas",
        nombre: "Casiopea",
        alias: "La reina",
        "descripcion mitologica": "Casiopea era la reina de Etiopía. Presumió de que ella y su hija Andrómeda eran más bellas que las Nereidas, y Poseidón envió un monstruo marino contra el reino. Como castigo, los dioses la pusieron en el cielo girando alrededor del polo.",
        "descripcion cientifica": "Dibuja una W (o una M, según la hora) sobre la Vía Láctea. Está al lado opuesto de la Estrella Polar respecto a la Osa Mayor, así que sirve para orientarse.",
        posicion: [0.153, 59.15],
        cuerpoCelestes: [
            { nombre: "Caph", ascensionRecta: 0.153, declinacion: 59.15, magnitud: 2.28, temperatura: 6900, elementoPrincipal: "Calcio", elementoSecundario: "Hierro", elementoTerciario: "Titanio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Su nombre significa «mano teñida». Gira muy rápido y tiene pulsaciones leves." },
            { nombre: "Schedar", ascensionRecta: 0.675, declinacion: 56.54, magnitud: 2.24, temperatura: 4530, elementoPrincipal: "Calcio", elementoSecundario: "Hierro", elementoTerciario: "Carbono", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es una gigante naranja. Su nombre significa «pecho» en árabe." },
            { nombre: "Gamma Cas", ascensionRecta: 0.945, declinacion: 60.72, magnitud: 2.47, temperatura: 25000, elementoPrincipal: "Helio", elementoSecundario: "Hidrógeno", elementoTerciario: "Carbono", fechaDescubierta: "1866", descubrio: "Angelo Secchi", descripcion: "Gira tan deprisa que se aplana y lanza un disco de gas. Su brillo cambia con el tiempo." },
            { nombre: "Ruchbah", ascensionRecta: 1.43, declinacion: 60.24, magnitud: 2.68, temperatura: 8400, elementoPrincipal: "Hidrógeno", elementoSecundario: "Calcio", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Su nombre significa «rodilla». Es una estrella blanca doble." },
            { nombre: "Segin", ascensionRecta: 1.907, declinacion: 63.67, magnitud: 3.37, temperatura: 15000, elementoPrincipal: "Helio", elementoSecundario: "Hidrógeno", elementoTerciario: "Silicio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es una estrella azul joven y la más tenue de la W." }
        ],
        lineas: [
            [0, 1], [1, 2], [2, 3], [3, 4]         // la W
        ]
    },
    {
        id: "leo",
        nombre: "Leo",
        alias: "El león",
        "descripcion mitologica": "El León de Nemea tenía una piel tan dura que ninguna arma la atravesaba. Vencerlo fue el primer trabajo de Hércules, que lo estranguló con sus manos y usó su piel como armadura.",
        "descripcion cientifica": "La Hoz (una especie de interrogación al revés) dibuja la cabeza y la melena. Un triángulo hacia la izquierda marca los cuartos traseros y la cola. Regulus, «el pequeño rey», está en el pecho.",
        posicion: [10.139, 11.97],
        cuerpoCelestes: [
            { nombre: "Regulus", ascensionRecta: 10.139, declinacion: 11.97, magnitud: 1.35, temperatura: 12460, elementoPrincipal: "Hidrógeno", elementoSecundario: "Helio", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Gira tan rápido que da una vuelta en unas 16 horas y queda achatada." },
            { nombre: "Algieba", ascensionRecta: 10.333, declinacion: 19.84, magnitud: 2.28, temperatura: 4470, elementoPrincipal: "Calcio", elementoSecundario: "Hierro", elementoTerciario: "Titanio", fechaDescubierta: "1783", descubrio: "William Herschel", descripcion: "Su nombre significa «la frente». Es una estrella doble que se ve con telescopio pequeño." },
            { nombre: "Zosma", ascensionRecta: 11.235, declinacion: 20.52, magnitud: 2.56, temperatura: 8300, elementoPrincipal: "Hidrógeno", elementoSecundario: "Calcio", elementoTerciario: "Hierro", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Está en el lomo del león. Su nombre árabe se relaciona con «cinturón»." },
            { nombre: "Denébola", ascensionRecta: 11.818, declinacion: 14.57, magnitud: 2.14, temperatura: 8500, elementoPrincipal: "Hidrógeno", elementoSecundario: "Silicio", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Su nombre significa «la cola del león»." },
            { nombre: "Algenubi", ascensionRecta: 9.764, declinacion: 23.77, magnitud: 2.98, temperatura: 5400, elementoPrincipal: "Hierro", elementoSecundario: "Calcio", elementoTerciario: "Magnesio", fechaDescubierta: "150 d.C.", descubrio: "Claudio Ptolomeo", descripcion: "Es el extremo sur de la Hoz. Su nombre significa «la del sur»." },
            { nombre: "Eta Leonis", ascensionRecta: 10.122, declinacion: 16.76, magnitud: 3.52, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Adhafera", ascensionRecta: 10.278, declinacion: 23.42, magnitud: 3.44, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Rasalas", ascensionRecta: 9.879, declinacion: 26.01, magnitud: 3.88, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." },
            { nombre: "Chertan", ascensionRecta: 11.237, declinacion: 15.43, magnitud: 3.33, temperatura: null, elementoPrincipal: null, elementoSecundario: null, elementoTerciario: null, fechaDescubierta: null, descubrio: null, descripcion: "Estrella componente de la constelación." }
        ],
        lineas: [
            [0, 5], [5, 1], [1, 6], [6, 7], [7, 4], // la Hoz
            [1, 2], [2, 3], [2, 8], [8, 3]          // lomo, cola y triángulo trasero
        ]
    }
];

function esCuerpoCelestePrincipal(cuerpoCeleste) {
    return cuerpoCeleste.temperatura !== null;
}

function radio(magnitud) {
    let tamano = 12 - magnitud * 2.5;
    if (tamano < 3) {
        tamano = 3;
    }
    return tamano;
}

function colorCuerpoCeleste(temperatura) {
    let colorEstrella;
    if (temperatura < 3700) {
        colorEstrella = "#ff8a5c";
    } else if (temperatura < 5200) {
        colorEstrella = "#ffb56b";
    } else if (temperatura < 6000) {
        colorEstrella = "#ffe4a1";
    } else if (temperatura < 7500) {
        colorEstrella = "#fff4e8";
    } else if (temperatura < 10000) {
        colorEstrella = "#f8f7ff";
    } else if (temperatura < 30000) {
        colorEstrella = "#cad8ff";
    } else {
        colorEstrella = "#9bb0ff";
    }
    return colorEstrella;
}

function proyectar(constelacion, ancho, alto, margenX, margenY) {
    const estrellas = constelacion.cuerpoCelestes || constelacion.cuerposCelestes || [];
    if (estrellas.length === 0) return [];

    let decMin = Number(estrellas[0].declinacion);
    let decMax = Number(estrellas[0].declinacion);
    for (let i = 1; i < estrellas.length; i++) {
        const dec = Number(estrellas[i].declinacion);
        if (dec < decMin) decMin = dec;
        if (dec > decMax) decMax = dec;
    }

    const decMedia = (decMax + decMin) / 2;
    const k = Math.cos(decMedia * Math.PI / 180) || 1;

    const puntos = [];
    for (let i = 0; i < estrellas.length; i++) {
        const ar = Number(estrellas[i].ascensionRecta);
        const dec = Number(estrellas[i].declinacion);
        puntos.push({ x: -ar * 15 * k, y: -dec });
    }

    let xMin = puntos[0].x;
    let xMax = puntos[0].x;
    let yMin = puntos[0].y;
    let yMax = puntos[0].y;
    for (let i = 1; i < puntos.length; i++) {
        if (puntos[i].x < xMin) xMin = puntos[i].x;
        if (puntos[i].x > xMax) xMax = puntos[i].x;
        if (puntos[i].y < yMin) yMin = puntos[i].y;
        if (puntos[i].y > yMax) yMax = puntos[i].y;
    }
    const anchoFigura = (xMax - xMin) || 1;
    const altoFigura = (yMax - yMin) || 1;

    const escalaX = (ancho - 2 * margenX) / anchoFigura;
    const escalaY = (alto - 2 * margenY) / altoFigura;
    const escala = Math.min(escalaX, escalaY);

    const offsetX = (ancho - anchoFigura * escala) / 2;
    const offsetY = (alto - altoFigura * escala) / 2;

    const posiciones = [];
    for (let i = 0; i < puntos.length; i++) {
        posiciones.push({
            x: offsetX + (puntos[i].x - xMin) * escala,
            y: offsetY + (puntos[i].y - yMin) * escala
        });
    }
    return posiciones;
}


function dibujarConstelacion(constelacion, colocadas, modo) {
    const mini = modo === "mini";
    const ancho = 600;
    const alto = mini ? 300 : 440;
    const cuerpoCeleste = constelacion.cuerpoCelestes || constelacion.cuerposCelestes || [];
    const lineas = constelacion.lineas || [];

    const posiciones = proyectar(constelacion, ancho, alto, mini ? 25 : 80, mini ? 25 : 45);
    if (posiciones.length === 0) return "";

    function estaLista(indice) {
        if (!cuerpoCeleste[indice]) return false;
        if (esCuerpoCelestePrincipal(cuerpoCeleste[indice]) === false) return true;
        if (colocadas[indice] === true) return true;
        return false;
    }

    let svg = '<svg viewBox="0 0 ' + ancho + ' ' + alto + '">';

    // ---------- LÍNEAS ----------
    for (let i = 0; i < lineas.length; i++) {
        const linea = lineas[i];
        const origen = linea[0];
        const destino = linea[1];

        if (!posiciones[origen] || !posiciones[destino]) {
            console.warn("Línea inválida en " + constelacion.nombre + ":", linea);
            continue;
        }

        let colorLinea, grosor, opacidad, guiones;
        if (estaLista(origen) && estaLista(destino)) {
            colorLinea = "#e8cf8a";
            grosor = 1.6;
            opacidad = 0.9;
            guiones = "";
        } else {
            colorLinea = "#3a4468";
            grosor = 1;
            opacidad = 0.5;
            guiones = 'stroke-dasharray="3 4"';
        }

        svg += '<line x1="' + posiciones[origen].x + '" y1="' + posiciones[origen].y + '"'
            + ' x2="' + posiciones[destino].x + '" y2="' + posiciones[destino].y + '"'
            + ' stroke="' + colorLinea + '" stroke-width="' + grosor + '"'
            + ' opacity="' + opacidad + '" ' + guiones + '/>';
    }

    // ---------- ESTRELLAS ----------
    for (let i = 0; i < cuerpoCeleste.length; i++) {
        const estrella = cuerpoCeleste[i];
        const x = posiciones[i].x;
        const y = posiciones[i].y;

        let tamano = Number(radio(estrella.magnitud));
        if (!Number.isFinite(tamano) || tamano < 2) {
            tamano = 4;
        }

        // Estrella secundaria
        if (esCuerpoCelestePrincipal(estrella) === false) {
            const tamanoPunto = Math.max(tamano * 0.55, 1.8);
            svg += '<circle cx="' + x + '" cy="' + y + '" r="' + tamanoPunto
                + '" fill="#cfd8f5" opacity="0.8"/>';
            continue;
        }

        // Estrella principal
        const xTexto = x < ancho / 2 ? x + tamano + 12 : x - tamano - 12;
        const alineacion = x < ancho / 2 ? "start" : "end";

        svg += '<g class="node" data-i="' + i + '" style="cursor:pointer">';

        if (colocadas[i] === true) {
            const colorEstrella = colorCuerpoCeleste(estrella.temperatura) || "#ffffff";
            svg += '<circle cx="' + x + '" cy="' + y + '" r="' + tamano
                + '" fill="' + colorEstrella + '"/>';
            if (!mini) {
                svg += '<text x="' + xTexto + '" y="' + (y + 3) + '" text-anchor="' + alineacion
                    + '" fill="#e8ecff" font-size="12">' + estrella.nombre + '</text>';
            }
        } else {
            svg += '<circle class="hueco" cx="' + x + '" cy="' + y + '" r="' + (tamano + 9)
                + '" fill="transparent" stroke="#aab8e8" stroke-dasharray="3 3"/>';
            svg += '<circle cx="' + x + '" cy="' + y + '" r="2" fill="#aab8e8" pointer-events="none"/>';
        }

        svg += '</g>';
    }

    svg += '</svg>';
    return svg;
}

function valoresElementoAlquimico(elemtosAlquimicos, elementoQuimicoPrincipal) {
    let valor = [];
    let elementoNombre;

    for (const valorLista of elemtosAlquimicos) {
        elementoNombre = valorLista.nombre;

        if ((elementoQuimicoPrincipal === "Hidrógeno") && elementoNombre === "Agua") {
            valor = [valorLista.color, valorLista.svg, valorLista.nombre];
        } else if ((elementoQuimicoPrincipal === "Titanio" || elementoQuimicoPrincipal === "Hierro" || elementoQuimicoPrincipal === "Calcio") && elementoNombre === "Tierra") {
            valor = [valorLista.color, valorLista.svg, valorLista.nombre];
        } else if (elementoQuimicoPrincipal === "Helio" && elementoNombre === "Aire") {
            valor = [valorLista.color, valorLista.svg, valorLista.nombre];
        } else if (elementoQuimicoPrincipal === "Cromo" && elementoNombre === "Fuego") {
            valor = [valorLista.color, valorLista.svg, valorLista.nombre];
        }
    }

    return valor;
}


function iniciarJuego() {

    let divElementAlq = document.getElementById("elementos");
    let as = document.createElement("div");
    as.id = "div_element";
    as.className = "div_element";

    function mostrarElementosAlquimicos(elemtosAlquimicos) {
        for (const valorLista of elemtosAlquimicos) {
            as.innerHTML += `
    <div class='triangulos'>
    <svg viewBox="0 0 100 100" width="50" style='color:${valorLista.color}' height="50" stroke="currentColor" fill="none" stroke-width="6">${valorLista.svg}</svg>
    <span>${valorLista.nombre}</span></div> `;

            divElementAlq.appendChild(as);
        }
    }


    const CLAVE_GUARDADO = "constelaciones_guardadas";

    let guardadas = {};
    try {
        guardadas = JSON.parse(localStorage.getItem(CLAVE_GUARDADO) || "{}");
    } catch (error) { }

    function listaConstelacionAleatoria() {
        const disponibles = [...constelaciones];
        const mezcladas = [];

        while (disponibles.length > 0) {
            const i = Math.floor(Math.random() * disponibles.length);
            mezcladas.push(disponibles.splice(i, 1)[0]);
        }
        return mezcladas;
    }

    function buscarConstelacionPendiente(listaConstelaciones) {
        for (const constelacion of listaConstelaciones) {
            if (!guardadas[constelacion.id]) {
                return constelacion;
            }
        }
        return undefined;
    }

    const constelacionesAleatorias = listaConstelacionAleatoria();
    let constelacion = buscarConstelacionPendiente(constelacionesAleatorias) ?? constelacionesAleatorias[0];

    // ---------- 3.3 Referencias al DOM y estado ----------
    const constelacionFigura = document.getElementById("sky");
    const nombreConstelacion = document.getElementById("title");
    const descripcionConstelacion = document.getElementById("descripcionCientifica");
    const cuerposCelesteConstelacion = document.getElementById("pieces");
    const pistas = document.getElementById("fb");

    let colocadas = {};
    let cont = 0;
    const piezasDOM = {};       // indice -> div, para quitar la pieza al acertar
    let totalPrincipales = 0;   // cambia en cada constelación

    // ---------- 3.4 Funciones del juego ----------
    function renderPendiente(constelacion, elemtosAlquimicos) {
        if (!constelacion) {
            constelacionFigura.innerHTML = "<p>¡Has completado todas las constelaciones!</p>";
            return;
        }
        constelacionFigura.innerHTML = dibujarConstelacion(constelacion, colocadas);

        const puntos = constelacionFigura.querySelectorAll(".node");

        for (const nodo of puntos) {
            const indice = Number(nodo.dataset.i);

            nodo.ondragover = function (e) {
                e.preventDefault();
            };

            nodo.ondrop = function (e) {
                e.preventDefault();
                const piezaIndice = Number(e.dataTransfer.getData("text/plain"));

                if (piezaIndice === indice) {
                    colocadas[indice] = true;
                    piezasDOM[piezaIndice].remove();
                    renderPendiente(constelacion, elemtosAlquimicos);
                    pistas.innerHTML = "<h2>Genial</h2><p>vamos a por otra!</p>";
                    comprobarVictoria(totalPrincipales);   // después, para que su mensaje quede encima
                } else {
                    pistas.innerHTML = "<h2>Ups</h2><p>Esa estrella no va ahí. ¡Prueba con otra!</p>";
                }
            };

            nodo.onclick = function () {
                cont++;

                let puntoSeleccionado = constelacionFigura.querySelector(".node.seleccionado");
                if (puntoSeleccionado !== null) {
                    puntoSeleccionado.classList.remove("seleccionado");
                }

                nodo.classList.add("seleccionado");
                mostrarPistas(indice, cont);
            };
        }
    }

    function mostrarPistas(indiceI, cont) {
        let cuerpoCeleste = constelacion.cuerpoCelestes[indiceI];
        let val = valoresElementoAlquimico(elemtosAlquimicos, cuerpoCeleste.elementoPrincipal);

        if (cont == 1) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Su elemento principal y secundario son: ${cuerpoCeleste.elementoPrincipal} -- ${cuerpoCeleste.elementoSecundario} </p>`;
        } else if (cont == 2) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Fue descubierto por : ${cuerpoCeleste.descubrio} en el año ${cuerpoCeleste.fechaDescubierta} </p>`;
        } else if (cont == 3) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Su elemento secundario y principal son: ${cuerpoCeleste.elementoSecundario} -- ${cuerpoCeleste.elementoPrincipal} </p>`;
        } else if (cont == 4) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Su temperatura es: ${cuerpoCeleste.temperatura} y su elemento Alquimico es: ${val[2]}  </p>`;
        } else if (cont == 5) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Su elemento terciario y principal son: ${cuerpoCeleste.elementoTerciario} -- ${cuerpoCeleste.elementoPrincipal} </p>`;
        } else if (cont == 6) {
            pistas.innerHTML =
                `<h2>Pista</h2>
                <p>Su elemento secundario y terciario son: ${cuerpoCeleste.elementoSecundario} -- ${cuerpoCeleste.elementoTerciario} </p>`;
        }
    }

    function mostrarCuerposCelestes(constelacion) {
        constelacion.cuerpoCelestes.forEach(function (cuerpoCeleste, indice) {

            if (!esCuerpoCelestePrincipal(cuerpoCeleste)) return;

            const div = document.createElement('div');
            div.id = `id${cuerpoCeleste.nombre}`;
            div.className = 'piece';
            div.draggable = true;

            let temperatura = cuerpoCeleste.temperatura;
            let principalElemento = cuerpoCeleste.elementoPrincipal;
            let val = valoresElementoAlquimico(elemtosAlquimicos, principalElemento);

            div.innerHTML = `
     <div class='prow-container'>
         <div class='prow'>
             <div class='orb' style="--c:${colorCuerpoCeleste(temperatura)}"></div>
             <div class='small' id="info">
                 <h3>${cuerpoCeleste.nombre}</h3>
                 <svg viewBox="0 0 100 100" width="50"  height="50" stroke="currentColor" style='color:${val[0]}' fill="none" stroke-width="6">${val[1]}</svg>
                <br> ${temperatura}
                 <br>${cuerpoCeleste.elementoPrincipal}
             </div>
         </div>
         <div class='info-extra'></div>
     </div>
    `;

            div.addEventListener("dragstart", function (e) {
                e.dataTransfer.setData("text/plain", String(indice));
                e.dataTransfer.effectAllowed = "move";
                div.classList.add("arrastrando");
            });
            div.addEventListener("dragend", function () {
                div.classList.remove("arrastrando");
            });

            let clicki = false;

            div.onclick = function () {
                const infoExtra = div.querySelector('.info-extra');

                if (!clicki) {
                    infoExtra.innerHTML = `<p>Elemento principal: ${cuerpoCeleste.elementoPrincipal},  secundario: ${cuerpoCeleste.elementoSecundario}, terciario: ${cuerpoCeleste.elementoTerciario}, descubierto en el año: ${cuerpoCeleste.fechaDescubierta}, por ${cuerpoCeleste.descubrio}</p>`;
                    clicki = true;
                } else {
                    infoExtra.innerHTML = "";
                    clicki = false;
                }
            };

            piezasDOM[indice] = div;
            cuerposCelesteConstelacion.appendChild(div);
        });
    }

    function comprobarVictoria(totalPrincipales) {
        if (Object.keys(colocadas).length === totalPrincipales) {
            console.log("Victoria, id:", constelacion.id);
            guardadas[constelacion.id] = true;
            try {
                localStorage.setItem(CLAVE_GUARDADO, JSON.stringify(guardadas));
                console.log("Guardado:", localStorage.getItem(CLAVE_GUARDADO));
            } catch (error) {
                console.error("Error al guardar:", error);
            }
            pistas.innerHTML =
                `<h2>Sabias que : </h2><p>${constelacion["descripcion mitologica"]}</p>`;
            document.getElementById("next").disabled = false;
        }
    }

    function cargarConstelacion(constelacion, elemtosAlquimicos) {
        colocadas = {};
        cont = 0;
        for (const k in piezasDOM) delete piezasDOM[k];
        totalPrincipales = constelacion.cuerpoCelestes.filter(esCuerpoCelestePrincipal).length;

        nombreConstelacion.textContent = `${constelacion.nombre} -${constelacion.alias}`;
        descripcionConstelacion.textContent = constelacion["descripcion cientifica"];
        cuerposCelesteConstelacion.innerHTML = "";
        pistas.innerHTML = "";
        document.getElementById("next").disabled = true;

        mostrarCuerposCelestes(constelacion);
        renderPendiente(constelacion, elemtosAlquimicos);
    }

    // ---------- 3.5 Arranque (una sola vez) ----------
    document.getElementById("next").onclick = function () {
        constelacion = buscarConstelacionPendiente(constelacionesAleatorias) ?? constelacionesAleatorias[0];
        cargarConstelacion(constelacion, elemtosAlquimicos);
    };

    document.getElementById("inventario").onclick = function () {
        window.location.href = "/games/constelacion/inventario";
    };

    document.getElementById("jugar").onclick = function () {
        window.location.href = "/games/constelacion";
    };

    mostrarElementosAlquimicos(elemtosAlquimicos);
    cargarConstelacion(constelacion, elemtosAlquimicos);
}

if (document.getElementById("sky")) {
    iniciarJuego();
}