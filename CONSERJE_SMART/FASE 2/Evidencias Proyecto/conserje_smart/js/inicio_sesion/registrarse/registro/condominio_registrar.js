// TITULO 1 CONDOMINIO REGISTRAR
// obtiene el select donde se mostraran los condominios disponibles
const entradaCondominioRegistrar = document.getElementById("condominio_registrar");

// TITULO 2 CARGAR CONDOMINIOS
// solicita a PHP los condominios registrados en la base de datos
async function cargarCondominiosRegistrar() {
    // detiene la carga si el select no existe en la pagina
    if (!entradaCondominioRegistrar) {
        return;
    }

    try {
        // consulta el endpoint que devuelve los condominios en JSON
        const respuesta = await fetch(
            "/conserje_smart/php/inicio_sesion/registrarse/seguridad/listar_condominios.php"
        );

        // informa si el servidor respondio con un estado HTTP fallido
        if (!respuesta.ok) {
            throw new Error(`Error HTTP ${respuesta.status}`);
        }

        // convierte la respuesta del servidor en un objeto JavaScript
        const resultado = await respuesta.json();

        // detiene el flujo si PHP informo un error
        if (!resultado.exito) {
            throw new Error(resultado.mensaje);
        }

        // crea una opcion por cada condominio devuelto por PHP
        resultado.condominios.forEach(function (condominio) {
            const opcion = document.createElement("option");
            opcion.value = condominio.id_condominio;
            opcion.textContent = condominio.nombre_condominio;
            entradaCondominioRegistrar.appendChild(opcion);
        });
    } catch (error) {
            // registra el error tecnico y notifica al usuario
            console.error("Error al cargar los condominios:", error);
            alert("No se pudieron cargar los condominios.");
    }
}

// TITULO 3 OBTENER CONDOMINIO
// devuelve el id del condominio seleccionado
function obtenerCondominioRegistrar() {
    return entradaCondominioRegistrar ? entradaCondominioRegistrar.value : "";
}

// TITULO 4 VALIDAR CONDOMINIO
// comprueba que se haya seleccionado un id valido
function validarCondominioRegistrar() {
    if (!/^\d+$/.test(obtenerCondominioRegistrar())) {
            alert("Debe seleccionar un condominio.");
        return false;
    }

    return true;
}

// TITULO 5 INICIALIZAR CONDOMINIOS
// inicia la carga de condominios al cargar el archivo
cargarCondominiosRegistrar();