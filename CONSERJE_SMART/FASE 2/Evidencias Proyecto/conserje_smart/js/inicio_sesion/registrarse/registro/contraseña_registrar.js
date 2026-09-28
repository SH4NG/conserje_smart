// TITULO 1 CONTRASEÑA REGISTRAR
// obtiene el campo donde se ingresa la contraseña
const entradaContraseñaRegistrar = document.getElementById("contraseña_registrar");

// TITULO 2 OBTENER CONTRASEÑA
// devuelve la contraseña sin alterar los caracteres introducidos
function obtenerContraseñaRegistrar() {
    return entradaContraseñaRegistrar ? entradaContraseñaRegistrar.value : "";
}

// TITULO 3 VALIDAR CONTRASEÑA
// valida el largo y la complejidad minima de la contraseña
function validarContraseñaRegistrar() {
    const contraseña = obtenerContraseñaRegistrar();

    // verifica el largo permitido por el formulario y la base de datos
    if (contraseña.length < 8 || contraseña.length > 20) {
        alert("La contraseña debe tener entre 8 y 255 caracteres.");
        return false;
    }

    // exige una mayuscula, una minuscula y un numero
    if (!/[A-Z]/.test(contraseña) || !/[a-z]/.test(contraseña) || !/\d/.test(contraseña)) {
        alert("La contraseña debe incluir una mayúscula, una minúscula y un número.");
        return false;
    }

    return true;
}