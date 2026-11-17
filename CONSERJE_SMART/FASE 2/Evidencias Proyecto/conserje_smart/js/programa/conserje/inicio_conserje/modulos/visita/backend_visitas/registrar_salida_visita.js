// TITULO 1 IMPORTAR LAS PETICIONES Y NOTIFICACIONES
import { solicitarVisita, notificarCambioVisita } from './api_visita.js';

// TITULO 2 SOLICITAR EL REGISTRO DE SALIDA DE LA VISITA
export async function registrarSalidaVisita(idVisita) {
    const datos = new FormData();
    datos.set('id_visita', String(idVisita));
    const resultado = await solicitarVisita('registrar_salida_visita.php', { datos });
    notificarCambioVisita();
    return resultado;
}
