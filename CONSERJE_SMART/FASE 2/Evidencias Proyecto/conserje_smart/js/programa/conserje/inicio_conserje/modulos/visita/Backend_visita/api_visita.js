// TITULO 1 DEFINIR LA RUTA DEL BACKEND DE VISITAS
const rutaBackend = '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/visita/Backend_visita/';

// TITULO 2 PREPARAR Y ENVIAR LA PETICION AL BACKEND
export async function solicitarVisita(archivo, { datos, parametros, signal } = {}) {
    const url = new URL(archivo, window.location.origin + rutaBackend);
    if (parametros) {
        url.search = new URLSearchParams(parametros).toString();
    }
    const headers = { Accept: 'application/json' };
    if (datos) {
        headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-visita"]')?.content || '';
    }
    const respuesta = await fetch(url, {
        method: datos ? 'POST' : 'GET',
        credentials: 'same-origin',
        headers,
        body: datos,
        signal
    });

    // TITULO 3 INTERPRETAR LA RESPUESTA Y CONTROLAR ERRORES
    let resultado;
    try {
        resultado = await respuesta.json();
    } catch {
        throw new Error('El servidor no devolvió una respuesta válida. Revisa tu sesión e inténtalo nuevamente.');
    }
    if (!respuesta.ok || resultado.exito !== true) {
        const error = new Error(resultado.mensaje || `No se pudo completar la solicitud (HTTP ${respuesta.status}).`);
        error.codigoHttp = respuesta.status;
        throw error;
    }
    return resultado;
}

// TITULO 4 MOSTRAR EL MENSAJE DE LA OPERACION
export function mostrarMensajeVisita(elemento, mensaje = '', tipo = '') {
    if (!elemento) {
        return;
    }
    elemento.textContent = mensaje;
    elemento.classList.remove('error', 'exito');
    if (tipo) {
        elemento.classList.add(tipo);
    }
}

// TITULO 5 NOTIFICAR LA ACTUALIZACION DEL TABLERO
export function notificarCambioVisita() {
    document.dispatchEvent(new CustomEvent('visita:actualizada'));
}
