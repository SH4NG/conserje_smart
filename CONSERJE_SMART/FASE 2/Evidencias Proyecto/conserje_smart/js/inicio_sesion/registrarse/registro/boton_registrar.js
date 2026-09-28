// TITULO 1 BOTON REGISTRAR
// obtiene el boton que inicia las validaciones y el envio del registro
const botonRegistrar = document.getElementById("boton_registrar");

// TITULO 2 OBTENER DIALOGO REGISTRO
// obtiene la ventana que solicitara la autorizacion temporal
const ventanaEmergente = document.getElementById("ventana_emergente");

// TITULO 3 EVENTO BOTON REGISTRAR
// escucha el clic del boton y evita el envio tradicional de la pagina
if (botonRegistrar) {
    botonRegistrar.addEventListener("click", function (evento) {
        // evita la recarga de la pagina
        evento.preventDefault();
        // inicia las validaciones de los datos principales
        abrirValidacionTemporal();
    });
}

// TITULO 4 ABRIR VALIDACION TEMPORAL
// valida los datos principales y abre el dialogo de autorización
function abrirValidacionTemporal() {
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

    // verifica que el elemento dialogo exista antes de abrirlo
    if (!ventanaEmergente) {
        console.error("No se encontró el elemento con ID 'ventana_emergente'.");
        return;
    }

    // abre el dialogo como una ventana modal
    ventanaEmergente.showModal();
}

// TITULO 5 CREAR CORREO REGISTRAR
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

// TITULO 6 REGISTRAR USUARIO
// valida todos los campos, arma el POST y procesa la respuesta de PHP
async function registrarUsuario() {
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

    try {
        // define el endpoint responsable de registrar el usuario
        const respuesta = await fetch(
            "/conserje_smart/php/inicio_sesion/registrarse/seguridad/registrar_usuario.php",
            {
                method: "POST",
                body: datos
            }
        );

        // informa si el servidor respondio con un estado HTTP fallido
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status}`);
        }

        // convierte la respuesta JSON de PHP en un objeto JavaScript
        const resultado = await respuesta.json();

        // muestra el error devuelto por PHP y detiene el flujo
        if (!resultado.exito) {
            alert(resultado.mensaje);
            return;
        }

        // informa el exito y vuelve a la pantalla de inicio de sesion
        alert("Usuario registrado correctamente.");
        window.location.href = "/conserje_smart/php/inicio_sesion/inicio_sesion.php";
    } catch (error) {
        // registra el error tecnico y notifica el problema de comunicacion
        console.error("Error al registrar el usuario:", error);
        alert("Ocurrió un error al registrar el usuario.");
    }
}