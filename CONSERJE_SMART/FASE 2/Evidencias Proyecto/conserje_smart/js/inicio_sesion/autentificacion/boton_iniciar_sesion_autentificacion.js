// espera a que el contenido de la pagina termine de cargar
document.addEventListener("DOMContentLoaded", function () {

    // obtiene el boton de iniciar sesion mediante su id
    const botonIniciarSesionAutentificacion = document.getElementById("boton_iniciar_sesion_autentificacion");

    // comprueba si el boton de iniciar sesion existe
    if (botonIniciarSesionAutentificacion) {

        // detecta cuando se hace click en el boton de iniciar sesion
        botonIniciarSesionAutentificacion.addEventListener("click", function (evento) {

            // previene que la pagina se recargue si el boton esta dentro de un formulario
            evento.preventDefault();

            // ejecuta la validacion y autenticacion del usuario
            validarAutentificacion();
        });

    } else {

        // muestra un error en la consola si no encuentra el boton
        console.error("No se encontró el elemento con ID 'boton_iniciar_sesion_autentificacion'");
    }
});

// valida los datos y realiza la consulta a la BD a traves de PHP
async function validarAutentificacion() {

    // valida el correo
    if (!validarCampoCorreoAutentificacion()) {

        // detiene la ejecucion si el correo no es valido
        return;
    }

    // valida la contraseña
    if (!validarCampoContraseñaAutentificacion()) {

        // detiene la ejecucion si la contraseña no es valida
        return;
    }

    // obtiene el correo escrito por el usuario
    const correo = obtenerCorreoAutentificacion();

    // obtiene la contraseña escrita por el usuario
    const contraseña = obtenerContraseñaAutentificacion();

    // prepara los datos que seran enviados al PHP
    const datos = new FormData();

    // agrega el correo a los datos
    datos.append("correo", correo);

    // agrega la contraseña a los datos
    datos.append("contraseña", contraseña);

    try {

        // establece la ruta del archivo PHP que realiza la autenticacion
        const urlBackend = "/conserje_smart/php/inicio_sesion/autentificacion/seguridad/perfil_usuarios/autentificacion_usuario.php";

        // realiza la peticion al PHP mediante POST
        const respuesta = await fetch(urlBackend, {
            method: "POST",
            body: datos
        });

        // comprueba si la respuesta del servidor tuvo un estado HTTP fallido
        if (!respuesta.ok) {

            // genera un error indicando el estado HTTP recibido
            throw new Error(`Error en el servidor: HTTP ${respuesta.status}`);
        }

        // convierte la respuesta recibida del servidor a formato JSON
        const resultado = await respuesta.json();

        // comprueba si el servidor respondio afirmativamente a la autenticacion
        if (resultado.exito) {

            // comprueba si el usuario tiene rol de administracion
            if (resultado.rol === "admin") {

                // redirige al inicio de administracion
                window.location.href = "/conserje_smart/php/programa/admin/inicio_admin/inicio_admin.php";

                // detiene la ejecucion
                return;
            }

            // comprueba si el usuario tiene rol de conserje
            if (resultado.rol === "conserje") {

                // redirige al inicio de conserje
                window.location.href = "/conserje_smart/php/programa/conserje/inicio_conserje/inicio_conserje.php";

                // detiene la ejecucion
                return;
            }

            // comprueba si el usuario tiene rol de residente
            if (resultado.rol === "residente") {

                // redirige al inicio de residente
                window.location.href = "/conserje_smart/php/programa/residente/inicio_residente/inicio_residente.php";

                // detiene la ejecucion
                return;
            }

            // muestra un mensaje si el rol recibido no esta configurado
            alert("El usuario fue autenticado, pero su rol no está configurado.");

        } else {

            // muestra el mensaje enviado por el PHP cuando la autenticacion falla
            alert(resultado.mensaje);
        }

    } catch (error) {

        // registra el error en la consola
        console.error("Error en el inicio de sesión:", error);

        // muestra un mensaje indicando que ocurrio un problema de conexion
        alert("Ocurrió un error en la conexión. Abre la Consola del navegador (F12) para ver más detalles.");
    }
}