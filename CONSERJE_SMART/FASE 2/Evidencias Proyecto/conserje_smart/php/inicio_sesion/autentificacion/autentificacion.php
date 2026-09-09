<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="/conserje_smart/css/autentificacion/autentificacion.css">
</head>

    <!-- TITULO AUTENTIFICACION -->
    <div class="autentificacion" id="autentificacion">

        <!-- CONTENEDOR LOGO AUTENTIFICACION -->
        <div class="contenedor_logo_autentificacion" id="contenedor_logo_autentificacion">

            <!-- llamamos a traves de UN include al logo_autentificacion.php -->
            <?php include __DIR__ . '/logo_autentificacion.php'; ?>

        </div>
        
        <!-- CONTENEDOR TITULO AUTENTIFICACION -->
        <div class="contenedor_titulo_autentificacion" id="contenedo_titulor_autentificacion">

            <!-- llamamos a traves de UN include al titulo_autentificacion.php -->
            <?php include __DIR__ . '/titulo_autentificacion.php'; ?>
        </div>

        <!-- CONTENEDOR CAMPO CORREO AUTENTIFICACION -->
        <div class="contenedor_campo_correo_autentificacion" id="contenedor_campo_correo_autentificacion">

            <!-- llamamos a traves de UN include al campo_correo_autentificacion.php -->
            <?php include __DIR__ . '/campo_correo_autentificacion.php'; ?>

        </div>

        <!-- CONTENEDOR CAMPO CONTRASEÑA AUTENTIFICACION -->
        <div class="contenedor_campo_contraseña_autentificacion" id="contenedor_campo_contraseña_autentificacion">

            <!-- llamamos a traves de un include al campo_contraseña_autentificacion.php -->
            <?php include __DIR__ . '/campo_contraseña_autentificacion.php'; ?>

        </div>

        <!-- CONTENEDOR BOTON RECORDAR Y RECUPERAR SESION AUTENTIFICACION -->
        <div class="contenedor_recordar_recuperar_autentificacion" id="contenedor_recordar_recuperar_sesion_autentificacion">

            <div class="contenedor_recordar_autentificacion" id="contenedor_recordar_autentificacion">
            <!-- llamamos a traves de un include al recordar_autentificacion.php -->
            <?php include __DIR__ . '/recordar_autentificacion.php'; ?>
            </div>

            <div class="contenedor_recuperar_autentificacion" id="contenedor_recuperar_autentificacion">
            <!-- llamamos a traves de un include al recuperar_autentificacion.php -->
            <?php include __DIR__ . '/recuperar_autentificacion.php'; ?>
            </div>

        </div>

        <!-- CONTENEDOR BOTON INICIAR SESION AUTENTIFICACION -->
        <div class="contenedor_boton_iniciar_sesion_autentificacion" id="contenedor_boton_iniciar_sesion_autentificacion">
            
            <!-- llamamos a traves de un include al boton_iniciar_sesion_autentificacion.php -->
            <?php include __DIR__ . '/boton_iniciar_sesion_autentificacion.php'; ?>
        </div>

        <!-- CONTENEDOR REGISTRAR AUTENTIFICACION -->
        <div class="contenedor_registrar_autentificacion" id="contenedor_registrar_autentificacion">
            
            <!-- llamamos a traves de un include al registrar_autentificacion.php -->
            <?php include __DIR__ . '/registrar_autentificacion.php'; ?>
        </div>  

    </div>


    <script>
        <?php include __DIR__ . '/../../../js/inicio_sesion/autentificacion/autentificacion.js'; ?>
    </script>
<body>
    
</body>
</html>