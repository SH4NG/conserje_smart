<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('CONSERJE_DB_HOST') ?: 'localhost';
$usuario = getenv('CONSERJE_DB_USUARIO') ?: 'root';
$contrasena = getenv('CONSERJE_DB_CONTRASENA') ?: '';
$nombreBase = getenv('CONSERJE_DB_NOMBRE') ?: 'conserje_smart_bd';

try {
    $mysqli = new mysqli($host, $usuario, $contrasena, $nombreBase);
    $mysqli->set_charset('utf8mb4');
    $mysqli->begin_transaction();

    $usuarios = $mysqli->query(
        'SELECT id_usuario, contraseña FROM usuario FOR UPDATE'
    );
    $actualizar = $mysqli->prepare(
        'UPDATE usuario SET contraseña = ? WHERE id_usuario = ?'
    );

    $migradas = 0;
    $yaProtegidas = 0;
    $sinContrasena = 0;

    while ($fila = $usuarios->fetch_assoc()) {
        if ($fila['contraseña'] === null || $fila['contraseña'] === '') {
            $sinContrasena++;
            continue;
        }

        $informacionHash = password_get_info($fila['contraseña']);
        if ($informacionHash['algo'] !== null) {
            $yaProtegidas++;
            continue;
        }

        if (strlen($fila['contraseña']) > 72) {
            throw new RuntimeException(
                'La cuenta con ID ' . $fila['id_usuario'] . ' requiere restablecer su contraseña antes de migrar.'
            );
        }

        $hash = password_hash($fila['contraseña'], PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException(
                'No se pudo generar el hash para la cuenta con ID ' . $fila['id_usuario'] . '.'
            );
        }

        $idUsuario = (int) $fila['id_usuario'];
        $actualizar->bind_param('si', $hash, $idUsuario);
        $actualizar->execute();
        $migradas++;
    }

    $actualizar->close();
    $usuarios->free();
    $mysqli->commit();
    $mysqli->close();

    echo "Contraseñas migradas: {$migradas}" . PHP_EOL;
    echo "Ya protegidas: {$yaProtegidas}" . PHP_EOL;
    echo "Sin contraseña: {$sinContrasena}" . PHP_EOL;
} catch (Throwable $error) {
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        try {
            $mysqli->rollback();
            $mysqli->close();
        } catch (Throwable $errorCierre) {
            error_log('No se pudo cerrar la conexión después del error de migración.');
        }
    }

    error_log('Falló la migración de contraseñas: ' . $error->getMessage());
    fwrite(STDERR, 'No se pudieron migrar las contraseñas. Revisa el log de PHP.' . PHP_EOL);
    exit(1);
}
