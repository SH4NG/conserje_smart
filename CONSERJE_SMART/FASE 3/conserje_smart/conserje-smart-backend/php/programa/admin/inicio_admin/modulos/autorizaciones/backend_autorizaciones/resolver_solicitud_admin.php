<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio, int $administrador): void {
    $id = enteroAdmin($_POST['id_solicitud'] ?? null);
    $accion = textoAdmin('accion', 10, true);
    $motivo = textoAdmin('motivo', 500, $accion === 'rechazar');
    if (!$id || !in_array($accion, ['aprobar', 'rechazar'], true)) {
        responderAdmin(false, 'Solicitud o decisión no válida.', 400);
    }

    $bd->beginTransaction();
    $solicitud = consultarAdmin(
        $bd,
        "SELECT s.estado, s.id_usuario_fk, s.id_invitacion_fk,
                i.estado AS estado_invitacion, i.id_condominio_fk AS condominio_invitacion,
                i.id_usuario_fk AS usuario_invitacion
         FROM solicitud_registro_conserje s
         INNER JOIN usuario u ON u.id_usuario = s.id_usuario_fk
            AND u.id_condominio_fk = s.id_condominio_fk
         LEFT JOIN invitacion_conserje i ON i.id_invitacion = s.id_invitacion_fk
         WHERE s.id_solicitud = ? AND s.id_condominio_fk = ?
            AND u.rol_usuario = 'conserje'
         FOR UPDATE OF s",
        [$id, $condominio]
    )->fetch();
    if (!$solicitud) {
        $bd->rollBack();
        responderAdmin(false, 'La solicitud no está disponible en tu condominio.', 404);
    }
    if ($solicitud['estado'] !== 'pendiente') {
        $bd->rollBack();
        responderAdmin(false, 'Otro administrador ya resolvió esta solicitud. Actualiza la bandeja.', 409);
    }
    if (
        $solicitud['id_invitacion_fk'] !== null &&
        ($solicitud['estado_invitacion'] !== 'usada' ||
            (int) $solicitud['condominio_invitacion'] !== $condominio ||
            (int) $solicitud['usuario_invitacion'] !== (int) $solicitud['id_usuario_fk'])
    ) {
        $bd->rollBack();
        responderAdmin(false, 'La invitación y la asignación de la cuenta no coinciden. Revisa la solicitud.', 409);
    }

    if ($accion === 'aprobar') {
        $codigo = $_POST['codigo_auth_admin'] ?? '';
        if (!is_string($codigo) || trim($codigo) === '' || strlen($codigo) > 255) {
            $bd->rollBack();
            responderAdmin(false, 'Ingresa tu código personal de autorización para aprobar la cuenta.', 400, [
                'campo' => 'codigo_auth_admin'
            ]);
        }
        $guardado = consultarAdmin(
            $bd,
            "SELECT codigo_auth_usuario FROM usuario
             WHERE id_usuario = ? AND id_condominio_fk = ? AND rol_usuario = 'admin'
             FOR UPDATE",
            [$administrador, $condominio]
        )->fetchColumn();
        $guardado = is_string($guardado) ? $guardado : '';
        $esHash = password_get_info($guardado)['algoName'] !== 'unknown';
        $correcto = $guardado !== '' &&
            ($esHash ? password_verify($codigo, $guardado) : hash_equals($guardado, $codigo));
        unset($codigo, $guardado);
        if (!$correcto) {
            $bd->rollBack();
            responderAdmin(false, 'El código de autorización no corresponde a tu cuenta de administrador.', 403, [
                'campo' => 'codigo_auth_admin'
            ]);
        }
    }

    $estado = $accion === 'aprobar' ? 'aprobada' : 'rechazada';
    $actualizar = consultarAdmin(
        $bd,
        "UPDATE solicitud_registro_conserje
         SET estado = ?, motivo_resolucion = NULLIF(?, ''),
             id_admin_resolutor_fk = ?, fecha_resolucion = timezone('UTC', now())
         WHERE id_solicitud = ? AND id_condominio_fk = ? AND estado = 'pendiente'",
        [$estado, $motivo, $administrador, $id, $condominio]
    );
    if ($actualizar->rowCount() !== 1) {
        $bd->rollBack();
        responderAdmin(false, 'Otro administrador ya resolvió esta solicitud. Actualiza la bandeja.', 409);
    }
    $bd->commit();
    responderAdmin(true, $estado === 'aprobada'
        ? 'Solicitud aprobada. El conserje ya puede iniciar sesión.'
        : 'Solicitud rechazada. La cuenta permanece sin acceso.', 200, [
            'id_solicitud' => $id,
            'estado' => $estado
        ]);
});
