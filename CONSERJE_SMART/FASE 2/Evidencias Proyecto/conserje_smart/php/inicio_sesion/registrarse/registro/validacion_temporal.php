<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <!-- TITULO ENTRADA RUT VALIDACION -->

        <input class="entrada_rut" id="entrada_rut"  type="text">

    <!-- TITULO ENTRADA CLAVE VALIDACION -->

        <input class="entrada_clave" id="entrada_clave" type="text">

    <!-- TITULO BOTON VALIDAR VALIDACION -->

        <button class="boton_validar" id="boton_validar" >validar</button>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validación temporal</title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/validacion_temporal.css">
</head>
<body>

    <!-- TITULO 1 VALIDACION TEMPORAL -->
    <!-- contiene los datos del administrador que autorizara el registro -->
    <div class="validacion_temporal" id="validacion_temporal">

        <!-- TITULO 2 TITULO VALIDACION TEMPORAL -->
        <!-- informa al usuario el objetivo de la ventana -->
        <div class="titulo_validacion_temporal" id="titulo_validacion_temporal">
            AUTORIZACIÓN DEL ADMINISTRADOR
        </div>

        <!-- TITULO 3 EXPLICACION VALIDACION TEMPORAL -->
        <!-- indica que debe ingresar un administrador del condominio seleccionado -->
        <p class="texto_validacion_temporal" id="texto_validacion_temporal">
            Ingresa el RUT y el código de autorización de un administrador del condominio seleccionado.
        </p>

        <!-- TITULO 4 ENTRADA RUT ADMINISTRADOR -->
        <!-- recibe el RUT del administrador que autoriza el registro -->
        <label for="rut_admin_validacion_temporal">RUT del administrador</label>
        <input
            class="entrada_rut_validacion_temporal"
            id="rut_admin_validacion_temporal"
            name="rut_admin"
            type="text"
            placeholder="RUT"
            maxlength="9"
        >

        <!-- TITULO 5 ENTRADA CODIGO ADMINISTRADOR -->
        <!-- recibe el codigo de autorizacion del administrador -->
        <label for="codigo_auth_admin_validacion_temporal">Código de autorización</label>
        <input
            class="entrada_codigo_auth_validacion_temporal"
            id="codigo_auth_admin_validacion_temporal"
            name="codigo_auth_admin"
            type="password"
            placeholder="Código de autorización"
            maxlength="255"
        >

        <!-- TITULO 6 BOTONES VALIDACION TEMPORAL -->
        <!-- permite confirmar o cancelar la validacion temporal -->
        <div class="botones_validacion_temporal" id="botones_validacion_temporal">
            <button
                class="boton_validar_validacion_temporal"
                id="boton_validar_validacion_temporal"
                type="button"
            >
                Validar
            </button>

            <button
                class="boton_cancelar_validacion_temporal"
                id="boton_cancelar_validacion_temporal"
                type="button"
                formmethod="dialog"
            >
                Cancelar
            </button>
        </div>
    </div>

    <!-- carga las funciones propias de la validacion temporal -->
    <script src="/conserje_smart/js/inicio_sesion/registrarse/registro/validacion_temporal.js"></script>

</body>
</html>