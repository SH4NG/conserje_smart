
//TITULO 1 CAMPO CONTRASEÑA AUTENTIFICACION

    //sin funcion

//TITULO 2 ENTRADA CONTRASEÑA AUTENTIFICACION

    //sin funcion

//TITULO 3 IMAGEN CONTRASEÑA AUTENTIFICACION

    //sin funcion

// TITULO 4 TEXTO ENTRADA CONTRASEÑA AUTENTIFICACION

    // obtiene la contraseña escrita en el campo
    function obtenerContraseñaAutentificacion() {

        // obtiene el campo de contraseña mediante su id
        const entradaContraseñaAutentificacion = document.getElementById("texto_entrada_contraseña_autentificacion");

        // verifica que el campo exista
        if (!entradaContraseñaAutentificacion) {

            // devuelve un texto vacio si el campo no existe
            return "";
        }

        // devuelve la contraseña escrita sin espacios al inicio ni al final
        return entradaContraseñaAutentificacion.value.trim();
    }

    // valida el campo contraseña
    function validarCampoContraseñaAutentificacion() {

        // obtiene la contraseña escrita por el usuario
        const contraseña = obtenerContraseñaAutentificacion();

        // comprueba si la contraseña esta vacia
        if (contraseña === "") {

            // muestra un mensaje indicando que falta la contraseña
            alert("Debe ingresar una contraseña.");

            // indica que la validacion no fue correcta
            return false;
        }

        // indica que la validacion fue correcta
        return true;
    }

