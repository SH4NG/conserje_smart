<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [, $buscar] = paginacionAdmin();
    listadoAdmin(
        $bd,
        "FROM usuario u
         LEFT JOIN solicitud_registro_conserje s
            ON s.id_usuario_fk = u.id_usuario AND s.id_condominio_fk = u.id_condominio_fk
         WHERE u.id_condominio_fk = ? AND u.rol_usuario = 'conserje'
           AND (CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) ILIKE ?
                OR u.rut_usuario ILIKE ? OR COALESCE(u.correo, '') ILIKE ?)",
        "u.id_usuario, u.nombre_usuario, u.apellido_usuario, u.rut_usuario, u.correo,
         CASE WHEN s.id_solicitud IS NULL THEN 'existente' ELSE s.estado END AS estado,
         s.fecha_solicitud, s.fecha_resolucion, s.id_invitacion_fk",
        'u.apellido_usuario, u.nombre_usuario, u.id_usuario',
        [$condominio, $buscar, $buscar, $buscar]
    );
});
