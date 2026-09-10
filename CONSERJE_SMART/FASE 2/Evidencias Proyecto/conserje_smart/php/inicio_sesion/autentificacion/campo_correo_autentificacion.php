<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/campo_correo_autentificacion.css">
</head>
<body>
    <!-- TITULO CAMPO CORREO AUTENTIFICACION -->
    <div class="campo_correo_autentificacion" id="campo_correo_autentificacion">
        <!-- TITULO ENTRADA CAMPO CORREO AUTENTIFICACION -->
        <div class="entrada_campo_correo_autentificacion" id="entrada_campo_correo_autentificacion">
            <img class= "imagen_campo_correo_autentificacion" src="/conserje_smart/imagenes/inicio_sesion/autentificacion/campo_correo_autentificacion.png" alt="">
            
            <!-- TITULO TEXTO ENTRADA DE TEXTO DEL CORREO -->
            <input class="texto_entrada_campo_correo_autentificacion" id="texto_entrada_campo_correo_autentificacion" type="email" placeholder="tu@gmail.com">

        </div>

    </div>

    <script>
        <?php include __DIR__ . '/../../../js/inicio_sesion/autentificacion/campo_correo_autentificacion.js'; ?>
    </script>
</body>
</html>