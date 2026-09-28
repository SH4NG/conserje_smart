// TITULO 1 NOMBRE REGISTRAR
// obtiene el campo donde se ingresa el nombre del usuario
const entradaNombreRegistrar = document.getElementById("nombre_registrar");

// TITULO 2 CONFIGURAR ENTRADA NOMBRE
// configura el limite y limpia el valor mientras el usuario escribe
if (entradaNombreRegistrar) {
    // establece el maximo permitido para el nombre
    entradaNombreRegistrar.maxLength = 30;

    // escucha cada cambio realizado en el campo
    entradaNombreRegistrar.addEventListener("input", function () {
        // elimina caracteres que no correspondan a texto o espacios
        this.value = this.value
            .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, "")
            .toLowerCase()
            .slice(0, 30);
    });
}

// TITULO 3 OBTENER NOMBRE
// devuelve el nombre limpio para que lo utilice el boton de registro
function obtenerNombreRegistrar() {
    return entradaNombreRegistrar ? entradaNombreRegistrar.value.trim().toLowerCase() : "";
}

// TITULO 4 VALIDAR NOMBRE
// comprueba que el nombre exista, tenga texto valido y no supere el limite
function validarNombreRegistrar() {
    const nombre = obtenerNombreRegistrar();

    // verifica que el campo no este vacio
    if (nombre === "") {
        alert("Debe ingresar el nombre.");
        return false;
    }

    // verifica que solo existan letras y espacios
    if (nombre.length > 30 || !/^[a-záéíóúñü]+(?:\s+[a-záéíóúñü]+)*$/i.test(nombre)) {
        alert("El nombre solo puede contener texto y tener un máximo de 30 caracteres.");
        return false;
    }

    return true;
}