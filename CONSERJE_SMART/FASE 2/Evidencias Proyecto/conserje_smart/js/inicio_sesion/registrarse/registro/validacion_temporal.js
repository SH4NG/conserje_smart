// TITULO 1 BOTON CANCELAR VALIDACION TEMPORAL
// obtiene el boton que permite cerrar la ventana emergente
const botonCancelarValidacionTemporal = document.getElementById("boton_cancelar_validacion_temporal");

// TITULO 2 CERRAR VALIDACION TEMPORAL
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