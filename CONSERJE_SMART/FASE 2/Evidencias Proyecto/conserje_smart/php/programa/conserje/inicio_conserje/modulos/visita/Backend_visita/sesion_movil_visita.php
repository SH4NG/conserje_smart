<?php
// TITULO 1 PREPARAR LA SESION Y EL TOKEN DEL FORMULARIO
function iniciarSesionVisita(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        // el origen HTTPS configurado también cubre un proxy TLS del alojamiento
        $hostPublico = parse_url((string) getenv('VISITA_APP_MOVIL_URL'), PHP_URL_HOST);
        $hostPeticion = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($hostPublico && strcasecmp($hostPublico, (string) $hostPeticion) === 0)) {
            ini_set('session.cookie_secure', '1');
        }
        session_start();
    }
    if (empty($_SESSION['csrf_visita'])) {
        $_SESSION['csrf_visita'] = bin2hex(random_bytes(32));
    }
}

// TITULO 2 RECONOCER EL ACCESO NORMAL DEL CONSERJE
function sesionEscritorioVisita(): ?array
{
    $usuario = enteroPositivoVisita($_SESSION['id_usuario'] ?? null);
    $condominio = enteroPositivoVisita($_SESSION['id_condominio_fk'] ?? null);
    if (($_SESSION['estado'] ?? '') !== 'sesion_iniciada'
        || ($_SESSION['rol_usuario'] ?? '') !== 'conserje' || !$usuario || !$condominio) {
        return null;
    }
    return ['id_usuario' => $usuario, 'id_condominio' => $condominio, 'movil' => false];
}

// TITULO 3 VALIDAR EL TOKEN DE LAS OPERACIONES DE ESCRITURA
function validarTokenFormularioVisita(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_visita']) || !hash_equals($_SESSION['csrf_visita'], $token)) {
        responderVisita(false, 'Recarga la página para renovar el formulario.', 403);
    }
}

// TITULO 4 CONECTAR LA BASE DE DATOS DE VISITA
function abrirBaseVisita(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $bd = new mysqli('localhost', 'root', '', getenv('CONSERJE_DB_NOMBRE') ?: 'conserje_smart_bd');
    $bd->set_charset('utf8mb4');
    return $bd;
}

// TITULO 5 IDENTIFICAR EL COMPUTADOR QUE AUTORIZA EL MOVIL
function origenSesionVisita(): string
{
    return hash('sha256', session_id());
}

// TITULO 6 VALIDAR LA DIRECCION HTTPS CONFIGURADA POR EL SERVIDOR
function urlAplicacionMovilVisita(): ?string
{
    $url = trim((string) getenv('VISITA_APP_MOVIL_URL'));
    $partes = parse_url($url);
    $ruta = '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/appMovil_visita.php';
    if (!filter_var($url, FILTER_VALIDATE_URL) || !is_array($partes)
        || ($partes['scheme'] ?? '') !== 'https' || ($partes['path'] ?? '') !== $ruta
        || empty($partes['host']) || in_array(strtolower($partes['host']), ['localhost', '::1', '[::1]'], true)
        || preg_match('/^127\./', $partes['host'])
        || isset($partes['user']) || isset($partes['pass']) || isset($partes['query']) || isset($partes['fragment'])) {
        return null;
    }
    return $url;
}

// TITULO 7 LEER EL VINCULO DEL NAVEGADOR MOVIL
function consultarVinculoMovilVisita(mysqli $bd): ?array
{
    $id = enteroPositivoVisita($_SESSION['visita_movil_vinculo'] ?? null);
    $nonce = $_SESSION['visita_movil_nonce'] ?? '';
    if (!$id || !is_string($nonce) || !preg_match('/^[a-f0-9]{64}$/D', $nonce)) {
        return null;
    }
    $hash = hash('sha256', $nonce);
    $consulta = $bd->prepare(
        "SELECT v.id_vinculo, v.id_usuario_fk, v.id_condominio_fk, v.estado, v.codigo_confirmacion,
            v.vence_en, CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje,
            c.nombre_condominio
         FROM visita_vinculo_movil v
         INNER JOIN usuario u ON u.id_usuario = v.id_usuario_fk AND u.id_condominio_fk = v.id_condominio_fk
         INNER JOIN condominio c ON c.id_condominio = v.id_condominio_fk
         WHERE v.id_vinculo = ? AND v.solicitud_hash = ? AND u.rol_usuario = 'conserje'
           AND v.vence_en > UTC_TIMESTAMP() AND v.estado <> 'revocado'
           AND (v.estado <> 'activo' OR v.ultimo_uso > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 MINUTE))"
    );
    $consulta->bind_param('is', $id, $hash);
    $consulta->execute();
    $fila = $consulta->get_result()->fetch_assoc();
    $consulta->close();
    return $fila ?: null;
}

// TITULO 8 OBTENER EL ACCESO LIMITADO DEL MOVIL
function accesoMovilVisita(mysqli $bd): ?array
{
    $fila = consultarVinculoMovilVisita($bd);
    if (!$fila || $fila['estado'] !== 'activo') {
        return null;
    }
    $id = (int) $fila['id_vinculo'];
    $actualizar = $bd->prepare("UPDATE visita_vinculo_movil SET ultimo_uso = UTC_TIMESTAMP() WHERE id_vinculo = ? AND estado = 'activo' AND vence_en > UTC_TIMESTAMP() AND ultimo_uso > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 MINUTE)");
    $actualizar->bind_param('i', $id);
    $actualizar->execute();
    $actualizar->close();
    return [
        'id_usuario' => (int) $fila['id_usuario_fk'],
        'id_condominio' => (int) $fila['id_condominio_fk'],
        'movil' => true,
        'id_vinculo' => $id,
        'nombre_conserje' => $fila['nombre_conserje'],
        'nombre_condominio' => $fila['nombre_condominio'],
        'vence_en' => $fila['vence_en']
    ];
}

// TITULO 9 REVOCAR LOS MOVILES AL CERRAR LA SESION DEL COMPUTADOR
function revocarMovilesDelEscritorioVisita(mysqli $bd): void
{
    $origen = origenSesionVisita();
    $consulta = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'revocado' WHERE sesion_origen_hash = ? AND estado <> 'revocado'");
    $consulta->bind_param('s', $origen);
    $consulta->execute();
    $consulta->close();
}
