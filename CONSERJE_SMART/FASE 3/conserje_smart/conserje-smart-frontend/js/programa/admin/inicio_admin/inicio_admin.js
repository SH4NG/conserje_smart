// TITULO 1 IMPORTAR LAS PETICIONES COMPARTIDAS DEL ADMINISTRADOR
import { pedirAdmin, mensajeAdmin } from './modulos/autorizaciones/backend_autorizaciones/api_admin.js';

// TITULO 2 INICIALIZAR EL PANEL Y SUS SECCIONES
const pagina = document.getElementById('pagina_admin');
const mensaje = document.getElementById('mensaje_global_admin');
const modulos = {
    infraestructura: [() => import('./modulos/infraestructura/infraestructura_admin.js'), 'iniciarInfraestructuraAdmin'],
    bitacora: [() => import('./modulos/bitacora/bitacora_admin.js'), 'iniciarBitacoraAdmin'],
    paqueteria: [() => import('./modulos/paqueteria/paqueteria_admin.js'), 'iniciarPaqueteriaAdmin'],
    visita: [() => import('./modulos/visita/visita_admin.js'), 'iniciarVisitaAdmin'],
    autorizaciones: [() => import('./modulos/autorizaciones/autorizaciones_admin.js'), 'iniciarAutorizacionesAdmin']
};
const iniciados = new Map();
let actualizacion;
document.getElementById('fecha_admin').textContent = new Intl.DateTimeFormat('es-CL', {
    timeZone: 'America/Santiago', weekday: 'long', day: 'numeric', month: 'long'
}).format(new Date());

// TITULO 3 MOSTRAR LA SECCION SIN RECARGAR NI PERDER EL HISTORIAL
async function mostrarSeccion(clave, guardar = false) {
    if (clave !== 'resumen' && !Object.hasOwn(modulos, clave)) clave = 'resumen';
    document.querySelectorAll('dialog[open]').forEach(dialogo => dialogo.close());
    pagina.dataset.seccion = clave;
    document.querySelectorAll('[data-panel-admin]').forEach(panel => { panel.hidden = panel.dataset.panelAdmin !== clave; });
    document.querySelectorAll('.navegacion_admin [data-seccion-admin]').forEach(enlace => {
        if (enlace.dataset.seccionAdmin === clave) enlace.setAttribute('aria-current', 'page');
        else enlace.removeAttribute('aria-current');
    });
    if (guardar) {
        const url = new URL(location.href);
        if (clave === 'resumen') url.searchParams.delete('seccion');
        else url.searchParams.set('seccion', clave);
        history.pushState(null, '', url);
    }
    if (clave === 'resumen') return actualizarResumen();
    if (iniciados.has(clave)) {
        await iniciados.get(clave);
        document.dispatchEvent(new CustomEvent('admin:seccion', { detail: clave }));
        return;
    }
    const [cargar, iniciar] = modulos[clave];
    const tarea = cargar().then(modulo => modulo[iniciar]());
    iniciados.set(clave, tarea);
    try { await tarea; }
    catch (error) {
        iniciados.delete(clave);
        mensajeAdmin(mensaje, 'No se pudo abrir esta sección. Recarga la página para reintentar.', 'error');
        console.error('Administrador:', error);
    }
}
document.addEventListener('click', evento => {
    const enlace = evento.target.closest('a[data-seccion-admin]');
    if (!enlace || evento.ctrlKey || evento.metaKey || evento.shiftKey || evento.altKey || evento.button !== 0) return;
    evento.preventDefault();
    mensajeAdmin(mensaje);
    mostrarSeccion(enlace.dataset.seccionAdmin, true);
});
window.addEventListener('popstate', () => mostrarSeccion(new URL(location.href).searchParams.get('seccion') || 'resumen'));

// TITULO 4 MOSTRAR LOS CONTADORES REALES DEL CONDOMINIO
async function actualizarResumen() {
    actualizacion?.abort();
    const solicitud = new AbortController();
    actualizacion = solicitud;
    try {
        const [resumen, bandeja] = await Promise.all([
            pedirAdmin('autorizaciones', 'resumen_admin.php', { signal: solicitud.signal }),
            pedirAdmin('autorizaciones', 'listar_solicitudes_admin.php', { parametros: { estado: 'pendiente' }, signal: solicitud.signal })
        ]);
        if (solicitud.signal.aborted) return;
        document.getElementById('nombre_condominio_admin').textContent = resumen.datos.condominio;
        document.querySelectorAll('[data-total-admin]').forEach(elemento => {
            elemento.textContent = resumen.datos.totales[elemento.dataset.totalAdmin];
        });
        mensajeAdmin(mensaje);
        const lista = document.getElementById('lista_resumen_solicitudes_admin');
        lista.replaceChildren();
        if (!bandeja.datos.length) {
            const vacio = document.createElement('p');
            vacio.className = 'estado_vacio_admin';
            vacio.textContent = 'Todo al día. No hay solicitudes pendientes de revisión.';
            lista.append(vacio);
        }
        for (const dato of bandeja.datos.slice(0, 4)) {
            const enlace = document.createElement('a');
            enlace.href = '?seccion=autorizaciones';
            enlace.dataset.seccionAdmin = 'autorizaciones';
            enlace.className = 'solicitud_resumen_admin';
            const nombre = document.createElement('strong');
            nombre.textContent = dato.nombre_usuario + ' ' + dato.apellido_usuario;
            const detalle = document.createElement('span');
            detalle.textContent = 'Solicita acceso como conserje · ' + dato.rut_usuario;
            const accion = document.createElement('span');
            accion.className = 'estado_registro_admin pendiente_admin';
            accion.textContent = 'Revisar →';
            enlace.append(nombre, detalle, accion);
            lista.append(enlace);
        }
    } catch (error) {
        if (error.name === 'AbortError') return;
        mensajeAdmin(mensaje, error.message, 'error');
        document.getElementById('nombre_condominio_admin').textContent = 'No se pudo actualizar la comunidad';
        document.getElementById('lista_resumen_solicitudes_admin').textContent = 'No se pudo consultar la bandeja. Abre Autorizaciones para reintentar.';
        document.querySelectorAll('[data-total-admin]').forEach(elemento => { elemento.textContent = '—'; });
    }
}

// TITULO 5 SINCRONIZAR EL PANEL AL CAMBIAR LOS REGISTROS
document.addEventListener('admin:actualizado', actualizarResumen);
document.addEventListener('visibilitychange', () => { if (!document.hidden) actualizarResumen(); });
const temporizador = setInterval(() => {
    if (!document.hidden) {
        actualizarResumen();
        if (pagina.dataset.seccion === 'autorizaciones' && !document.querySelector('dialog[open]')) {
            document.dispatchEvent(new CustomEvent('admin:seccion', { detail: 'autorizaciones' }));
        }
    }
}, 30000);
window.addEventListener('pagehide', () => { clearInterval(temporizador); actualizacion?.abort(); });
mostrarSeccion(pagina.dataset.seccion);
if (pagina.dataset.seccion !== 'resumen') actualizarResumen();
