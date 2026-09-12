<?php

// reanuda la sesion actual
session_start();

// elimina todas las variables almacenadas en la sesion
$_SESSION = array();

// destruye la sesion activa en el servidor
session_destroy();

// redirige al usuario de regreso a la pagina de inicio de sesion
header("Location: /conserje_smart/php/inicio_sesion/inicio_sesion.php");
exit;
?>