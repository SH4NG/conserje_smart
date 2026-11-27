// TITULO 1 IMPORTAR LAS PETICIONES PROTEGIDAS DEL ADMINISTRADOR
import { pedirAdmin } from '../../autorizaciones/backend_autorizaciones/api_admin.js';

// TITULO 2 CONSULTAR LA ESTRUCTURA DEL CONDOMINIO
export function listarInfraestructuraAdmin(parametros, signal) {
    return pedirAdmin('infraestructura', 'listar_infraestructura_admin.php', { parametros, signal });
}

// TITULO 3 GUARDAR LA CONFIGURACION DE TORRES Y DEPARTAMENTOS
export function guardarTorreAdmin(datos) {
    return pedirAdmin('infraestructura', 'guardar_torre_admin.php', { datos });
}
export function guardarDepartamentoAdmin(datos) {
    return pedirAdmin('infraestructura', 'guardar_departamento_admin.php', { datos });
}

// TITULO 4 PREVISUALIZAR Y CONFIRMAR UNA GENERACION
export function generarDepartamentosAdmin(datos, accion, firma = '') {
    datos.set('accion', accion);
    if (firma) datos.set('firma_previa', firma);
    return pedirAdmin('infraestructura', 'generar_departamentos_admin.php', { datos });
}
