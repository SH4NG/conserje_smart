// TITULO 1 IMPORTAR LAS PETICIONES COMPARTIDAS
import { solicitarVisita } from './api_visita.js';

// TITULO 2 SOLICITAR LAS VISITAS DEL CONDOMINIO
export async function listarVisitas({ estado = 'activas', pagina = 1, idVisita, signal } = {}) {
    const parametros = { estado, pagina };
    if (idVisita != null) parametros.id_visita = idVisita;
    const resultado = await solicitarVisita('listar_visita.php', { parametros, signal });
    if (!Array.isArray(resultado.datos)) {
        throw new Error('No se pudo leer la lista de visitas.');
    }
    return resultado;
}

// TITULO 3 SOLICITAR LOS DEPARTAMENTOS DISPONIBLES
export async function listarDepartamentos() {
    const resultado = await solicitarVisita('listar_departamento_visita.php');
    if (!Array.isArray(resultado.datos)) {
        throw new Error('No se pudo leer la lista de departamentos.');
    }
    return resultado.datos;
}

// TITULO 4 FORMAR LA ETIQUETA DEL DEPARTAMENTO
export function etiquetaDepartamento(departamento) {
    return [
        departamento.torre_departamento ? `Torre ${departamento.torre_departamento}` : '',
        `Depto. ${departamento.numero_departamento}`,
        departamento.piso_departamento != null ? `Piso ${departamento.piso_departamento}` : ''
    ].filter(Boolean).join(' · ');
}
