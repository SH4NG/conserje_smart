<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/campo_contraseña_autentificacion.css">
</head>
<body>
    <!-- TITULO CAMPO CONTRASEÑA AUTENTIFICACION -->
    <div class="campo_contraseña_autentificacion" id="campo_contraseña_autentificacion">
        <!-- TITULO ENTRADA CAMPO CONTRASEÑA AUTENTIFICACION -->
        <div class="entrada_campo_contraseña_autentificacion" id="entrada_campo_contraseña_autentificacion">
            <img class="imagen_campo_contraseña_autentificacion" src="/conserje_smart/imagenes/inicio_sesion/autentificacion/campo_contrasena_autentificacion.png" alt="">
            
            <!-- TITULO ENTRADA DE TEXTO DE LA CONTRASEÑA -->
            <input class="texto_entrada_contraseña_autentificacion" id="texto_entrada_contraseña_autentificacion" type="password" placeholder="Contraseña">

        </div>

    </div>
    
    <script>
        <?php include __DIR__ . '/../../../js/inicio_sesion/autentificacion/campo_contraseña_autentificacion.js'; ?>
    </script>
</body>
</html>