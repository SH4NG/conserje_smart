<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio): void {
    $id = enteroAdmin($_POST['id_bitacora'] ?? null);
    $version = textoAdmin('version', 64, true);
    if (!$id || !preg_match('/^[a-f0-9]{64}$/D', $version)) {
        responderAdmin(false, 'Registro no válido.', 400);
    }

    $titulo = textoAdmin('titulo', 150, true);
    $descripcion = textoAdmin('descripcion', 10000, true);
    $categoria = textoAdmin('categoria', 40);
    $tipoLugar = textoAdmin('tipo_lugar', 40);
    $lugar = textoAdmin('lugar', 150);
    $observaciones = textoAdmin('observaciones', 5000);
    $fecha = textoAdmin('fecha_evento', 10);
    $hora = textoAdmin('hora_evento', 8);
    $fechaValidada = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    if (
        ($fecha !== '' && (!$fechaValidada || $fechaValidada->format('Y-m-d') !== $fecha)) ||
        ($hora !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/D', $hora))
    ) {
        responderAdmin(false, 'Fecha u hora del evento no válida.', 400);
    }

    $bd->beginTransaction();
    $anterior = consultarAdmin(
        $bd,
        "SELECT b.*, CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje
         FROM bitacora b
         LEFT JOIN usuario u ON u.id_usuario = b.id_usuario_fk AND u.id_condominio_fk = b.id_condominio_fk
         WHERE b.id_bitacora = ? AND b.id_condominio_fk = ?
         FOR UPDATE OF b",
        [$id, $condominio]
    )->fetch();
    if (!$anterior) {
        $bd->rollBack();
        responderAdmin(false, 'Novedad no disponible en tu condominio.', 404);
    }
    if (!hash_equals(versionBitacoraAdmin($anterior), $version)) {
        $bd->rollBack();
        responderAdmin(false, 'La novedad cambió desde que la abriste. Actualiza antes de editar.', 409);
    }

    consultarAdmin(
        $bd,
        'UPDATE bitacora
         SET titulo = ?, descripcion = ?, categoria = NULLIF(?, \'\'),
             tipo_lugar = NULLIF(?, \'\'), lugar = NULLIF(?, \'\'),
             observaciones = NULLIF(?, \'\'), fecha_evento = ?, hora_evento = ?,
             fecha_actualizacion = timezone(\'UTC\', now())
         WHERE id_bitacora = ? AND id_condominio_fk = ?',
        [
            $titulo,
            $descripcion,
            $categoria,
            $tipoLugar,
            $lugar,
            $observaciones,
            $fecha === '' ? null : $fecha,
            $hora === '' ? null : $hora,
            $id,
            $condominio
        ]
    );
    $bd->commit();
    responderAdmin(true, 'Novedad actualizada. Se conservaron su autor y fecha de registro.');
});
