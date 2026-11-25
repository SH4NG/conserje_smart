// TITULO 1 IMPORTAR LAS PETICIONES Y NOTIFICACIONES
import { solicitarVisita, notificarCambioVisita } from './api_visita.js';

// TITULO 2 ENVIAR LOS DATOS MODIFICADOS DE LA VISITA
export async function modificarVisita(idVisita, datos) {
    datos.set('id_visita', String(idVisita));
    const resultado = await solicitarVisita('modificar_visita.php', { datos });
    notificarCambioVisita();
    return resultado;
}

// TITULO 3 CARGAR LA VISITA EN EL FORMULARIO DE EDICION
export function prepararEdicionVisita(visita) {
    document.dispatchEvent(new CustomEvent('visita:editar', { detail: visita }));
}
