<?php

// TITULO 1 RESPUESTA JSON
// define el formato de salida del endpoint de registro
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 RESPONDER REGISTRO
// devuelve una respuesta uniforme y finaliza la ejecucion del endpoint
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
// establece la conexion local con la base de datos del proyecto
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

// verifica si la conexion produjo un error
if ($mysqli->connect_error) {
    responderRegistro(false, 'No se pudo conectar con la base de datos.', 500);
}

// configura la codificacion para recibir correctamente caracteres especiales
$mysqli->set_charset('utf8mb4');

// TITULO 5 RECIBIR DATOS
// obtiene y limpia los valores enviados por el formulario
$rut = trim($_POST['rut'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$rol = trim($_POST['rol'] ?? '');
$contraseña = trim($_POST['contraseña'] ?? '');
$idCondominio = filter_var($_POST['condominio'] ?? null, FILTER_VALIDATE_INT);

// TITULO 6 VALIDAR CAMPOS OBLIGATORIOS
// comprueba que todos los datos necesarios hayan sido enviados
if (
    $rut === '' ||
    $nombre === '' ||
    $apellido === '' ||
    $correo === '' ||
    $rol === '' ||
    $contraseña === '' ||
    $idCondominio === false ||
    $idCondominio === null ||
    $idCondominio < 1
) {
    $mysqli->close();
    responderRegistro(false, 'Faltan datos obligatorios por ingresar.', 400);
}

// valida el formato general del correo recibido
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $mysqli->close();
    responderRegistro(false, 'El correo electrónico no es válido.', 400);
}

// TITULO 7 VALIDAR ROL
// limita el registro a los roles definidos por el sistema
$rolesPermitidos = ['admin', 'conserje', 'residente'];
if (!in_array($rol, $rolesPermitidos, true)) {
    $mysqli->close();
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
    mb_strlen($contraseña) > 255
) {
    $mysqli->close();
    responderRegistro(false, 'Uno o más datos superan la longitud permitida.', 400);
}

// TITULO 9 VALIDAR CONDOMINIO
// comprueba que el id recibido exista en la tabla condominio
$consultaCondominio = $mysqli->prepare(
    'SELECT id_condominio FROM condominio WHERE id_condominio = ?'
);

if ($consultaCondominio === false) {
    $mysqli->close();
    responderRegistro(false, 'No se pudo validar el condominio.', 500);
}

$consultaCondominio->bind_param('i', $idCondominio);
// ejecuta la consulta preparada para validar la relacion
$consultaCondominio->execute();
$resultadoCondominio = $consultaCondominio->get_result();
$consultaCondominio->close();

if ($resultadoCondominio->num_rows === 0) {
    $mysqli->close();
    responderRegistro(false, 'El condominio seleccionado no existe.', 400);
}

// TITULO 10 VALIDAR DUPLICADOS
// comprueba que el RUT y el correo no esten registrados
$consultaDuplicado = $mysqli->prepare(
    'SELECT rut_usuario, correo FROM usuario WHERE rut_usuario = ? OR correo = ? LIMIT 1'
);

if ($consultaDuplicado === false) {
    $mysqli->close();
    responderRegistro(false, 'No se pudieron validar los datos del usuario.', 500);
}

$consultaDuplicado->bind_param('ss', $rut, $correo);
// ejecuta la consulta que busca coincidencias
$consultaDuplicado->execute();
$resultadoDuplicado = $consultaDuplicado->get_result();
$usuarioDuplicado = $resultadoDuplicado->fetch_assoc();
$consultaDuplicado->close();

if ($usuarioDuplicado !== null) {
    $mysqli->close();

    if ($usuarioDuplicado['rut_usuario'] === $rut) {
        responderRegistro(false, 'El RUT ya está registrado.', 409);
    }

    responderRegistro(false, 'El correo ya está registrado.', 409);
}

// TITULO 11 GENERAR CODIGO DE AUTENTICACION
// crea un codigo aleatorio para identificar el registro
$codigoAuth = bin2hex(random_bytes(32));

// TITULO 12 INSERTAR USUARIO
// inicia una transaccion para mantener consistente el registro
$mysqli->begin_transaction();

// prepara la insercion con parametros para evitar inyecciones SQL
$insertarUsuario = $mysqli->prepare(
    'INSERT INTO usuario
        (rut_usuario, nombre_usuario, apellido_usuario, telefono_usuario, correo, rol_usuario, codigo_auth_usuario, contraseña, id_condominio_fk)
     VALUES (?, ?, ?, NULLIF(?, \'\'), ?, ?, ?, ?, ?)'
);

if ($insertarUsuario === false) {
    $mysqli->rollback();
    $mysqli->close();
    responderRegistro(false, 'No se pudo preparar el registro del usuario.', 500);
}

$insertarUsuario->bind_param(
    'ssssssssi',
    $rut,
    $nombre,
    $apellido,
    $telefono,
    $correo,
    $rol,
    $codigoAuth,
    $contraseña,
    $idCondominio
);

// ejecuta la insercion del nuevo usuario
if (!$insertarUsuario->execute()) {
    $insertarUsuario->close();
    $mysqli->rollback();
    $mysqli->close();
    responderRegistro(false, 'No se pudo registrar el usuario.', 500);
}

// conserva el id generado por la base de datos
$idUsuario = $mysqli->insert_id;
$insertarUsuario->close();
$mysqli->commit();
$mysqli->close();

// TITULO 13 RESPUESTA EXITOSA
// informa al navegador que el usuario fue registrado
responderRegistro(true, 'Usuario registrado correctamente.');

?>
