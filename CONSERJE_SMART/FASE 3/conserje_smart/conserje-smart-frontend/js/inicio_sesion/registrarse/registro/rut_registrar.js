// TITULO 1 RUT REGISTRAR
// obtiene el campo donde se ingresa el RUT del usuario
const entradaRutRegistrar = document.getElementById("rut_registrar");

// TITULO 2 CONFIGURAR ENTRADA RUT
// limita la entrada a nueve digitos y convierte la k en cero
if (entradaRutRegistrar) {
    // establece el largo maximo solicitado para el RUT
    entradaRutRegistrar.maxLength = 9;

    // limpia el valor cada vez que el usuario escribe
    entradaRutRegistrar.addEventListener("input", function () {
        // conserva solamente digitos y reemplaza la k por cero
        this.value = this.value
            .toLowerCase()
            .replace(/k/g, "0")
            .replace(/\D/g, "")
            .slice(0, 9);
    });
}

// TITULO 3 OBTENER RUT
// devuelve el RUT con puntos y guion para enviarlo al PHP
function obtenerRutRegistrar() {
    // obtiene el valor sin formato para separar cuerpo y digito verificador
    const rutSinFormato = entradaRutRegistrar
        ? entradaRutRegistrar.value.toLowerCase().replace(/k/g, "0").replace(/\D/g, "").slice(0, 9)
        : "";

    // evita formatear valores incompletos
    if (rutSinFormato.length < 2) {
        return "";
    }

    // separa el cuerpo y el digito verificador del RUT
    const cuerpo = rutSinFormato.slice(0, -1);
    const digitoVerificador = rutSinFormato.slice(-1);
    const cuerpoFormateado = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    return `${cuerpoFormateado}-${digitoVerificador}`;
}

// TITULO 4 VALIDAR RUT
// valida el largo y calcula el digito verificador del RUT
function validarRutRegistrar() {
    // obtiene el RUT sin puntos, guion ni caracteres no numericos
    const rutSinFormato = entradaRutRegistrar
        ? entradaRutRegistrar.value.toLowerCase().replace(/k/g, "0").replace(/\D/g, "").slice(0, 9)
        : "";

    // exige exactamente ocho digitos de cuerpo y uno verificador
    if (!/^\d{9}$/.test(rutSinFormato)) {
        alert("El RUT debe contener exactamente 9 dígitos.");
        return false;
    }

    const cuerpo = rutSinFormato.slice(0, -1);
    const digitoVerificador = Number(rutSinFormato.slice(-1));
    let multiplicador = 2;
    let suma = 0;

    // calcula la suma ponderada del cuerpo del RUT
    for (let indice = cuerpo.length - 1; indice >= 0; indice -= 1) {
        suma += Number(cuerpo[indice]) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }

    // obtiene el digito verificador esperado
    const resto = 11 - (suma % 11);
    const digitoCalculado = resto === 11 ? 0 : resto === 10 ? 0 : resto;

    // compara el digito calculado con el ingresado
    if (digitoCalculado !== digitoVerificador) {
        alert("El RUT ingresado no es válido.");
        return false;
    }

    return true;
}