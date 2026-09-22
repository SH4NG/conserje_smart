<?php
$nombreUsuario = trim(
    (string) ($_SESSION['nombre_usuario'] ?? '') . ' ' .
    (string) ($_SESSION['apellido_usuario'] ?? '')
);

if ($nombreUsuario === '') {
    $nombreUsuario = 'Usuario';
}
?>

<header class="cabecera" id="cabecera">
    <div class="contenedor_logo_cabecera">
        <img
            class="logo_cabecera"
            src="/conserje_smart/imagenes/programa/cabecera/logo_cabecera.png"
            alt="Conserje Smart"
        >
    </div>

    <div class="cabecera_programa">
        <span>Hola, <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?></span>
        <a class="cabecera_cerrar_sesion" href="/conserje_smart/php/inicio_sesion/autentificacion/seguridad/perfil_usuarios/cerrar_sesion.php">
            Cerrar sesión
        </a>
    </div>
</header>