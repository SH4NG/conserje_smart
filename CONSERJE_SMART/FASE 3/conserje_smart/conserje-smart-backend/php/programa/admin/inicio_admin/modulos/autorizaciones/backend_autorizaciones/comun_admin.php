<?php

require_once dirname(__DIR__, 6) . '/base_datos/conexion_postgresql.php';

function iniciarSesionAdmin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            ini_set('session.cookie_secure', '1');
        }
        session_start();
    }

    if (empty($_SESSION['csrf_admin'])) {
        $_SESSION['csrf_admin'] = bin2hex(random_bytes(32));
    }
}

/** @return never */
function responderAdmin(bool $exito, string $mensaje, int $codigo = 200, array $datos = [], array $extra = []): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode(
        array_merge(['exito' => $exito, 'mensaje' => $mensaje, 'datos' => $datos], $extra),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

function abrirBaseAdmin(): PDO
{
    return conectarBasePostgreSQL();
}

function consultarAdmin(PDO $bd, string $sql, array $valores = []): PDOStatement
{
    $consulta = $bd->prepare($sql);
    $consulta->execute($valores);
    return $consulta;
}

function enteroAdmin($valor): ?int
{
    $numero = filter_var($valor, FILTER_VALIDATE_INT);
    return $numero !== false && $numero > 0 ? $numero : null;
}

function textoAdmin(string $campo, int $maximo, bool $obligatorio = false): string
{
    $valor = $_POST[$campo] ?? '';
    if (!is_string($valor)) {
        responderAdmin(false, 'Formato de campo no válido.', 400);
    }
    $valor = trim($valor);
    if (($obligatorio && $valor === '') || mb_strlen($valor, 'UTF-8') > $maximo) {
        responderAdmin(false, 'Revisa los campos obligatorios y sus longitudes.', 400);
    }
    return $valor;
}

function ejecutarAdmin(string $metodo, callable $operacion): void
{
    header('Referrer-Policy: no-referrer');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $metodo) {
        header('Allow: ' . $metodo);
        responderAdmin(false, 'Método no permitido.', 405);
    }

    iniciarSesionAdmin();
    $idUsuario = enteroAdmin($_SESSION['id_usuario'] ?? null);
    $idCondominio = enteroAdmin($_SESSION['id_condominio_fk'] ?? null);
    if (($_SESSION['estado'] ?? '') !== 'sesion_iniciada' || !$idUsuario || !$idCondominio) {
        responderAdmin(false, 'Inicia sesión para continuar.', 401);
    }
    if (($_SESSION['rol_usuario'] ?? '') !== 'admin') {
        responderAdmin(false, 'Acceso exclusivo del administrador.', 403);
    }
    if ($metodo === 'POST') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_admin'], $token)) {
            responderAdmin(false, 'Recarga la página para renovar el formulario.', 403);
        }
    }

    session_write_close();
    $bd = null;
    try {
        $bd = abrirBaseAdmin();
        $validar = consultarAdmin(
            $bd,
            "SELECT id_usuario FROM usuario
             WHERE id_usuario = ? AND id_condominio_fk = ? AND rol_usuario = 'admin'",
            [$idUsuario, $idCondominio]
        );
        if ($validar->fetchColumn() === false) {
            responderAdmin(false, 'La sesión de administrador ya no es válida.', 403);
        }
        $operacion($bd, $idCondominio, $idUsuario);
    } catch (Throwable $error) {
        if ($bd instanceof PDO && $bd->inTransaction()) {
            $bd->rollBack();
        }
        if ($error instanceof PDOException && $error->getCode() === '23505') {
            responderAdmin(false, 'El nombre o número ya existe. Revisa los registros del condominio.', 409);
        }
        error_log('Administrador: ' . $error->getMessage());
        responderAdmin(false, 'No se pudo completar la operación. Revisa la conexión y la migración de Administración.', 503);
    }
}

function paginacionAdmin(): array
{
    $pagina = enteroAdmin($_GET['pagina'] ?? 1);
    $busqueda = $_GET['buscar'] ?? '';
    if (!$pagina || $pagina > 100000 || !is_string($busqueda) || mb_strlen($busqueda, 'UTF-8') > 100) {
        responderAdmin(false, 'Filtros no válidos.', 400);
    }
    return [$pagina, '%' . trim($busqueda) . '%', 25, ($pagina - 1) * 25];
}

function listadoAdmin(PDO $bd, string $desde, string $columnas, string $orden, array $valores): void
{
    [$pagina, , $limite, $offset] = paginacionAdmin();
    $contar = consultarAdmin($bd, 'SELECT COUNT(*) ' . $desde, $valores);
    $total = (int) $contar->fetchColumn();
    $consulta = consultarAdmin(
        $bd,
        'SELECT ' . $columnas . ' ' . $desde . ' ORDER BY ' . $orden . ' LIMIT ? OFFSET ?',
        array_merge($valores, [$limite, $offset])
    );
    responderAdmin(true, 'Registros consultados.', 200, $consulta->fetchAll(), [
        'pagina' => $pagina,
        'total' => $total,
        'hay_mas' => $offset + $limite < $total
    ]);
}

function versionBitacoraAdmin(array $fila): string
{
    unset($fila['version']);
    return hash('sha256', json_encode($fila, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

function versionInfraAdmin(array $fila): string
{
    unset($fila['version']);
    return hash('sha256', json_encode($fila, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
}

function torreInfraAdmin(PDO $bd, int $id, int $condominio, bool $bloquear = false): array
{
    $consulta = consultarAdmin(
        $bd,
        'SELECT * FROM torre WHERE id_torre = ? AND id_condominio_fk = ?' . ($bloquear ? ' FOR UPDATE' : ''),
        [$id, $condominio]
    );
    $torre = $consulta->fetch();
    if (!$torre) {
        responderAdmin(false, 'La torre no pertenece a tu condominio.', 404);
    }
    return $torre;
}

function pisoInfraAdmin(array $torre, $valor): int
{
    $piso = filter_var($valor, FILTER_VALIDATE_INT);
    if ($torre['cantidad_pisos'] === null) {
        responderAdmin(false, 'Define primero los pisos de esta torre desde Administración.', 409);
    }
    $inicio = (int) $torre['piso_inicial'];
    if ($piso === false || $piso < $inicio || $piso >= $inicio + (int) $torre['cantidad_pisos']) {
        responderAdmin(false, 'El piso está fuera del rango configurado para esta torre.', 400);
    }
    return $piso;
}

function urlRegistroInvitacionAdmin(): ?string
{
    $base = trim((string) getenv('CONSERJE_URL_PUBLICA'));
    if ($base === '') {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (!preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/D', $host)) {
            return null;
        }
        $base = 'http://' . $host . '/conserje_smart/conserje-smart-frontend';
    }
    $partes = parse_url($base);
    if (
        !filter_var($base, FILTER_VALIDATE_URL) ||
        !is_array($partes) ||
        empty($partes['host']) ||
        isset($partes['user']) ||
        isset($partes['pass']) ||
        isset($partes['query']) ||
        isset($partes['fragment']) ||
        (($partes['scheme'] ?? '') !== 'https' && !(($partes['scheme'] ?? '') === 'http'
            && in_array($partes['host'], ['localhost', '127.0.0.1'], true)))
    ) {
        return null;
    }
    return rtrim($base, '/') . '/php/inicio_sesion/registrarse/registrarse.php';
}

function consultarInvitacionAdmin(PDO $bd, string $token, bool $bloquear = false): array
{
    if (!preg_match('/^[a-f0-9]{64}$/D', $token)) {
        responderAdmin(false, 'La invitación no es válida.', 400);
    }
    $consulta = consultarAdmin(
        $bd,
        "SELECT i.*, c.nombre_condominio,
            (i.vence_en <= timezone('UTC', now())) AS vencida
         FROM invitacion_conserje i
         INNER JOIN condominio c ON c.id_condominio = i.id_condominio_fk
         INNER JOIN usuario u ON u.id_usuario = i.id_admin_fk
            AND u.id_condominio_fk = i.id_condominio_fk AND u.rol_usuario = 'admin'
         WHERE i.token_hash = ?" . ($bloquear ? ' FOR UPDATE OF i' : ''),
        [hash('sha256', $token)]
    );
    $fila = $consulta->fetch();
    if (!$fila || $fila['estado'] !== 'activa' || $fila['vencida'] === true || $fila['vencida'] === 't') {
        responderAdmin(false, 'La invitación venció, fue utilizada o se revocó. Solicita otra al administrador.', 410);
    }
    return $fila;
}
