<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 VALIDAR LOS FILTROS Y LA PAGINA SOLICITADA
ejecutarVisita('GET', function (PDO $bd, int $idCondominio): void {
    $estado = $_GET['estado'] ?? 'activas';
    $pagina = enteroPositivoVisita($_GET['pagina'] ?? 1);
    $idVisita = isset($_GET['id_visita']) ? enteroPositivoVisita($_GET['id_visita']) : null;
    if (!in_array($estado, ['activas', 'finalizadas', 'todas'], true) || !$pagina || $pagina > 1000000) {
        responderVisita(false, 'El estado o la página solicitada no son válidos.', 400);
    }
    if (isset($_GET['id_visita']) && !$idVisita) {
        responderVisita(false, 'El número del registro no es válido.', 400);
    }
    $limite = 25;
    $offset = ($pagina - 1) * $limite;
    $filtro = $estado === 'activas' ? ' AND v.fecha_hora_salida IS NULL'
        : ($estado === 'finalizadas' ? ' AND v.fecha_hora_salida IS NOT NULL' : '');
    $filtro .= $idVisita !== null ? ' AND v.id_visita = ?' : '';

    // TITULO 3 CONTAR LAS VISITAS DEL ESTADO SELECCIONADO
    // el filtro procede de valores fijos y los datos de la solicitud se enlazan
    $consultaTotal = $bd->prepare('SELECT COUNT(*) AS total FROM visita v WHERE v.id_condominio_fk = ?' . $filtro);
    $parametrosTotal = [$idCondominio];
    if ($idVisita !== null) {
        $parametrosTotal[] = $idVisita;
    }
    $consultaTotal->execute($parametrosTotal);
    $total = (int) $consultaTotal->fetchColumn();

    // TITULO 4 CONSULTAR LAS VISITAS Y SUS DEPARTAMENTOS
    $consulta = $bd->prepare(
        'SELECT v.id_visita, v.id_departamento_fk, v.nombre_visitante, v.rut_visitante,
            v.motivo_visita, v.observaciones_visita, v.fecha_hora_ingreso, v.fecha_hora_salida,
            d.numero_departamento, d.torre_departamento, d.piso_departamento
         FROM visita v
         INNER JOIN departamento d ON d.id_departamento = v.id_departamento_fk AND d.id_condominio_fk = v.id_condominio_fk
         WHERE v.id_condominio_fk = ?' . $filtro . '
         ORDER BY v.fecha_hora_ingreso DESC, v.id_visita DESC LIMIT ? OFFSET ?'
    );
    $parametros = [$idCondominio];
    if ($idVisita !== null) {
        $parametros[] = $idVisita;
    }
    $parametros[] = $limite;
    $parametros[] = $offset;
    $consulta->execute($parametros);
    
    // TITULO 5 PREPARAR LAS FILAS DEL TABLERO
    $visitas = [];
    foreach ($consulta->fetchAll() as $fila) {
        $fila['id_visita'] = (int) $fila['id_visita'];
        $fila['id_departamento_fk'] = (int) $fila['id_departamento_fk'];
        $fila['piso_departamento'] = $fila['piso_departamento'] !== null ? (int) $fila['piso_departamento'] : null;
        $visitas[] = $fila;
    }
    // TITULO 6 CERRAR LA CONSULTA Y RESPONDER CON LA PAGINACION
    responderVisita(true, 'Visitas consultadas correctamente.', 200, $visitas, [
        'pagina' => $pagina, 'total' => $total, 'hay_mas' => $offset + $limite < $total
    ]);
});
