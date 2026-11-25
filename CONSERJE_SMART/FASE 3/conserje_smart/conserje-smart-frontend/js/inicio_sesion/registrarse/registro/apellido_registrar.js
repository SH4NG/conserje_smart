// TITULO 1 APELLIDO REGISTRAR
// obtiene el campo donde se ingresa el apellido del usuario
const entradaApellidoRegistrar = document.getElementById("apellido_registrar");

// TITULO 2 CONFIGURAR ENTRADA APELLIDO
// configura el limite y limpia el valor mientras el usuario escribe
if (entradaApellidoRegistrar) {
    // establece el maximo permitido para el apellido
    entradaApellidoRegistrar.maxLength = 30;

    // escucha cada cambio realizado en el campo
    entradaApellidoRegistrar.addEventListener("input", function () {
        // elimina caracteres que no correspondan a texto o espacios
        this.value = this.value
            .replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, "")
            .toLowerCase()
            .slice(0, 30);
    });
}

// TITULO 3 OBTENER APELLIDO
// devuelve el apellido limpio para que lo utilice el boton de registro
function obtenerApellidoRegistrar() {
    return entradaApellidoRegistrar ? entradaApellidoRegistrar.value.trim().toLowerCase() : "";
}

// TITULO 4 VALIDAR APELLIDO
// comprueba que el apellido exista, tenga texto valido y no supere el limite
function validarApellidoRegistrar() {
    const apellido = obtenerApellidoRegistrar();

    // verifica que el campo no este vacio
    if (apellido === "") {
        alert("Debe ingresar el apellido.");
        return false;
    }

    // verifica que solo existan letras y espacios
    if (apellido.length > 30 || !/^[a-záéíóúñü]+(?:\s+[a-záéíóúñü]+)*$/i.test(apellido)) {
        alert("El apellido solo puede contener texto y tener un máximo de 30 caracteres.");
        return false;
    }

    return true;
}