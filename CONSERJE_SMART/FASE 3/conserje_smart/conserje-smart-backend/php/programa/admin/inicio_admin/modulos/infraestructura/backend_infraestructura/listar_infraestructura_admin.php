<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [$pagina, $buscar, $limite, $offset] = paginacionAdmin();
    $torres = consultarAdmin(
        $bd,
        'SELECT * FROM torre WHERE id_condominio_fk = ? ORDER BY nombre',
        [$condominio]
    )->fetchAll();
    foreach ($torres as &$torre) {
        $torre['version'] = versionInfraAdmin($torre);
    }
    unset($torre);

    $filtro = $_GET['torre'] ?? '';
    if (!is_string($filtro)) {
        responderAdmin(false, 'Filtro de torre no válido.', 400);
    }
    $desde = 'FROM departamento WHERE id_condominio_fk = ?
        AND (numero_departamento ILIKE ? OR torre_departamento ILIKE ?)';
    $valores = [$condominio, $buscar, $buscar];
    if ($filtro === 'sin_torre') {
        $desde .= ' AND id_torre_fk IS NULL';
    } elseif ($filtro !== '') {
        $idTorre = enteroAdmin($filtro);
        if (!$idTorre) {
            responderAdmin(false, 'Filtro de torre no válido.', 400);
        }
        torreInfraAdmin($bd, $idTorre, $condominio);
        $desde .= ' AND id_torre_fk = ?';
        $valores[] = $idTorre;
    }

    $total = (int) consultarAdmin($bd, 'SELECT COUNT(*) ' . $desde, $valores)->fetchColumn();
    $departamentos = consultarAdmin(
        $bd,
        'SELECT * ' . $desde .
            ' ORDER BY torre_departamento, piso_departamento, numero_departamento, id_departamento
              LIMIT ? OFFSET ?',
        array_merge($valores, [$limite, $offset])
    )->fetchAll();
    foreach ($departamentos as &$departamento) {
        $departamento['version'] = versionInfraAdmin($departamento);
    }
    unset($departamento);

    $totales = consultarAdmin(
        $bd,
        'SELECT COUNT(*) AS departamentos,
                COUNT(*) FILTER (WHERE id_torre_fk IS NULL) AS sin_torre
         FROM departamento WHERE id_condominio_fk = ?',
        [$condominio]
    )->fetch();
    $totales['torres'] = count($torres);
    $totales['torres_sin_configurar'] = count(array_filter(
        $torres,
        static function (array $torre): bool {
            return $torre['cantidad_pisos'] === null;
        }
    ));

    responderAdmin(true, 'Infraestructura consultada.', 200, [
        'torres' => $torres,
        'departamentos' => $departamentos,
        'totales' => $totales
    ], [
        'pagina' => $pagina,
        'total' => $total,
        'hay_mas' => $offset + $limite < $total
    ]);
});
