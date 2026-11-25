// TITULO 1 IMPORTAR LAS CONSULTAS EXISTENTES DE VISITA
import { listarVisitas, etiquetaDepartamento } from './modulos/visita/Backend_visita/listar_visita.js';

// TITULO 2 MOSTRAR LA FECHA DEL INGRESO EN EL HORARIO DE SANTIAGO
export function fechaActividadInicio(valor, ahora = new Date()) {
    const partes = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/.exec(valor || '');
    if (!partes) return 'Fecha no disponible';
    const calendario = Object.fromEntries(new Intl.DateTimeFormat('en-CA', {
        timeZone: 'America/Santiago', year: 'numeric', month: '2-digit', day: '2-digit'
    }).formatToParts(ahora).map(({ type, value }) => [type, value]));
    const hoy = `${calendario.year}-${calendario.month}-${calendario.day}`;
    const ayer = new Date(Date.UTC(Number(calendario.year), Number(calendario.month) - 1, Number(calendario.day) - 1)).toISOString().slice(0, 10);
    const dia = valor.slice(0, 10);
    const etiqueta = dia === hoy ? 'Hoy' : dia === ayer ? 'Ayer' : `${partes[3]}/${partes[2]}/${partes[1]}`;
    return `${etiqueta}, ${partes[4]}:${partes[5]}`;
}

// TITULO 3 CREAR ICONOS SIN DEPENDENCIAS EXTERNAS
function iconoActividadInicio(tipo) {
    const espacio = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(espacio, 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.7');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');
    const trazos = {
        persona: ['M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z', 'M4 21v-2a8 8 0 0 1 16 0v2H4Z'],
        reloj: ['M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'M12 7v5l3 2'],
        flecha: ['m9 5 7 7-7 7']
    };
    for (const dibujo of trazos[tipo]) {
        const path = document.createElementNS(espacio, 'path');
        path.setAttribute('d', dibujo);
        svg.appendChild(path);
    }
    return svg;
}

// TITULO 4 INICIALIZAR LOS ELEMENTOS DE NOVEDADES
export function inicializarActividadesInicio() {
    const lista = document.getElementById('lista_actividades_inicio');
    if (!lista) return;
    const contador = document.getElementById('contador_actividades_inicio');
    const mensaje = document.getElementById('mensaje_actividades_inicio');
    const actualizar = document.getElementById('actualizar_actividades_inicio');
    let solicitud = null;
    let temporizador;
    let sinPermiso = false;
    let ultimaVersion = null;

    // TITULO 5 DIBUJAR LOS CINCO INGRESOS MAS RECIENTES
    // los nombres, RUT y departamentos siempre se incorporan como texto
    function dibujar(visitas) {
        const fragmento = document.createDocumentFragment();
        for (const visita of visitas) {
            const fila = document.createElement('li');
            const enlace = document.createElement('a');
            enlace.className = 'actividad_item actividad_visita_inicio';
            enlace.href = `/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/visita/visita.php?visita=${encodeURIComponent(visita.id_visita)}#contenedor_tablero_visita`;
            const icono = document.createElement('span');
            icono.className = 'actividad_icono actividad_visita';
            icono.appendChild(iconoActividadInicio('persona'));
            const contenido = document.createElement('div');
            contenido.className = 'detalle_actividad_inicio';
            const titulo = document.createElement('strong');
            titulo.textContent = 'Visita registrada';
            const descripcion = document.createElement('p');
            descripcion.textContent = `Ingreso de visitante: ${visita.nombre_visitante} (RUT ${visita.rut_visitante})`;
            const departamento = document.createElement('p');
            departamento.textContent = etiquetaDepartamento(visita);
            contenido.append(titulo, descripcion, departamento);
            const fecha = document.createElement('span');
            fecha.className = 'fecha_actividad_inicio';
            fecha.appendChild(iconoActividadInicio('reloj'));
            const hora = document.createElement('time');
            hora.dateTime = visita.fecha_hora_ingreso.replace(' ', 'T');
            hora.textContent = fechaActividadInicio(visita.fecha_hora_ingreso);
            hora.title = 'Hora de Santiago: ' + visita.fecha_hora_ingreso;
            fecha.appendChild(hora);
            const flecha = iconoActividadInicio('flecha');
            flecha.classList.add('flecha_actividad_inicio');
            enlace.append(icono, contenido, fecha, flecha);
            fila.appendChild(enlace);
            fragmento.appendChild(fila);
        }
        lista.replaceChildren(fragmento);
    }

    // TITULO 6 CONSULTAR SOLO LAS VISITAS DEL CONDOMINIO AUTENTICADO
    async function cargar({ manual = false } = {}) {
        clearTimeout(temporizador);
        if (solicitud || document.hidden || sinPermiso || !navigator.onLine) return;
        const actual = new AbortController();
        const limiteEspera = setTimeout(() => actual.abort(new DOMException('La consulta tardó demasiado.', 'TimeoutError')), 15000);
        solicitud = actual;
        actualizar.disabled = true;
        lista.setAttribute('aria-busy', 'true');
        if (manual || ultimaVersion === null) mensaje.textContent = 'Cargando visitas recientes...';
        try {
            const resultado = await listarVisitas({ estado: 'todas', pagina: 1, signal: actual.signal });
            const recientes = resultado.datos.slice(0, 5);
            const version = JSON.stringify(recientes);
            // evita reemplazar un enlace que el conserje tiene enfocado
            if (version !== ultimaVersion) {
                dibujar(recientes);
                ultimaVersion = version;
            }
            contador.hidden = false;
            contador.textContent = `${recientes.length} ${recientes.length === 1 ? 'reciente' : 'recientes'}`;
            mensaje.classList.remove('error');
            mensaje.textContent = recientes.length ? 'Mostrando los últimos registros de visitas.' : 'Todavía no hay visitas registradas en este condominio.';
        } catch (error) {
            if (error.name === 'AbortError') return;
            if ([401, 403].includes(error.codigoHttp)) {
                sinPermiso = true;
                lista.replaceChildren();
                contador.hidden = true;
                ultimaVersion = null;
            }
            mensaje.classList.add('error');
            mensaje.textContent = sinPermiso
                ? 'Tu sesión no permite consultar las visitas. Inicia sesión como conserje nuevamente.'
                : 'No se pudieron actualizar las visitas. ' + (ultimaVersion === null ? 'Pulsa Actualizar para reintentar.' : 'Se conservan los últimos datos recibidos; pulsa Actualizar para reintentar.');
        } finally {
            clearTimeout(limiteEspera);
            if (solicitud === actual) {
                solicitud = null;
                actualizar.disabled = sinPermiso;
                lista.setAttribute('aria-busy', 'false');
                programarActualizacion();
            }
        }
    }

    // TITULO 7 ACTUALIZAR MIENTRAS EL INICIO ESTE VISIBLE
    function programarActualizacion() {
        clearTimeout(temporizador);
        if (!document.hidden && !sinPermiso && navigator.onLine) temporizador = setTimeout(cargar, 30000);
    }
    actualizar.addEventListener('click', () => cargar({ manual: true }));
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearTimeout(temporizador);
            solicitud?.abort();
        } else {
            cargar();
        }
    });
    window.addEventListener('offline', () => {
        clearTimeout(temporizador);
        solicitud?.abort();
        mensaje.textContent = 'Sin conexión. Las novedades se actualizarán cuando vuelva la conexión.';
    });
    window.addEventListener('online', () => cargar());
    window.addEventListener('pageshow', () => cargar());
    window.addEventListener('pagehide', () => { clearTimeout(temporizador); solicitud?.abort(); });
    document.addEventListener('visita:actualizada', () => cargar());
    if (navigator.onLine) cargar();
    else {
        lista.setAttribute('aria-busy', 'false');
        mensaje.textContent = 'Sin conexión. Conéctate para consultar las visitas recientes.';
    }
}

// TITULO 8 INICIAR LAS NOVEDADES CON LA ESTRUCTURA DISPONIBLE
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarActividadesInicio, { once: true });
} else {
    inicializarActividadesInicio();
}
