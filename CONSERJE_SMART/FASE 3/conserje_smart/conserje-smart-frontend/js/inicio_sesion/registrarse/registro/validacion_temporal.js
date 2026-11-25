// TITULO 1 OBTENER CONTROLES DE VALIDACION TEMPORAL
// obtiene el boton de validacion y sus campos de autorizacion
const botonValidarTemporal = document.getElementById("boton_validar_validacion_temporal");
const rutAdminTemporal = document.getElementById("rut_admin_validacion_temporal");
const codigoAdminTemporal = document.getElementById("codigo_auth_admin_validacion_temporal");

// TITULO 2 VALIDAR Y REGISTRAR USUARIO
// envia las credenciales del administrador junto con el registro para validarlas en PHP
if (botonValidarTemporal && rutAdminTemporal && codigoAdminTemporal) {
    botonValidarTemporal.addEventListener("click", async function () {
        const rutAdmin = rutAdminTemporal.value.trim();
        const codigoAdmin = codigoAdminTemporal.value.trim();

        if (rutAdmin === "" || codigoAdmin === "") {
            alert("Ingresa el RUT y el código de autorización del administrador.");
            return;
        }

        botonValidarTemporal.disabled = true;
        botonValidarTemporal.textContent = "Validando...";

        try {
            await registrarUsuario(rutAdmin, codigoAdmin);
        } finally {
            if (document.getElementById("ventana_emergente")?.open) {
                botonValidarTemporal.disabled = false;
                botonValidarTemporal.textContent = "Validar";
            }
        }
    });
} else {
    console.error("No se encontraron los controles requeridos para validar el administrador.");
}

// TITULO 3 BOTON CANCELAR VALIDACION TEMPORAL
// obtiene el boton que permite cerrar la ventana emergente
const botonCancelarValidacionTemporal = document.getElementById("boton_cancelar_validacion_temporal");

// TITULO 4 CERRAR VALIDACION TEMPORAL
// cierra el dialogo sin enviar datos al servidor
if (botonCancelarValidacionTemporal) {
    botonCancelarValidacionTemporal.addEventListener("click", function () {
        // obtiene la ventana emergente que contiene el formulario temporal
        const ventanaEmergente = document.getElementById("ventana_emergente");

        // verifica que la ventana exista y este abierta como modal
        if (ventanaEmergente && ventanaEmergente.open) {
            // cierra la ventana emergente
            ventanaEmergente.close();
        }
    });
}