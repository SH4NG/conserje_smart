<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 CONSULTAR LOS DEPARTAMENTOS DEL CONDOMINIO
ejecutarVisita('GET', function (PDO $bd, int $idCondominio): void {
    $consulta = $bd->prepare(
        'SELECT id_departamento, torre_departamento, numero_departamento, piso_departamento
         FROM departamento WHERE id_condominio_fk = ?
         ORDER BY torre_departamento ASC, piso_departamento IS NULL, piso_departamento ASC, numero_departamento ASC'
    );
    $consulta->execute([$idCondominio]);
    // TITULO 3 PREPARAR LOS DATOS PARA EL SELECTOR
    $departamentos = [];
    foreach ($consulta->fetchAll() as $fila) {
        $fila['id_departamento'] = (int) $fila['id_departamento'];
        $fila['piso_departamento'] = $fila['piso_departamento'] !== null ? (int) $fila['piso_departamento'] : null;
        $departamentos[] = $fila;
    }
    // TITULO 4 CERRAR LA CONSULTA Y ENVIAR LA RESPUESTA
    responderVisita(true, 'Departamentos consultados correctamente.', 200, $departamentos);
}, true);
