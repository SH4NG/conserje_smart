// TITULO 1 ROL REGISTRAR
// obtiene el select donde se escoge el rol del usuario
const entradaRolRegistrar = document.getElementById("rol_registrar");

// TITULO 2 OBTENER ROL
// devuelve el valor seleccionado para que lo utilice el boton de registro
function obtenerRolRegistrar() {
    return entradaRolRegistrar ? entradaRolRegistrar.value : "";
}

// TITULO 3 VALIDAR ROL
// comprueba que el rol seleccionado pertenezca a los roles permitidos
function validarRolRegistrar() {
    const rol = obtenerRolRegistrar();

    // evita enviar el formulario con el rol vacio o no permitido
    if (!["admin", "conserje", "residente"].includes(rol)) {
        alert("Debe seleccionar un rol.");
        return false;
    }

    return true;
}