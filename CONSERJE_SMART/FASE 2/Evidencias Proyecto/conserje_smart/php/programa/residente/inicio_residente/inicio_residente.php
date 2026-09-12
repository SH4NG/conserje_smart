<?php
// inicia o reanuda la sesion existente
session_start();

// comprueba si la variable de estado no existe o no esta iniciada
if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'sesion_iniciada') {
    
    // destruye cualquier resto de sesion residual
    session_destroy();
    
    // redirige inmediatamente al formulario de login si intentan entrar a la fuerza
    header("Location: /conserje_smart/php/inicio_sesion/inicio_sesion.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    HOLA RESIDENTE
</body>
</html>