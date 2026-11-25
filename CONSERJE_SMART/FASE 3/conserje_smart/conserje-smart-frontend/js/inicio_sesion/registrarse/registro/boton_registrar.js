// TITULO 1 BOTON REGISTRAR
// obtiene el boton que inicia las validaciones y el envio del registro
const botonRegistrar = document.getElementById("boton_registrar");

// TITULO 2 EVENTO BOTON REGISTRAR
// escucha el clic del boton y evita el envio tradicional de la pagina
if (botonRegistrar) {
    botonRegistrar.addEventListener("click", function (evento) {
        // evita la recarga de la pagina
        evento.preventDefault();
        // inicia las validaciones de los datos principales
        abrirValidacionTemporal();
    });
}

// TITULO 3 ABRIR VALIDACION TEMPORAL
// valida los datos principales y abre el dialogo de autorización
function abrirValidacionTemporal() {
    // busca el dialogo al abrirlo, porque este script se carga antes de su include
    const ventanaEmergente = document.getElementById("ventana_emergente");

    if (!ventanaEmergente) {
        console.error("No se encontró el elemento con ID 'ventana_emergente'.");
        return;
    }

    // detiene el flujo si alguna validacion del formulario principal falla
    if (
        !validarNombreRegistrar() ||
        !validarApellidoRegistrar() ||
        !validarRutRegistrar() ||
        !validarTelefonoRegistrar() ||
        !validarRolRegistrar() ||
        !validarCondominioRegistrar() ||
        !validarContraseñaRegistrar()
    ) {
        return;
    }

    // abre el dialogo como una ventana modal
    ventanaEmergente.showModal();
}

// TITULO 4 CREAR CORREO REGISTRAR
// construye el correo segun el nombre, apellido y rol seleccionados
function crearCorreoRegistrar(nombre, apellido, rol) {
    // elimina tildes y espacios para formar un correo limpio
    const limpiarTextoCorreo = function (texto) {
        return texto
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/\s+/g, "");
    };

    // prepara las partes del nombre y apellido que formaran el correo
    const nombreCorreo = limpiarTextoCorreo(nombre);
    const apellidoLimpio = limpiarTextoCorreo(apellido);
    const cantidadLetrasApellido = rol === "residente" ? 1 : 2;
    const inicialesApellido = apellidoLimpio.slice(0, cantidadLetrasApellido);

    // define el dominio correspondiente a cada rol
    const dominios = {
        conserje: "@conserje.smart.cl",
        admin: "@admin.conserje.smart.cl",
        residente: "@residente.conserje.smart.cl"
    };

    return `${nombreCorreo}${inicialesApellido}${dominios[rol]}`;
}

// TITULO 5 REGISTRAR USUARIO
// valida todos los campos, arma el POST y procesa la respuesta de PHP
async function registrarUsuario(rutAdmin, codigoAdmin) {
    // detiene el proceso cuando alguna validacion no es correcta
    if (
        !validarNombreRegistrar() ||
        !validarApellidoRegistrar() ||
        !validarRutRegistrar() ||
        !validarTelefonoRegistrar() ||
        !validarRolRegistrar() ||
        !validarCondominioRegistrar() ||
        !validarContraseñaRegistrar()
    ) {
        return;
    }

    // obtiene los valores ya limpios desde los archivos de cada campo
    const nombre = obtenerNombreRegistrar();
    const apellido = obtenerApellidoRegistrar();
    const rol = obtenerRolRegistrar();
    const correo = crearCorreoRegistrar(nombre, apellido, rol);
    const datos = new FormData();

    // agrega cada dato al formulario que recibira PHP
    datos.append("rut", obtenerRutRegistrar());
    datos.append("nombre", nombre);
    datos.append("apellido", apellido);
    datos.append("telefono", obtenerTelefonoRegistrar());
    datos.append("correo", correo);
    datos.append("rol", rol);
    datos.append("contraseña", obtenerContraseñaRegistrar());
    datos.append("condominio", obtenerCondominioRegistrar());
    datos.append("rut_admin", rutAdmin);
    datos.append("codigo_auth_admin", codigoAdmin);

    try {
        // define el endpoint responsable de registrar el usuario
        const respuesta = await fetch(
            "/conserje_smart/conserje-smart-backend/php/inicio_sesion/registrarse/seguridad/registrar_usuario.php",
            {
                method: "POST",
                body: datos
            }
        );

        // lee primero el JSON para conservar los mensajes de error del servidor
        const resultado = await respuesta.json();

        if (!respuesta.ok || resultado.exito !== true) {
            throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
        }

        // informa el exito y vuelve a la pantalla de inicio de sesion
        alert("Usuario registrado correctamente.");
        window.location.href = "/conserje_smart/conserje-smart-frontend/php/inicio_sesion/inicio_sesion.php";
    } catch (error) {
        // informa el rechazo concreto del servidor o el error de comunicacion
        console.error("Error al registrar el usuario:", error);
        alert(error.message || "Ocurrió un error al registrar el usuario.");
    }
}