// TITULO 1 INICIALIZAR MODULOS DE VISITA
// carga cada sección por separado para que un fallo de cámara no bloquee el formulario
async function iniciarVisita() {
    if (!document.getElementById('pagina_visita')) {
        return;
    }

    // TITULO 2 DEFINIR SECCIONES Y FUNCIONES DE INICIO
    const modulos = [
        {
            cargar: () => import('./rut_visita.js'),
            iniciar: 'inicializarRutVisita',
            mensaje: 'mensaje_formulario_visita'
        },
        {
            cargar: () => import('./escanerQR_visita.js'),
            iniciar: 'inicializarEscanerVisita',
            mensaje: 'mensaje_escaner_visita'
        },
        {
            cargar: () => import('./backend_visitas/registrar_visita.js'),
            iniciar: 'inicializarRegistroVisita',
            mensaje: 'mensaje_formulario_visita'
        },
        {
            cargar: () => import('./tablero_visita.js'),
            iniciar: 'inicializarTableroVisita',
            mensaje: 'mensaje_tablero_visita'
        }
    ];

    if (document.getElementById('contenedor_instalacion_visita')) {
        modulos.push({
            cargar: () => import('./visita_appMovil/visita_appMovil.js'),
            iniciar: 'inicializarVisitaAppMovil',
            mensaje: 'mensaje_instalacion_visita'
        });
    }

    // TITULO 3 CARGAR SECCIONES Y MOSTRAR ERRORES DE INICIO
    await Promise.all(modulos.map(async (modulo) => {
        try {
            const funciones = await modulo.cargar();
            await funciones[modulo.iniciar]();
        } catch (error) {
            console.error('No se pudo iniciar un módulo de Visita:', error);
            const mensaje = document.getElementById(modulo.mensaje);
            if (mensaje) {
                mensaje.textContent = 'No se pudo cargar esta sección. Recarga la página para reintentar.';
                mensaje.classList.add('error');
            }
            if (modulo.iniciar === 'inicializarRegistroVisita') {
                const select = document.getElementById('select_departamento_visita');
                select.replaceChildren(new Option('No disponible', ''));
                select.disabled = true;
                document.getElementById('boton_registrar_visita').disabled = true;
            }
        }
    }));
}

// TITULO 4 ESPERAR LA ESTRUCTURA DE LA PAGINA
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciarVisita, { once: true });
} else {
    iniciarVisita();
}
