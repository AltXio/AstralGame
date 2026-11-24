// Lógica del Juego 1 (Astralquimium)
// Cada vista (sistema, planeta, laboratorio) trae su HTML desde Blade; aquí solo se añade la interacción.

const raiz = document.querySelector('.aq');

if (raiz) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const alquimicos = JSON.parse(document.getElementById('aq-alquimicos')?.textContent || '{}');
    const $ = (sel, el = raiz) => el.querySelector(sel);

    /** Llamada JSON al servidor con el token CSRF. */
    async function api(url, metodo = 'POST', cuerpo = null, extra = {}) {
        const res = await fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: cuerpo ? JSON.stringify(cuerpo) : null,
            ...extra,
        });
        const datos = res.status === 204 ? null : await res.json().catch(() => null);
        if (!res.ok) throw Object.assign(new Error(datos?.message || 'Error'), { status: res.status });
        return datos;
    }

    const iconoAlq = (k) =>
        `<svg class="alq-icon" viewBox="0 0 100 100" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round">${alquimicos[k]?.svg ?? ''}</svg>`;

    const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    /* ---------- sesión (partida) ----------
       El servidor abre o reutiliza la partida al cargar la página (data-sesion).
       Mientras haya actividad se avisa para guardar la duración; tras 10 minutos
       sin avisos la partida se da por terminada y la siguiente empieza con el inventario vacío. */
    (function sesion() {
        const id = raiz.dataset.sesion;
        if (!id) return;
        const aviso = () => {
            api(raiz.dataset.sesionAviso.replace('__ID__', id), 'PATCH', null, { keepalive: true }).catch(() => {});
        };
        setInterval(() => { if (!document.hidden) aviso(); }, 60000);
        addEventListener('pagehide', aviso);
        document.addEventListener('visibilitychange', () => { if (document.hidden) aviso(); });
    })();

    /* ---------- vista 1: sistema solar ---------- */
    if (raiz.dataset.vista === 'sistema') {
        const hint = $('#hint');
        const base = hint.dataset.base;
        const mostrar = (e) => {
            const b = e.target.closest('.body');
            if (b) hint.textContent = `${b.dataset.nombre}: ${b.dataset.resumen}`;
        };
        const limpiar = () => { hint.textContent = base; };
        const escenario = $('#stage');
        escenario.addEventListener('pointerover', mostrar);
        escenario.addEventListener('focusin', mostrar);
        escenario.addEventListener('pointerout', limpiar);
        escenario.addEventListener('focusout', limpiar);
    }

    /* ---------- vista 2: ficha del planeta ---------- */
    if (raiz.dataset.vista === 'planeta') {
        const detalle = $('#detail');
        const lista = $('#inv-list');

        function anadirAlInventario(orbe) {
            if (lista.querySelector(`[data-sim-chip="${orbe.dataset.sim}"]`)) return;
            $('#inv-empty')?.remove();
            const li = document.createElement('li');
            li.className = 'chip-sym';
            li.dataset.simChip = orbe.dataset.sim;
            li.title = orbe.dataset.nombre;
            li.style.setProperty('--el', orbe.dataset.color);
            li.textContent = orbe.dataset.sim;
            lista.append(li);
        }

        $('#dial').addEventListener('click', async (e) => {
            const orbe = e.target.closest('.orb');
            if (!orbe) return;
            raiz.querySelectorAll('.orb').forEach((o) => o.setAttribute('aria-pressed', String(o === orbe)));

            const d = orbe.dataset;
            detalle.innerHTML = `
                <div class="el-head">
                    <div class="tile" style="--el:${d.color}">${esc(d.sim)}</div>
                    <div><h3>${esc(d.nombre)}</h3></div>
                </div>
                <p class="tag" style="--el:${d.color}">${iconoAlq(d.alq)}<span>${esc(d.alqNombre)}</span></p>
                <p class="why">${esc(d.porque)}</p>
                ${d.dato ? `<p class="fact">${esc(d.dato)}</p>` : ''}`;

            // al seleccionarlo se guarda en el inventario
            try {
                await api(d.url);
                orbe.classList.add('found');
                anadirAlInventario(orbe);
            } catch (err) { /* sin conexión: el elemento se verá pero no se guardará */ }
        });
    }

    /* ---------- vista 3: laboratorio ---------- */
    if (raiz.dataset.vista === 'laboratorio' && $('#lab')) {
        const lab = $('#lab');
        const MAX = Number(lab.dataset.max);
        const UMBRAL = Number(lab.dataset.umbral);
        const partes = {};                       // id_elemento -> número de partes
        const total = () => Object.values(partes).reduce((a, b) => a + b, 0);
        let ultimaPeticion = 0;
        let temporizador = null;

        function colorMezcla(alq, suma) {
            if (!suma) return '#7a8bd0';
            let rgb = [0, 0, 0];
            for (const k of Object.keys(alquimicos)) {
                const h = alquimicos[k].color;
                const w = (alq[k] || 0) / suma;
                rgb = rgb.map((v, i) => v + parseInt(h.slice(1 + i * 2, 3 + i * 2), 16) * w);
            }
            return `rgb(${rgb.map(Math.round).join(',')})`;
        }

        const veredicto = (h, t) =>
            !t ? 'Mezcla vacía' : h < 20 ? 'Mundo estéril' : h < 40 ? 'Mundo hostil' :
            h < 60 ? 'Extremo: quizá microbios' : h < 80 ? 'Habitable' : 'Apto para vida compleja';

        /** Pinta lo que no depende del servidor (cuentas y botones). */
        function pintarCuentas() {
            const t = total();
            $('#cuenta').textContent = `${t} de ${MAX} partes`;
            lab.querySelectorAll('.mix-row').forEach((fila) => {
                const n = partes[fila.dataset.id] || 0;
                $('.count', fila).textContent = n;
                $('.menos', fila).disabled = n === 0;
                $('.mas', fila).disabled = t >= MAX;
            });
            $('#guardar').disabled = t === 0;
        }

        /** Pinta los indicadores que calcula el servidor. */
        function pintarEvaluacion(ev) {
            for (const k of Object.keys(ev.ind)) {
                const li = $(`#ind [data-k="${k}"]`);
                $('.fill', li).style.width = ev.ind[k] + '%';
                $('.val', li).textContent = ev.ind[k];
            }
            $('#meter').style.setProperty('--v', ev.habit);
            $('#meter-val').textContent = ev.habit + ' %';
            $('#verdict').textContent = veredicto(ev.habit, ev.total);
            $('#dom').textContent = ev.dom ? `Predomina el ${alquimicos[ev.dom].nombre}` : 'Sin elemento alquímico predominante';
            $('#tips').innerHTML = ev.avisos.map((t) => `<span>${esc(t)}</span>`).join('');

            const disco = $('#lab-disc');
            disco.classList.toggle('vacio', !ev.dom);
            disco.style.setProperty('--tint', colorMezcla(ev.alq, ev.alqTotal));
            disco.innerHTML = ev.dom ? iconoAlq(ev.dom) : '<span class="interro">?</span>';
        }

        /** Pide al servidor los indicadores de la mezcla actual (la última respuesta es la que vale). */
        function evaluar() {
            clearTimeout(temporizador);
            if (!total()) {
                pintarEvaluacion({ ind: { atmosfera: 0, agua: 0, suelo: 0, temperatura: 0, energia: 0 }, habit: 0, total: 0, dom: null, alq: {}, alqTotal: 0, avisos: ['Añade elementos con + para empezar a construir tu planeta.'] });
                return;
            }
            temporizador = setTimeout(async () => {
                const turno = ++ultimaPeticion;
                try {
                    const ev = await api(lab.dataset.evaluar, 'POST', { partes });
                    if (turno === ultimaPeticion) pintarEvaluacion(ev);
                } catch (e) { /* se mantiene el último resultado */ }
            }, 120);
        }

        function cambiar() {
            pintarCuentas();
            evaluar();
        }

        function pintarProgreso(n) {
            $('#progreso').textContent = n >= UMBRAL
                ? 'Todos los juegos se han desbloqueado.'
                : `Crea ${UMBRAL - n} más para desbloquear los otros juegos.`;
        }

        lab.addEventListener('click', async (e) => {
            const fila = e.target.closest('.mix-row');
            if (fila && e.target.closest('.mas')) {
                if (total() < MAX) {
                    partes[fila.dataset.id] = (partes[fila.dataset.id] || 0) + 1;
                    $('#msg').textContent = '';
                    cambiar();
                }
                return;
            }
            if (fila && e.target.closest('.menos')) {
                const id = fila.dataset.id;
                if (partes[id]) {
                    partes[id]--;
                    if (!partes[id]) delete partes[id];
                    cambiar();
                }
                return;
            }
            if (e.target.closest('#vaciar-mezcla')) {
                Object.keys(partes).forEach((k) => delete partes[k]);
                $('#msg').textContent = '';
                cambiar();
                return;
            }
            if (e.target.closest('#guardar')) {
                if (!total()) return;
                const boton = $('#guardar');
                boton.disabled = true;
                try {
                    const p = await api(lab.dataset.guardar, 'POST', { nombre: $('#nombre-planeta').value.trim(), partes });
                    const li = document.createElement('li');
                    li.dataset.id = p.id;
                    if (p.dominante) li.style.setProperty('--el', alquimicos[p.dominante].color);
                    li.innerHTML = `${p.dominante ? iconoAlq(p.dominante) : ''}<span class="nombre">${esc(p.nombre)}</span>
                        <span class="estado">${p.habitable ? 'Habitable' : 'No habitable'}</span>
                        <button type="button" class="link quitar" aria-label="Quitar ${esc(p.nombre)}">Quitar</button>`;
                    $('#creados-vacio')?.remove();
                    $('#creados').append(li);
                    pintarProgreso(p.total);
                    $('#msg').textContent = `«${p.nombre}» creado con ${p.habitabilidad} % de habitabilidad.`;
                    $('#nombre-planeta').value = '';
                    Object.keys(partes).forEach((k) => delete partes[k]);
                    cambiar();
                } catch (err) {
                    $('#msg').textContent = err.message || 'No se pudo crear el planeta.';
                    pintarCuentas();
                }
                return;
            }
            const quitar = e.target.closest('.quitar');
            if (quitar) {
                const li = quitar.closest('li');
                try {
                    await api(lab.dataset.eliminar.replace('__ID__', li.dataset.id), 'DELETE');
                    li.remove();
                    const quedan = $('#creados').querySelectorAll('li[data-id]').length;
                    if (!quedan) {
                        $('#creados').innerHTML = '<li class="progreso" id="creados-vacio">Todavía no has creado ninguno.</li>';
                    }
                    pintarProgreso(quedan);
                } catch (err) {
                    $('#msg').textContent = err.message || 'No se pudo quitar el planeta.';
                }
            }
        });

        pintarCuentas();
        evaluar();
    }
}
