<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- TITULO CABECERA -->
    <div class="cabecera" id="cabecera">
        <span class="titulo_cabecera">ConserjeSmart</span>
        <!-- TITULO CABECERA -->
        <div class="cabecera_programa" id="cabecera_programa">
            <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?></span>
            <a href="/conserje_smart/php/programa/cerrar_sesion.php" class="cabecera_cerrar_sesion">Cerrar sesión</a>
        </div>
    </div>

</body>
</html>