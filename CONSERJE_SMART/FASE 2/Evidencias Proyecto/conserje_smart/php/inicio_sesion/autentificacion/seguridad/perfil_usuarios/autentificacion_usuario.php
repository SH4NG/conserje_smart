<?php

// inicia el manejo de sesiones en PHP para almacenar el estado del usuario
session_start();

// Configurar encabezado para responder exclusivamente en formato JSON
header('Content-Type: application/json; charset=utf-8');

/* ------------------------
   -- INICIO CONEXION BD --
   ------------------------ */

// Establece la conexión a la base de datos de conserje_smart_bd, conexion para xampp
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

// Establece la conexión a la base de datos de conserje_smart_bd, conexion para el hosting
//$mysqli = new mysqli('localhost', 'nombre_servidor_root', '123456', 'nombre_servidor_conserje_smart_bd');

// Establecer el charset a utf8mb4
$mysqli->set_charset("utf8mb4");

/* ---------------------
   -- FIN CONEXION BD --
   --------------------- */


//  TITULO 1 VALIDAR CONEXION BASE DATOS

    // verifica si ocurrio un error al intentar conectar con la base de datos
    if ($mysqli->connect_error) {
        echo json_encode([ // devuelve una respuesta en formato JSON indicando que la conexion fallo
            "exito" => false, 
            "mensaje" => "Error de conexión a la BD: " . $mysqli->connect_error
        ]);
        exit; // detiene la ejecucion del script
    }


// TITULO 2 VALIDAR CORREO Y CONTRASEÑA ENTRADA

    // obtiene el correo enviado por el formulario sin espacios vacios
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    // obtiene la contraseña enviado por el formulario sin espacios vacios
    $contraseña = isset($_POST['contraseña']) ? trim($_POST['contraseña']) : '';

    // Comprobar campos vacíos
    if (empty($correo) || empty($contraseña)) {
        echo json_encode([ // devuelve un mensaje en formato JSON indicando que faltan campos por rellenar
            "exito" => false, 
            "mensaje" => "Faltan datos por ingresar."
        ]);
        exit;
    }


// TITULO 3 CONSULTA USUARIO SQL

    // prepara la consulta SQL para buscar el usuario mediante su correo electronico
    $sql = "SELECT id_usuario,rut_usuario, nombre_usuario, apellido_usuario, telefono_usuario, correo, rol_usuario, codigo_auth_usuario, contraseña, id_condominio_fk
            FROM usuario 
            WHERE correo = ?";
    // crea la sentencia preparada para evitar inyecciones SQL
    $stmt = $mysqli->prepare($sql);
    // vincula el parámetro del correo a la consulta preparada indicando que es de tipo string ("s")
    $stmt->bind_param("s", $correo);
    // ejecuta la consulta en la base de datos
    $stmt->execute();
    // obtiene el resultado generado por la consulta
    $resultado = $stmt->get_result();


// TITULO 4 VALIDAR USUARIO  

    // comprueba si se encontro un registro que coincida con el correo ingresado
    if ($usuario = $resultado->fetch_assoc()) {
        
        // comprueba si la contraseña ingresada coincide exactamente con la registrada en la base de datos
        if ($contraseña === $usuario['contraseña']) {

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

/* -------------------------------
   -- INICIO CIERRE CONEXION BD --
   ------------------------------- */

$stmt->close();
$mysqli->close();

?>