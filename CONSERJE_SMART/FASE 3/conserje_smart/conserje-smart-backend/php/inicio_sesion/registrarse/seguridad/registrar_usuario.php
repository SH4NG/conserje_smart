<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once dirname(__DIR__, 3) . '/programa/admin/inicio_admin/modulos/autorizaciones/backend_autorizaciones/comun_admin.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responderAdmin(false, 'Método no permitido.', 405);
}

function rutRegistroAdmin(string $valor): ?string
{
    $rut = strtoupper((string) preg_replace('/[.\s-]/u', '', $valor));
    if (!preg_match('/^([0-9]{1,8})([0-9K])$/D', $rut, $partes) || (int) $partes[1] < 1) {
        return null;
    }
    $base = ltrim($partes[1], '0');
    $suma = 0;
    $factor = 2;
    for ($i = strlen($base) - 1; $i >= 0; $i--) {
        $suma += (int) $base[$i] * $factor;
        $factor = $factor === 7 ? 2 : $factor + 1;
    }
    $resto = 11 - ($suma % 11);
    $digito = $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);
    return $partes[2] === $digito ? $base . '-' . $digito : null;
}

$rut = rutRegistroAdmin(textoAdmin('rut', 20, true));
$nombre = preg_replace('/\s+/u', ' ', textoAdmin('nombre', 100, true));
$apellido = preg_replace('/\s+/u', ' ', textoAdmin('apellido', 100, true));
$telefono = textoAdmin('telefono', 32);
$correo = textoAdmin('correo', 100, true);
$rol = textoAdmin('rol', 50, true);
$clave = $_POST['contraseña'] ?? '';
$condominio = enteroAdmin($_POST['condominio'] ?? null);

if (!$rut) {
    responderAdmin(false, 'El RUT o su dígito verificador no es válido.', 400, ['campo' => 'rut']);
}
foreach (['nombre' => $nombre, 'apellido' => $apellido] as $campo => $valor) {
    if (!preg_match("/^[\p{L}\p{M}]+(?:[ '\\x{2019}-][\p{L}\p{M}]+)*$/uD", $valor)) {
        responderAdmin(false, 'El nombre y los apellidos admiten letras, espacios, apóstrofes y guiones.', 400, [
            'campo' => $campo
        ]);
    }
}
if ($telefono !== '') {
    if (
        !preg_match('/^[+0-9\s().-]+$/uD', $telefono) ||
        substr_count($telefono, '+') > 1 ||
        (strpos($telefono, '+') !== false && $telefono[0] !== '+')
    ) {
        responderAdmin(false, 'El teléfono no tiene un formato válido.', 400, ['campo' => 'telefono']);
    }
    $digitos = preg_replace('/\D/', '', $telefono);
    if ($telefono[0] !== '+' && preg_match('/^[0-9]{8}$/D', $digitos)) {
        $digitos = '569' . $digitos;
    } elseif ($telefono[0] !== '+' && preg_match('/^[2-9][0-9]{8}$/D', $digitos)) {
        $digitos = '56' . $digitos;
    }
    if (!preg_match('/^56[2-9][0-9]{8}$/D', $digitos)) {
        responderAdmin(false, 'Ingresa un teléfono chileno de 9 dígitos o con prefijo +56.', 400, [
            'campo' => 'telefono'
        ]);
    }
    $telefono = '+' . $digitos;
}
if (
    !is_string($clave) ||
    mb_strlen($clave, 'UTF-8') < 8 ||
    strlen($clave) > 72 ||
    strpos($clave, "\0") !== false ||
    !preg_match('/\p{Lu}/u', $clave) ||
    !preg_match('/\p{Ll}/u', $clave) ||
    !preg_match('/[0-9]/', $clave)
) {
    responderAdmin(false, 'La contraseña necesita al menos 8 caracteres, una mayúscula, una minúscula y un número.', 400, [
        'campo' => 'contraseña'
    ]);
}
if (
    !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
    !in_array($rol, ['admin', 'conserje', 'residente'], true) ||
    ($rol !== 'conserje' && !$condominio)
) {
    responderAdmin(false, 'Revisa correo, rol y condominio.', 400);
}

$bd = null;
try {
    $bd = abrirBaseAdmin();
    $bd->beginTransaction();
    $invitacion = null;
    if ($rol === 'conserje') {
        $token = textoAdmin('token_invitacion', 64, true);
        $invitacion = consultarInvitacionAdmin($bd, $token, true);
        $condominioAsignado = (int) $invitacion['id_condominio_fk'];
        if ($condominio && $condominio !== $condominioAsignado) {
            $bd->rollBack();
            responderAdmin(false, 'El condominio lo fija la invitación del administrador. No puedes cambiarlo.', 400);
        }
        $condominio = $condominioAsignado;
    }

    $existeCondominio = consultarAdmin(
        $bd,
        'SELECT 1 FROM condominio WHERE id_condominio = ?',
        [$condominio]
    )->fetchColumn();
    if ($existeCondominio === false) {
        $bd->rollBack();
        responderAdmin(false, 'El condominio no existe.', 400);
    }

    if ($rol !== 'conserje') {
        $rutAdmin = rutRegistroAdmin(textoAdmin('rut_admin', 20, true));
        $codigo = textoAdmin('codigo_auth_admin', 255, true);
        if (!$rutAdmin) {
            $bd->rollBack();
            responderAdmin(false, 'El RUT o su dígito verificador no es válido.', 400, ['campo' => 'rut']);
        }
        $autorizador = consultarAdmin(
            $bd,
            "SELECT codigo_auth_usuario
             FROM usuario
             WHERE rol_usuario = 'admin' AND id_condominio_fk = ?
               AND upper(regexp_replace(rut_usuario, '[.[:space:]-]', '', 'g')) = ?",
            [$condominio, str_replace('-', '', $rutAdmin)]
        )->fetchColumn();
        $autorizador = is_string($autorizador) ? $autorizador : '';
        $esHash = password_get_info($autorizador)['algoName'] !== 'unknown';
        $autorizado = $autorizador !== '' &&
            ($esHash ? password_verify($codigo, $autorizador) : hash_equals($autorizador, $codigo));
        unset($codigo, $autorizador);
        if (!$autorizado) {
            $bd->rollBack();
            responderAdmin(false, 'La autorización del administrador del condominio no es válida.', 403);
        }
    }

    $duplicado = consultarAdmin(
        $bd,
        "SELECT 1 FROM usuario
         WHERE correo = ?
            OR upper(regexp_replace(rut_usuario, '[.[:space:]-]', '', 'g')) = ?
         LIMIT 1",
        [$correo, str_replace('-', '', $rut)]
    )->fetchColumn();
    if ($duplicado !== false) {
        $bd->rollBack();
        responderAdmin(false, 'El RUT o correo ya está registrado.', 409);
    }

    $hash = password_hash($clave, PASSWORD_BCRYPT);
    if ($hash === false) {
        $bd->rollBack();
        responderAdmin(false, 'No se pudo proteger la contraseña del usuario.', 500);
    }
    $codigoAuth = bin2hex(random_bytes(32));
    $idUsuario = (int) consultarAdmin(
        $bd,
        "INSERT INTO usuario
            (rut_usuario, nombre_usuario, apellido_usuario, telefono_usuario, correo,
             rol_usuario, codigo_auth_usuario, contraseña, id_condominio_fk)
         VALUES (?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?, ?)
         RETURNING id_usuario",
        [$rut, $nombre, $apellido, $telefono, $correo, $rol, $codigoAuth, $hash, $condominio]
    )->fetchColumn();

    if ($rol === 'conserje') {
        consultarAdmin(
            $bd,
            "INSERT INTO solicitud_registro_conserje
                (id_usuario_fk, id_condominio_fk, id_invitacion_fk, estado)
             VALUES (?, ?, ?, 'pendiente')",
            [$idUsuario, $condominio, (int) $invitacion['id_invitacion']]
        );
        $actualizarInvitacion = consultarAdmin(
            $bd,
            "UPDATE invitacion_conserje
             SET estado = 'usada', id_usuario_fk = ?, usada_en = timezone('UTC', now())
             WHERE id_invitacion = ? AND id_condominio_fk = ?
                AND estado = 'activa' AND vence_en > timezone('UTC', now())",
            [$idUsuario, (int) $invitacion['id_invitacion'], $condominio]
        );
        if ($actualizarInvitacion->rowCount() !== 1) {
            $bd->rollBack();
            responderAdmin(false, 'La invitación ya no está vigente. Solicita otra al administrador.', 410);
        }
    }

    $bd->commit();
    responderAdmin(
        true,
        $rol === 'conserje'
            ? 'Solicitud enviada. Podrás iniciar sesión cuando el administrador de tu condominio la apruebe.'
            : 'Usuario registrado correctamente.',
        201,
        ['estado' => $rol === 'conserje' ? 'pendiente' : 'activo', 'correo' => $correo]
    );
} catch (Throwable $error) {
    if ($bd instanceof PDO && $bd->inTransaction()) {
        $bd->rollBack();
    }
    if ($error instanceof PDOException && $error->getCode() === '23505') {
        responderAdmin(false, 'El RUT o correo ya está registrado.', 409);
    }
    if ($error instanceof PDOException && $error->getCode() === '23503') {
        responderAdmin(false, 'El condominio o la invitación ya no están disponibles.', 409);
    }
    error_log('Registro de usuario: ' . $error->getMessage());
    responderAdmin(false, 'No se pudo registrar la solicitud. Comprueba la conexión y la migración de Administración.', 503);
}
