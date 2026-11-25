// Vista "Métricas": ejecuta el análisis en Python dentro del navegador (Pyodide).
// loadPyodide() lo define el <script> del CDN que carga la vista Blade.
//
// Flujo:
//   1. arrancar Pyodide e instalar los paquetes de Python
//   2. pedir a Laravel las sesiones en JSON (admin.metricas.datos)
//   3. pasar el JSON a Python, ejecutar public/python/metricas.py y leer `resultado`
//   4. pintar el resultado en la vista

const raiz = document.getElementById('metricas-python');

if (raiz) {
    const estado = document.getElementById('py-estado');
    const salida = document.getElementById('py-salida');
    const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const decir = (texto) => { estado.textContent = texto; };
    const dato = (v, sufijo = '') => (v === null || v === undefined ? '—' : `${v}${sufijo}`);

    // Paquetes de Python que necesita el pipeline (pandas, gráficos y modelo).
    const PAQUETES = ['pandas', 'matplotlib', 'scikit-learn'];

    async function ejecutar() {
        try {
            decir('Cargando Python (Pyodide)… la primera vez tarda unos segundos.');
            const py = await loadPyodide({ indexURL: raiz.dataset.pyodide });

            decir('Instalando paquetes de Python…');
            await py.loadPackage(PAQUETES);

            decir('Cargando las sesiones desde Laravel…');
            const datos = await fetch(raiz.dataset.datos, { headers: { Accept: 'application/json' } });
            if (!datos.ok) throw new Error(`No se pudieron cargar las sesiones (HTTP ${datos.status}).`);
            py.globals.set('datos_json', await datos.text());

            decir('Ejecutando el análisis en Python…');
            const codigo = await fetch(raiz.dataset.script, { cache: 'no-store' });
            if (!codigo.ok) throw new Error(`No se pudo cargar el script de Python (HTTP ${codigo.status}).`);
            await py.runPythonAsync(await codigo.text());

            pintar(JSON.parse(py.globals.get('resultado')));
            decir('Listo.');
        } catch (error) {
            console.error(error);
            decir(`Error: ${error.message}`);
        }
    }

    /** Tabla genérica: cabeceras y filas (los valores ya van escapados). */
    function tabla(cabeceras, filas, extra = '') {
        return `
            <div class="overflow-x-auto ${extra}">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase tracking-wider text-[#93a0bd] border-b border-[#93a0bd]/30">
                        <tr>${cabeceras.map((c) => `<th class="py-1.5 pr-4">${esc(c)}</th>`).join('')}</tr>
                    </thead>
                    <tbody>${filas.map((f) => `<tr class="border-b border-[#93a0bd]/10">${f.map((v) => `<td class="py-1.5 pr-4">${v}</td>`).join('')}</tr>`).join('')}</tbody>
                </table>
            </div>`;
    }

    const titulo = (t) => `<h3 class="text-xs uppercase tracking-wider text-[#93a0bd] mt-6 mb-2">${esc(t)}</h3>`;
    const tarjeta = (nombre, valor) => `
        <div class="rounded-xl border border-[#93a0bd]/25 bg-[#10153f]/60 px-4 py-3">
            <p class="font-mono text-xs text-[#93a0bd]">${esc(nombre)}</p>
            <p class="font-display text-2xl text-[#d3dbea]">${esc(valor)}</p>
        </div>`;

    /** Un gráfico de Python (imagen base64) o el aviso de que no se pudo generar. */
    const figura = (g) => `
        <figure class="rounded-xl border border-[#93a0bd]/25 bg-[#10153f]/60 p-3">
            ${g.imagen
                ? `<img src="${g.imagen}" alt="${esc(g.titulo)}" class="w-full h-auto">`
                : `<p class="text-sm text-[#ff8a5c]">No se pudo generar «${esc(g.titulo)}» (${esc(g.error)}).</p>`}
        </figure>`;

    /** Sección del modelo: resultados si se entrenó, o el motivo por el que no se pudo. */
    function seccionModelo(mod) {
        if (!mod.entrenado) {
            const motivo = mod.error
                ? `Error al entrenar el modelo (${esc(mod.error)}).`
                : `No hay datos suficientes para entrenar el modelo: ${esc(mod.jugadores)} jugadores
                   (${esc(mod.clases.vuelve)} vuelven y ${esc(mod.clases.abandona)} abandonan).
                   Se necesitan al menos ${esc(mod.minimos.jugadores)} jugadores y ${esc(mod.minimos.por_clase)} de cada tipo.`;
            return `${titulo('Modelo de predicción')}<p class="text-sm text-[#93a0bd]">${motivo}</p>`;
        }
        const k = mod.metricas;
        return `
            ${titulo('Modelo de predicción')}
            <p class="text-sm text-[#93a0bd] mb-3">
                ${esc(mod.algoritmo)} · ${esc(mod.jugadores)} jugadores (${esc(mod.entrenamiento)} para entrenar, ${esc(mod.prueba)} para probar) ·
                variables de la primera sesión: <span class="font-mono">${esc(mod.caracteristicas.join(', '))}</span>
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                ${tarjeta('accuracy', dato(k.accuracy))}
                ${tarjeta('precision', dato(k.precision))}
                ${tarjeta('recall', dato(k.recall))}
                ${tarjeta('ROC-AUC', dato(k.roc_auc))}
            </div>
            <div class="grid gap-5 md:grid-cols-2">${mod.graficos.map(figura).join('')}</div>`;
    }

    function pintar(r) {
        if (r.vacio) {
            salida.innerHTML = '<p class="text-sm text-[#93a0bd]">No hay sesiones válidas para analizar.</p>';
            return;
        }
        const m = r.resumen;
        const l = r.limpieza;

        salida.innerHTML = `
            ${titulo('Métricas derivadas')}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                ${tarjeta('player_count', m.player_count)}
                ${tarjeta('returning_player = 1', m.returning_players)}
                ${tarjeta('retention_rate', dato(m.retention_rate, ' %'))}
                ${tarjeta('churn_rate', dato(m.churn_rate, ' %'))}
                ${tarjeta('average_session_length', dato(m.average_session_length, ' min'))}
                ${tarjeta('sesiones por jugador', dato(m.sessions_per_player))}
                ${tarjeta('DAU (media)', dato(m.dau_medio))}
                ${tarjeta('MAU (último mes)', m.mau_ultimo_mes)}
            </div>

            ${titulo('Visualización de los datos')}
            <div class="grid gap-5 md:grid-cols-2">${r.graficos.map(figura).join('')}</div>

            ${seccionModelo(r.modelo)}

            ${titulo('Limpieza de datos')}
            ${tabla(['Paso', 'Filas / valores'], [
                ['Sesiones cargadas', esc(l.filas_entrada)],
                ['Duplicadas eliminadas (session_id repetido)', esc(l.duplicados)],
                ['Incompletas eliminadas (sin usuario, fecha o duración)', esc(l.incompletas)],
                ['Anómalas eliminadas (duración, puntuación o contadores imposibles)', esc(l.anomalias)],
                ['Nulos rellenados con 0', esc(Object.entries(l.nulos_rellenados).map(([c, n]) => `${c}: ${n}`).join(' · '))],
                ['Sesiones tras la limpieza', esc(l.filas_salida)],
            ], 'max-w-3xl')}

            ${titulo('Estructura de los datos (antes de limpiar)')}
            ${tabla(['Columna', 'Tipo', 'Nulos'], r.estructura.map((c) => [esc(c.nombre), esc(c.tipo), esc(c.nulos)]), 'max-w-xl')}

            ${titulo('Resumen estadístico (después de limpiar)')}
            ${tabla(['Variable', 'Media', 'Desv. típica', 'Mín.', 'Mediana', 'Máx.'],
                r.describe.map((d) => [esc(d.variable), dato(d.media), dato(d.std), dato(d.min), dato(d.mediana), dato(d.max)]), 'max-w-3xl')}

            ${titulo('Jugadores')}
            ${tabla(['Usuario', 'Sesiones', 'Primera sesión', 'Minutos totales', 'returning_player'],
                r.jugadores.map((j) => [esc(j.username), esc(j.sessions), esc(j.first_session), dato(j.total_minutes), esc(j.returning_player)]), 'max-w-3xl')}

            <div class="grid gap-6 md:grid-cols-2 max-w-3xl">
                <div>${titulo('Jugadores activos por día (DAU)')}
                    ${tabla(['Día', 'Jugadores'], r.actividad.dau.map((d) => [esc(d.periodo), esc(d.jugadores)]))}</div>
                <div>${titulo('Jugadores activos por mes (MAU)')}
                    ${tabla(['Mes', 'Jugadores'], r.actividad.mau.map((d) => [esc(d.periodo), esc(d.jugadores)]))}</div>
            </div>`;
    }

    ejecutar();
}
