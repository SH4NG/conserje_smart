

//TITULO 1 CAMPO CORREO AUTENTIFICACION

    //sin funcion

//TITULO 2 ENTRADA CORREO AUTENTIFICACION

    //sin funcion

//TITULO 3 IMAGEN CORREO AUTENTIFICACION

    //sin funcion

// TITULO 4 TEXTO ENTRADA COREO INICIO SESION

    
    // obtiene el correo escrito en el campo
    function obtenerCorreoAutentificacion() {

        // obtiene el campo de correo mediante su id
        const entradaCorreoAutentificacion = document.getElementById("texto_entrada_correo_autentificacion");

        // verifica que el campo exista
        if (!entradaCorreoAutentificacion) {

            // devuelve un texto vacio si el campo no existe
            return "";
        }

        // devuelve el correo escrito sin espacios al inicio ni al final
        return entradaCorreoAutentificacion.value.trim();
    }

    // valida el formato y el dominio permitido del correo
    function validarCorreoAutentificacion(correo) {

        // crea la expresion que permite solamente los dominios autorizados
        const expresionCorreo = /^[^\s@]+@(conserje|residente|admin)\.cl$/;

        // devuelve verdadero si el correo tiene un dominio autorizado
        return expresionCorreo.test(correo);
    }

    // valida el campo correo
    function validarCampoCorreoAutentificacion() {

        // obtiene el correo escrito por el usuario
        const correo = obtenerCorreoAutentificacion();

        // comprueba si el correo esta vacio
        if (correo === "") {

            // muestra un mensaje indicando que falta el correo
            alert("Debe ingresar un correo electrónico.");

            // indica que la validacion no fue correcta
            return false;
        }

        // comprueba si el correo tiene un dominio autorizado
        if (!validarCorreoAutentificacion(correo)) {

            // muestra un mensaje indicando los dominios permitidos
            alert("Ingrese un correo electrónico válido. Solo se permiten correos @conserje.cl, @residente.cl o @admin.cl.");

            // indica que la validacion no fue correcta
            return false;
        }

        // indica que la validacion fue correcta
        return true;
    }


