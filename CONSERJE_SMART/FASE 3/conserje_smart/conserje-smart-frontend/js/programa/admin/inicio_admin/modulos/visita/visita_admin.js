// TITULO 1 IMPORTAR LAS FUNCIONES COMPARTIDAS DEL ADMINISTRADOR
import { crearListadoAdmin, celdaAdmin, fechaAdmin, estadoAdmin, departamentoAdmin } from '../autorizaciones/backend_autorizaciones/api_admin.js';

// TITULO 2 CONSULTAR EL HISTORIAL DE VISITAS DEL CONDOMINIO
export function iniciarVisitaAdmin() {
    crearListadoAdmin('visita', 'listar_visitas_admin.php', 6, (fila, dato) => {
        const registro = celdaAdmin(fila, '');
        estadoAdmin(registro, dato.fecha_hora_salida ? 'Finalizada' : 'En el condominio', dato.fecha_hora_salida ? 'neutral_admin' : 'aprobada_admin');
        const numero = document.createElement('small');
        numero.textContent = '#' + dato.id_visita;
        registro.append(numero);
        celdaAdmin(fila, dato.nombre_visitante, dato.rut_visitante);
        celdaAdmin(fila, departamentoAdmin(dato));
        celdaAdmin(fila, dato.motivo_visita, dato.observaciones_visita);
        celdaAdmin(fila, fechaAdmin(dato.fecha_hora_ingreso), 'Salida: ' + fechaAdmin(dato.fecha_hora_salida));
        celdaAdmin(fila, dato.nombre_conserje);
    });
}
