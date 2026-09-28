// TITULO 1 TELEFONO REGISTRAR
// obtiene el campo donde se ingresa el telefono del usuario
const entradaTelefonoRegistrar = document.getElementById("telefono_registrar");

// TITULO 2 CONFIGURAR ENTRADA TELEFONO
// limita la entrada a ocho numeros sin incluir el prefijo internacional
if (entradaTelefonoRegistrar) {
    // establece el largo maximo del numero local
    entradaTelefonoRegistrar.maxLength = 8;

    // limpia el valor cada vez que el usuario escribe
    entradaTelefonoRegistrar.addEventListener("input", function () {
        // conserva solamente los digitos del telefono
        this.value = this.value.replace(/\D/g, "").slice(0, 8);
    });
}

// TITULO 3 OBTENER TELEFONO
// agrega el prefijo chileno al numero local antes de enviarlo
function obtenerTelefonoRegistrar() {
    // obtiene solamente los ocho digitos ingresados
    const telefono = entradaTelefonoRegistrar
        ? entradaTelefonoRegistrar.value.replace(/\D/g, "").slice(0, 8)
        : "";

    return telefono === "" ? "" : `+569${telefono}`;
}

// TITULO 4 VALIDAR TELEFONO
// exige que el telefono tenga exactamente ocho digitos locales
function validarTelefonoRegistrar() {
    // obtiene el valor limpio para validarlo
    const telefono = entradaTelefonoRegistrar
        ? entradaTelefonoRegistrar.value.replace(/\D/g, "")
        : "";

    if (!/^\d{8}$/.test(telefono)) {
        alert("El teléfono debe contener exactamente 8 dígitos.");
        return false;
    }

    return true;
}