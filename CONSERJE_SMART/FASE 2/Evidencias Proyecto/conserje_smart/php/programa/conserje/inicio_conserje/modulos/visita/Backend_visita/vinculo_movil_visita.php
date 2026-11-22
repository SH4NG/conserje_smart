<?php
// TITULO 1 PREPARAR LA RESPUESTA Y LA SESION DE VINCULACION
require_once __DIR__ . '/comun_visita.php';
header('Referrer-Policy: no-referrer');
iniciarSesionVisita();
$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'] ?? '';
$lectura = in_array($accion, ['configuracion', 'estado'], true);
if ((!$lectura && $metodo !== 'POST') || ($lectura && $metodo !== 'GET')) {
    header('Allow: ' . ($lectura ? 'GET' : 'POST'));
    responderVisita(false, 'Método no permitido.', 405);
}
if (!$lectura) {
    validarTokenFormularioVisita();
}

// TITULO 2 CONSULTAR EL ESTADO PUBLICO SIN MOSTRAR DATOS DEL CONSERJE
if ($accion === 'estado' && !sesionEscritorioVisita() && empty($_SESSION['visita_movil_vinculo'])) {
    responderVisita(true, 'Escanea el QR que aparece en el computador.', 200, [
        'estado' => 'sin_vinculo', 'csrf' => $_SESSION['csrf_visita']
    ]);
}

try {
    $bd = abrirBaseVisita();
    $escritorio = sesionEscritorioVisita();
    $idUsuario = $escritorio['id_usuario'] ?? 0;
    $idCondominio = $escritorio['id_condominio'] ?? 0;
    $origen = origenSesionVisita();

    // TITULO 3 CONSULTAR LA DIRECCION MOVIL SIN TOMARLA DEL NAVEGADOR
    if ($accion === 'configuracion') {
        if (!$escritorio) {
            responderVisita(false, 'Inicia sesión como conserje en el computador.', 401);
        }
        $url = urlAplicacionMovilVisita();
        responderVisita(true, 'Configuración de vinculación.', 200, [
            'disponible' => $url !== null,
            'mensaje' => $url ? 'Genera un QR para vincular tu teléfono.' : 'Falta configurar la dirección HTTPS de la app móvil en el servidor.',
            'url_movil' => $url
        ]);
    }

    // TITULO 4 CREAR UN QR TEMPORAL PARA EL CONSERJE AUTENTICADO
    if ($accion === 'crear') {
        if (!$escritorio) {
            responderVisita(false, 'Inicia sesión como conserje en el computador.', 401);
        }
        $url = urlAplicacionMovilVisita();
        if ($url === null) {
            responderVisita(false, 'Configura VISITA_APP_MOVIL_URL con la dirección HTTPS accesible desde el teléfono.', 503);
        }
        // revoca solamente las solicitudes pendientes de este navegador
        $anterior = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'revocado' WHERE sesion_origen_hash = ? AND estado IN ('pendiente','solicitado','aprobado')");
        $anterior->bind_param('s', $origen);
        $anterior->execute();
        $anterior->close();
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $crear = $bd->prepare(
            "INSERT INTO visita_vinculo_movil (id_usuario_fk, id_condominio_fk, sesion_origen_hash, token_hash, creado_en, vence_en)
             VALUES (?, ?, ?, ?, UTC_TIMESTAMP(), DATE_ADD(UTC_TIMESTAMP(), INTERVAL 3 MINUTE))"
        );
        $crear->bind_param('iiss', $idUsuario, $idCondominio, $origen, $hash);
        $crear->execute();
        $id = (int) $bd->insert_id;
        responderVisita(true, 'Escanea el QR y confirma el código que aparece en ambos dispositivos.', 201, [
            'id_vinculo' => $id, 'url' => $url . '#vinculo=' . $token, 'segundos' => 180
        ]);
    }

    // TITULO 5 RECIBIR LA SOLICITUD DEL TELEFONO SIN OTORGAR ACCESO
    if ($accion === 'solicitar') {
        if ($escritorio) {
            responderVisita(false, 'Este navegador ya tiene una sesión de conserje. Abre la app directamente o cierra esa sesión antes de vincular otra.', 409);
        }
        $token = textoVisita('token');
        if (!preg_match('/^[a-f0-9]{64}$/D', $token)) {
            responderVisita(false, 'El enlace de vinculación no es válido.', 400);
        }
        $hash = hash('sha256', $token);
        $nonce = bin2hex(random_bytes(32));
        $solicitudHash = hash('sha256', $nonce);
        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $consulta = $bd->prepare(
            "UPDATE visita_vinculo_movil SET estado = 'solicitado', solicitud_hash = ?, codigo_confirmacion = ?
             WHERE token_hash = ? AND estado = 'pendiente' AND vence_en > UTC_TIMESTAMP()"
        );
        $consulta->bind_param('sss', $solicitudHash, $codigo, $hash);
        $consulta->execute();
        if ($consulta->affected_rows !== 1) {
            responderVisita(false, 'El QR venció o ya fue utilizado. Genera uno nuevo desde el computador.', 410);
        }
        $leer = $bd->prepare('SELECT id_vinculo FROM visita_vinculo_movil WHERE token_hash = ?');
        $leer->bind_param('s', $hash);
        $leer->execute();
        $id = (int) $leer->get_result()->fetch_assoc()['id_vinculo'];
        // una nueva vinculación reemplaza el permiso anterior de este teléfono
        $anteriorId = enteroPositivoVisita($_SESSION['visita_movil_vinculo'] ?? null);
        $anteriorNonce = $_SESSION['visita_movil_nonce'] ?? '';
        if ($anteriorId && is_string($anteriorNonce)) {
            $anteriorHash = hash('sha256', $anteriorNonce);
            $anterior = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'revocado' WHERE id_vinculo = ? AND solicitud_hash = ?");
            $anterior->bind_param('is', $anteriorId, $anteriorHash);
            $anterior->execute();
        }
        $_SESSION['visita_movil_vinculo'] = $id;
        $_SESSION['visita_movil_nonce'] = $nonce;
        responderVisita(true, 'Comprueba este código en el computador y autoriza la conexión.', 200, [
            'estado' => 'solicitado', 'codigo' => $codigo, 'csrf' => $_SESSION['csrf_visita']
        ]);
    }

    // TITULO 6 MOSTRAR AL COMPUTADOR SOLO SUS PROPIOS VINCULOS
    if ($accion === 'estado' && $escritorio) {
        // también permite abrir la app en un navegador que ya inició sesión
        if (!isset($_GET['id_vinculo'])) {
            $consulta = $bd->prepare("SELECT CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje, c.nombre_condominio FROM usuario u INNER JOIN condominio c ON c.id_condominio = u.id_condominio_fk WHERE u.id_usuario = ? AND u.id_condominio_fk = ? AND u.rol_usuario = 'conserje'");
            $consulta->bind_param('ii', $idUsuario, $idCondominio);
            $consulta->execute();
            $datos = $consulta->get_result()->fetch_assoc();
            if (!$datos) responderVisita(false, 'La sesión del conserje ya no es válida.', 401);
            responderVisita(true, 'Sesión de conserje iniciada en este navegador.', 200, array_merge($datos, [
                'estado' => 'activo', 'sesion_completa' => true, 'csrf' => $_SESSION['csrf_visita']
            ]));
        }
        $id = enteroPositivoVisita($_GET['id_vinculo'] ?? null);
        if (!$id) {
            responderVisita(false, 'Selecciona un vínculo válido.', 400);
        }
        $consulta = $bd->prepare(
            "SELECT id_vinculo, estado, codigo_confirmacion, vence_en,
                (vence_en <= UTC_TIMESTAMP() OR (estado = 'activo' AND ultimo_uso <= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 MINUTE))) AS vencido
             FROM visita_vinculo_movil WHERE id_vinculo = ? AND id_usuario_fk = ? AND id_condominio_fk = ? AND sesion_origen_hash = ?"
        );
        $consulta->bind_param('iiis', $id, $idUsuario, $idCondominio, $origen);
        $consulta->execute();
        $fila = $consulta->get_result()->fetch_assoc();
        if (!$fila) {
            responderVisita(false, 'No se encontró ese vínculo en tu sesión.', 404);
        }
        responderVisita(true, 'Estado del móvil.', 200, [
            'estado' => $fila['vencido'] ? 'vencido' : $fila['estado'],
            'codigo' => $fila['codigo_confirmacion'],
            'vence_en' => $fila['vence_en']
        ]);
    }

    // TITULO 7 AUTORIZAR EL TELEFONO DESDE LA SESION QUE GENERO EL QR
    if ($accion === 'aprobar') {
        if (!$escritorio) {
            responderVisita(false, 'La aprobación debe realizarse desde el computador.', 401);
        }
        $id = enteroPositivoVisita($_POST['id_vinculo'] ?? null);
        $codigo = textoVisita('codigo');
        if (!$id || !preg_match('/^[0-9]{6}$/D', $codigo)) {
            responderVisita(false, 'Comprueba el código de vinculación.', 400);
        }
        $consulta = $bd->prepare(
            "UPDATE visita_vinculo_movil SET estado = 'aprobado', vence_en = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 MINUTE)
             WHERE id_vinculo = ? AND id_usuario_fk = ? AND id_condominio_fk = ? AND sesion_origen_hash = ?
               AND codigo_confirmacion = ? AND estado = 'solicitado' AND vence_en > UTC_TIMESTAMP()"
        );
        $consulta->bind_param('iiiss', $id, $idUsuario, $idCondominio, $origen, $codigo);
        $consulta->execute();
        if ($consulta->affected_rows !== 1) {
            responderVisita(false, 'La solicitud venció, cambió de estado o pertenece a otra sesión.', 409);
        }
        responderVisita(true, 'Móvil autorizado. El teléfono terminará la vinculación.', 200, ['estado' => 'aprobado']);
    }

    // TITULO 8 CONSULTAR LA APROBACION DESDE EL MISMO TELEFONO
    if ($accion === 'estado' && !$escritorio) {
        $fila = consultarVinculoMovilVisita($bd);
        if (!$fila) {
            unset($_SESSION['visita_movil_vinculo'], $_SESSION['visita_movil_nonce']);
            responderVisita(true, 'El vínculo venció o fue revocado. Escanea un QR nuevo.', 200, [
                'estado' => 'sin_vinculo', 'csrf' => $_SESSION['csrf_visita']
            ]);
        }
        $datos = ['estado' => $fila['estado'], 'codigo' => $fila['codigo_confirmacion'], 'csrf' => $_SESSION['csrf_visita']];
        if ($fila['estado'] === 'activo') {
            $datos['nombre_conserje'] = $fila['nombre_conserje'];
            $datos['nombre_condominio'] = $fila['nombre_condominio'];
            $datos['vence_en'] = $fila['vence_en'];
        }
        responderVisita(true, 'Estado de vinculación.', 200, $datos);
    }

    // TITULO 9 RENOVAR LA ACTIVIDAD REAL DEL TELEFONO
    if ($accion === 'mantener') {
        if (!accesoMovilVisita($bd)) responderVisita(false, 'El acceso móvil terminó. Vincula el teléfono de nuevo.', 401);
        responderVisita(true, 'Actividad del teléfono actualizada.');
    }

    // TITULO 10 ACTIVAR UNA SESION PROPIA CON PERMISOS LIMITADOS A VISITA
    if ($accion === 'activar') {
        $fila = consultarVinculoMovilVisita($bd);
        if (!$fila || $fila['estado'] !== 'aprobado') {
            responderVisita(false, 'Espera la aprobación del computador o genera un QR nuevo.', 409);
        }
        $id = (int) $fila['id_vinculo'];
        $consulta = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'activo', ultimo_uso = UTC_TIMESTAMP(), vence_en = DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR) WHERE id_vinculo = ? AND estado = 'aprobado' AND vence_en > UTC_TIMESTAMP()");
        $consulta->bind_param('i', $id);
        $consulta->execute();
        if ($consulta->affected_rows !== 1) {
            responderVisita(false, 'La solicitud ya fue utilizada o revocada.', 409);
        }
        session_regenerate_id(true);
        $_SESSION['csrf_visita'] = bin2hex(random_bytes(32));
        $vigencia = $bd->query('SELECT vence_en FROM visita_vinculo_movil WHERE id_vinculo = ' . $id)->fetch_assoc()['vence_en'];
        responderVisita(true, 'Teléfono vinculado para registrar visitas.', 200, [
            'estado' => 'activo', 'csrf' => $_SESSION['csrf_visita'],
            'nombre_conserje' => $fila['nombre_conserje'], 'nombre_condominio' => $fila['nombre_condominio'],
            'vence_en' => $vigencia
        ]);
    }

    // TITULO 11 DESVINCULAR DESDE EL COMPUTADOR O EL TELEFONO AUTORIZADO
    if ($accion === 'revocar') {
        if ($escritorio) {
            $id = enteroPositivoVisita($_POST['id_vinculo'] ?? null);
            if (!$id) {
                responderVisita(false, 'Selecciona un vínculo válido.', 400);
            }
            $consulta = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'revocado' WHERE id_vinculo = ? AND id_usuario_fk = ? AND id_condominio_fk = ? AND sesion_origen_hash = ?");
            $consulta->bind_param('iiis', $id, $idUsuario, $idCondominio, $origen);
        } else {
            $id = enteroPositivoVisita($_SESSION['visita_movil_vinculo'] ?? null);
            $nonce = $_SESSION['visita_movil_nonce'] ?? '';
            if (!$id || !is_string($nonce)) {
                responderVisita(false, 'No hay un móvil vinculado en esta sesión.', 401);
            }
            $hash = hash('sha256', $nonce);
            $consulta = $bd->prepare("UPDATE visita_vinculo_movil SET estado = 'revocado' WHERE id_vinculo = ? AND solicitud_hash = ?");
            $consulta->bind_param('is', $id, $hash);
        }
        $consulta->execute();
        if ($consulta->affected_rows < 1) {
            responderVisita(false, 'No se encontró un vínculo activo de esta sesión.', 404);
        }
        if (!$escritorio) {
            unset($_SESSION['visita_movil_vinculo'], $_SESSION['visita_movil_nonce']);
        }
        responderVisita(true, 'Móvil desvinculado.', 200, ['estado' => 'revocado']);
    }
    responderVisita(false, 'Operación no disponible.', 400);
} catch (Throwable $error) {
    error_log('Error de vinculación móvil: ' . $error->getMessage());
    responderVisita(false, 'No se pudo preparar la vinculación. Comprueba la configuración del servidor y su migración de datos.', 503);
}
