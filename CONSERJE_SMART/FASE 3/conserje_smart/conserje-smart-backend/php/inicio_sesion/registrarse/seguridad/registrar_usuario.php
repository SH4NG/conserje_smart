<?php

// TITULO 1 RESPUESTA JSON
// define el formato de salida del endpoint de registro
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 RESPONDER REGISTRO
// devuelve una respuesta uniforme y finaliza la ejecucion del endpoint
/** @return never */
function responderRegistro(bool $exito, string $mensaje, int $codigoHttp = 200): void
{
    // establece el codigo HTTP que recibira el navegador
    http_response_code($codigoHttp);
    // devuelve el resultado y el mensaje en formato JSON
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// TITULO 3 VALIDAR METODO
// permite solamente peticiones POST provenientes del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderRegistro(false, 'Método de solicitud no permitido.', 405);
}

// TITULO 4 CONEXION BASE DE DATOS
// establece la conexion PostgreSQL mediante la configuracion del servidor
require_once dirname(__DIR__, 3) . '/base_datos/conexion_postgresql.php';
try {
    $pdo = conectarBasePostgreSQL();
} catch (Throwable $error) {
    error_log('No se pudo conectar para registrar un usuario: ' . $error->getMessage());
    responderRegistro(false, 'No se pudo conectar con la base de datos.', 500);
}

// TITULO 5 RECIBIR DATOS
// obtiene y limpia los valores enviados por el formulario
$rut = trim($_POST['rut'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$rol = trim($_POST['rol'] ?? '');
$contraseñaRecibida = $_POST['contraseña'] ?? '';
$contraseña = is_string($contraseñaRecibida) ? $contraseñaRecibida : '';
$idCondominio = filter_var($_POST['condominio'] ?? null, FILTER_VALIDATE_INT);
$rutAdmin = trim($_POST['rut_admin'] ?? '');
$codigoAuthAdmin = trim($_POST['codigo_auth_admin'] ?? '');

// TITULO 6 VALIDAR CAMPOS OBLIGATORIOS
// comprueba que todos los datos necesarios hayan sido enviados
if (
    $rut === '' ||
    $nombre === '' ||
    $apellido === '' ||
    $correo === '' ||
    $rol === '' ||
    $contraseña === '' ||
    $rutAdmin === '' ||
    $codigoAuthAdmin === '' ||
    $idCondominio === false ||
    $idCondominio === null ||
    $idCondominio < 1
) {
    responderRegistro(false, 'Faltan datos obligatorios por ingresar.', 400);
}

// valida el formato general del correo recibido
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    responderRegistro(false, 'El correo electrónico no es válido.', 400);
}

// TITULO 7 VALIDAR ROL
// limita el registro a los roles definidos por el sistema
$rolesPermitidos = ['admin', 'conserje', 'residente'];
if (!in_array($rol, $rolesPermitidos, true)) {
    responderRegistro(false, 'El rol seleccionado no es válido.', 400);
}

// TITULO 8 VALIDAR LONGITUDES
// evita superar los tamaños definidos en la estructura de la base de datos
if (
    mb_strlen($rut) > 20 ||
    mb_strlen($nombre) > 100 ||
    mb_strlen($apellido) > 100 ||
    mb_strlen($telefono) > 20 ||
    mb_strlen($correo) > 100 ||
    mb_strlen($rol) > 50 ||
    mb_strlen($rutAdmin) > 20 ||
    mb_strlen($codigoAuthAdmin) > 255
) {
    responderRegistro(false, 'Uno o más datos superan la longitud permitida.', 400);
}

if (
    mb_strlen($contraseña, 'UTF-8') < 8 ||
    strlen($contraseña) > 72 ||
    !preg_match('/[A-Z]/', $contraseña) ||
    !preg_match('/[a-z]/', $contraseña) ||
    !preg_match('/\d/', $contraseña)
) {
    responderRegistro(false, 'La contraseña debe tener al menos 8 caracteres, máximo 72 bytes UTF-8 e incluir mayúscula, minúscula y número.', 400);
}

// TITULO 9 VALIDAR CONDOMINIO
// comprueba que el id recibido exista en la tabla condominio
$consultaCondominio = $pdo->prepare(
    'SELECT id_condominio FROM condominio WHERE id_condominio = ?'
);

try {
    $consultaCondominio->execute([$idCondominio]);
} catch (PDOException $error) {
    error_log('No se pudo validar el condominio del registro: ' . $error->getMessage());
    responderRegistro(false, 'No se pudo validar el condominio.', 500);
}

if (!$consultaCondominio->fetchColumn()) {
    responderRegistro(false, 'El condominio seleccionado no existe.', 400);
}

// TITULO 10 VALIDAR AUTORIZACION DEL ADMINISTRADOR
// acepta solo un administrador del mismo condominio con RUT y código coincidentes
$rutAdminNormalizado = strtoupper((string) preg_replace('/[^0-9kK]/', '', $rutAdmin));
$consultaAdmin = $pdo->prepare(
    'SELECT rut_usuario, codigo_auth_usuario
     FROM usuario
     WHERE id_condominio_fk = ? AND rol_usuario = \'admin\''
);

try {
    $consultaAdmin->execute([$idCondominio]);
    $administradores = $consultaAdmin->fetchAll();
} catch (PDOException $error) {
    error_log('No se pudo validar la autorización del administrador: ' . $error->getMessage());
    responderRegistro(false, 'No se pudo validar la autorización del administrador.', 500);
}

$adminAutorizado = false;

foreach ($administradores as $administrador) {
    $rutGuardadoNormalizado = strtoupper(
        (string) preg_replace('/[^0-9kK]/', '', $administrador['rut_usuario'])
    );

    if (
        $rutAdminNormalizado !== '' &&
        hash_equals($rutGuardadoNormalizado, $rutAdminNormalizado) &&
        hash_equals($administrador['codigo_auth_usuario'], $codigoAuthAdmin)
    ) {
        $adminAutorizado = true;
        break;
    }
}

if (!$adminAutorizado) {
    responderRegistro(false, 'El RUT o el código no corresponde a un administrador de este condominio.', 403);
}

// TITULO 11 VALIDAR DUPLICADOS
// comprueba que el RUT y el correo no esten registrados
$consultaDuplicado = $pdo->prepare(
    'SELECT rut_usuario, correo FROM usuario WHERE rut_usuario = ? OR correo = ? LIMIT 1'
);

try {
    $consultaDuplicado->execute([$rut, $correo]);
    $usuarioDuplicado = $consultaDuplicado->fetch();
} catch (PDOException $error) {
    error_log('No se pudo comprobar si el usuario ya existe: ' . $error->getMessage());
    responderRegistro(false, 'No se pudieron validar los datos del usuario.', 500);
}

if ($usuarioDuplicado !== false) {
    if ($usuarioDuplicado['rut_usuario'] === $rut) {
        responderRegistro(false, 'El RUT ya está registrado.', 409);
    }

    responderRegistro(false, 'El correo ya está registrado.', 409);
}

// TITULO 12 GENERAR CODIGO DE AUTENTICACION
// crea un codigo aleatorio para identificar el registro
$codigoAuth = bin2hex(random_bytes(32));
$hashContraseña = password_hash($contraseña, PASSWORD_DEFAULT);

if ($hashContraseña === false) {
    responderRegistro(false, 'No se pudo proteger la contraseña del usuario.', 500);
}

// TITULO 13 INSERTAR USUARIO
// inicia una transaccion para mantener consistente el registro
try {
    $pdo->beginTransaction();
    $insertarUsuario = $pdo->prepare(
        'INSERT INTO usuario
            (rut_usuario, nombre_usuario, apellido_usuario, telefono_usuario, correo, rol_usuario, codigo_auth_usuario, contraseña, id_condominio_fk)
         VALUES (?, ?, ?, NULLIF(?, \'\'), ?, ?, ?, ?, ?)
         RETURNING id_usuario'
    );
    $insertarUsuario->execute([
        $rut,
        $nombre,
        $apellido,
        $telefono,
        $correo,
        $rol,
        $codigoAuth,
        $hashContraseña,
        $idCondominio
    ]);

    $idUsuario = (int) $insertarUsuario->fetchColumn();
    $pdo->commit();
} catch (PDOException $error) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('No se pudo registrar el usuario: ' . $error->getMessage());
    if ($error->getCode() === '23505') {
        responderRegistro(false, 'El RUT o el correo ya está registrado.', 409);
    }
    responderRegistro(false, 'No se pudo registrar el usuario.', 500);
}

// TITULO 14 RESPUESTA EXITOSA
// informa al navegador que el usuario fue registrado
responderRegistro(true, 'Usuario registrado correctamente.');

?>
