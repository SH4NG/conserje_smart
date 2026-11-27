// TITULO 1 IMPORTAR LAS FUNCIONES COMPARTIDAS DEL ADMINISTRADOR
import { crearListadoAdmin, celdaAdmin, fechaAdmin, estadoAdmin, departamentoAdmin } from '../autorizaciones/backend_autorizaciones/api_admin.js';

// TITULO 2 CONSULTAR ENTREGAS Y RETIROS SIN MODIFICARLOS
export function iniciarPaqueteriaAdmin() {
    crearListadoAdmin('paqueteria', 'listar_paquetes_admin.php', 5, (fila, dato) => {
        const registro = celdaAdmin(fila, '');
        estadoAdmin(registro, dato.estado_paquete, dato.estado_paquete === 'Pendiente' ? 'pendiente_admin' : 'neutral_admin');
        const numero = document.createElement('small');
        numero.textContent = '#' + dato.id_paquete + (dato.fecha_eliminacion ? ' · Archivado' : '');
        registro.append(numero);
        celdaAdmin(fila, departamentoAdmin(dato));
        celdaAdmin(fila, dato.empresa_envio, [dato.codigo_seguimiento, dato.descripcion_paquete].filter(Boolean).join(' · '));
        celdaAdmin(fila, fechaAdmin(dato.fecha_recepcion), 'Retiro: ' + fechaAdmin(dato.fecha_retiro));
        celdaAdmin(fila, dato.nombre_conserje);
    });
}
