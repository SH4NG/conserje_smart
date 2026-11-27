<?php

// inicia el manejo de sesiones en PHP para almacenar el estado del usuario
session_start();

// Configurar encabezado para responder exclusivamente en formato JSON
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 4) . '/base_datos/conexion_postgresql.php';


// TITULO 2 VALIDAR CORREO Y CONTRASEÑA ENTRADA

    // obtiene el correo enviado por el formulario sin espacios vacios
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    // conserva exactamente los caracteres ingresados en la contraseña
    $contraseñaRecibida = $_POST['contraseña'] ?? '';
    $contraseña = is_string($contraseñaRecibida) ? $contraseñaRecibida : '';

    // Comprobar campos vacíos
    if ($correo === '' || $contraseña === '' || strlen($contraseña) > 72) {
        echo json_encode([ // devuelve un mensaje en formato JSON indicando que faltan campos por rellenar
            "exito" => false, 
            "mensaje" => "Correo o contraseña incorrectos."
        ]);
        exit;
    }


// TITULO 3 CONSULTA USUARIO SQL

    // prepara la consulta SQL para buscar el usuario mediante su correo electronico
    $sql = "SELECT id_usuario,rut_usuario, nombre_usuario, apellido_usuario, telefono_usuario, correo, rol_usuario, codigo_auth_usuario, contraseña, id_condominio_fk
            FROM usuario 
            WHERE correo = ?";
    // crea la sentencia preparada para evitar inyecciones SQL
    try {
    $pdo = conectarBasePostgreSQL();
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();
} catch (Throwable $error) {
    error_log('No se pudo consultar la autenticación del usuario: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo conectar con la base de datos."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


// TITULO 4 VALIDAR USUARIO  

    // comprueba si se encontro un registro que coincida con el correo ingresado
    if ($usuario) {
        
        // verifica la contraseña contra el hash almacenado
        if (is_string($usuario['contraseña']) && password_verify($contraseña, $usuario['contraseña'])) {

            if ($usuario['rol_usuario'] === 'conserje') {
                try {
                    $solicitud = $pdo->prepare(
                        'SELECT estado FROM solicitud_registro_conserje
                         WHERE id_usuario_fk = ? AND id_condominio_fk = ?
                         ORDER BY id_solicitud DESC LIMIT 1'
                    );
                    $solicitud->execute([
                        (int) $usuario['id_usuario'],
                        (int) $usuario['id_condominio_fk']
                    ]);
                    $estadoSolicitud = $solicitud->fetchColumn();
                } catch (Throwable $error) {
                    error_log('No se pudo comprobar la autorización de la cuenta de conserje: ' . $error->getMessage());
                    http_response_code(500);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'No se pudo completar el inicio de sesión.'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                if ($estadoSolicitud !== false && $estadoSolicitud !== 'aprobada') {
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => $estadoSolicitud === 'pendiente'
                            ? 'Tu solicitud de acceso aún está pendiente de aprobación.'
                            : 'Tu solicitud de acceso no fue aprobada. Contacta al administrador del condominio.'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }

            if (password_needs_rehash($usuario['contraseña'], PASSWORD_DEFAULT)) {
                $hashActualizado = password_hash($contraseña, PASSWORD_DEFAULT);
                if ($hashActualizado === false) {
                    echo json_encode([
                        "exito" => false,
                        "mensaje" => "No se pudo completar el inicio de sesión."
                    ]);
                    exit;
                }

                $idUsuario = (int) $usuario['id_usuario'];
                try {
                    $actualizarHash = $pdo->prepare(
                        'UPDATE usuario SET contraseña = ? WHERE id_usuario = ?'
                    );
                    $actualizarHash->execute([$hashActualizado, $idUsuario]);
                } catch (PDOException $error) {
                    error_log('No se pudo actualizar el hash de contraseña: ' . $error->getMessage());
                    echo json_encode([
                        "exito" => false,
                        "mensaje" => "No se pudo completar el inicio de sesión."
                    ]);
                    exit;
                }
            }

            session_regenerate_id(true);

            // guarda el estado activo de la sesion en el servidor
            $_SESSION['estado'] = 'sesion_iniciada';
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['apellido_usuario'] = $usuario['apellido_usuario'];
            $_SESSION['rol_usuario'] = $usuario['rol_usuario'];
            $_SESSION['id_condominio_fk'] = $usuario['id_condominio_fk'];

            echo json_encode([ // devuelve una respuesta exitosa con los datos principales del usuario autenticado
                "exito" => true,
                "mensaje" => "Autenticación exitosa",
                "nombre" => $usuario['nombre_usuario'],
                "apellido" => $usuario['apellido_usuario'],
                "rol" => $usuario['rol_usuario']
            ]);

        } else {

            echo json_encode([ // devuelve una respuesta de error si la contraseña no coincide
                "exito" => false, 
                "mensaje" => "Correo o contraseña incorrectos."
            ]);
        }
        
    } else {

        echo json_encode([ // devuelve una respuesta de error si el correo no existe en la base de datos
            "exito" => false, 
            "mensaje" => "Correo o contraseña incorrectos."
        ]);
    }

?>