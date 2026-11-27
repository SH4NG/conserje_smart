<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [, $buscar, $limite, $offset] = paginacionAdmin();
    $desde = "FROM bitacora b
        LEFT JOIN usuario u ON u.id_usuario = b.id_usuario_fk AND u.id_condominio_fk = b.id_condominio_fk
        WHERE b.id_condominio_fk = ?
            AND (b.titulo ILIKE ? OR b.descripcion ILIKE ? OR COALESCE(b.lugar, '') ILIKE ?)";
    $valores = [$condominio, $buscar, $buscar, $buscar];
    $total = (int) consultarAdmin($bd, 'SELECT COUNT(*) ' . $desde, $valores)->fetchColumn();
    $consulta = consultarAdmin(
        $bd,
        "SELECT b.*, CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje
         $desde
         ORDER BY b.fecha_novedad DESC, b.hora_novedad DESC, b.id_bitacora DESC
         LIMIT ? OFFSET ?",
        array_merge($valores, [$limite, $offset])
    );
    $novedades = $consulta->fetchAll();
    foreach ($novedades as &$novedad) {
        $novedad['version'] = versionBitacoraAdmin($novedad);
    }
    unset($novedad);
    responderAdmin(true, 'Bitácora consultada.', 200, $novedades, [
        'pagina' => (int) ($_GET['pagina'] ?? 1),
        'total' => $total,
        'hay_mas' => $offset + $limite < $total
    ]);
});
