<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio, int $administrador): void {
    $nombre = consultarAdmin(
        $bd,
        'SELECT nombre_condominio FROM condominio WHERE id_condominio = ?',
        [$condominio]
    )->fetchColumn();
    $identidad = consultarAdmin(
        $bd,
        "SELECT rut_usuario AS rut, CONCAT(nombre_usuario, ' ', apellido_usuario) AS nombre
         FROM usuario
         WHERE id_usuario = ? AND id_condominio_fk = ? AND rol_usuario = 'admin'",
        [$administrador, $condominio]
    )->fetch();
    if (!$identidad) {
        responderAdmin(false, 'La cuenta de administrador ya no está disponible.', 403);
    }

    $consultas = [
        'infraestructura' => 'SELECT COUNT(*) FROM departamento WHERE id_condominio_fk = ?',
        'bitacora' => 'SELECT COUNT(*) FROM bitacora WHERE id_condominio_fk = ?',
        'paqueteria' => "SELECT COUNT(*) FROM paquete WHERE id_condominio_fk = ?
            AND estado_paquete = 'Pendiente' AND fecha_eliminacion IS NULL",
        'visita' => 'SELECT COUNT(*) FROM visita WHERE id_condominio_fk = ? AND fecha_hora_salida IS NULL',
        'autorizaciones' => "SELECT COUNT(*) FROM solicitud_registro_conserje s
            INNER JOIN usuario u ON u.id_usuario = s.id_usuario_fk AND u.id_condominio_fk = s.id_condominio_fk
            WHERE s.id_condominio_fk = ? AND s.estado = 'pendiente' AND u.rol_usuario = 'conserje'"
    ];
    $totales = [];
    foreach ($consultas as $clave => $sql) {
        $totales[$clave] = (int) consultarAdmin($bd, $sql, [$condominio])->fetchColumn();
    }
    responderAdmin(true, 'Panel actualizado.', 200, [
        'condominio' => $nombre ?: 'Condominio',
        'totales' => $totales,
        'administrador' => $identidad
    ]);
});
