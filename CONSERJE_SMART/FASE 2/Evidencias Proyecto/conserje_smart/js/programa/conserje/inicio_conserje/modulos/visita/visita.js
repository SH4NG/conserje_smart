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
            cargar: () => import('./escanerQR_visita/control_escanerQR_visita.js'),
            iniciar: 'inicializarEscanerVisita',
            mensaje: 'mensaje_escaner_visita'
        },
        {
            cargar: () => import('./Backend_visita/registrar_visita.js'),
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
            cargar: () => import('./escanerQR_visita/appMovil_visita/appMovil_visita.js'),
            iniciar: 'inicializarVisitaAppMovil',
            mensaje: 'mensaje_instalacion_visita'
        });
    }

    const publicos = [];
    if (document.getElementById('estado_acceso_movil_visita')) {
        publicos.push({
            cargar: () => import('./escanerQR_visita/appMovil_visita/vinculo_appMovil_visita.js'),
            iniciar: 'inicializarVinculoAppMovilVisita',
            mensaje: 'mensaje_acceso_movil_visita'
        });
        // la instalación se prepara sin consultar departamentos ni iniciar la cámara
        const instalacion = modulos.findIndex((modulo) => modulo.iniciar === 'inicializarVisitaAppMovil');
        if (instalacion >= 0) publicos.push(...modulos.splice(instalacion, 1));
        document.addEventListener('visita:movil-autorizado', () => cargarModulos(modulos), { once: true });
        await cargarModulos(publicos);
    } else {
        modulos.push({
            cargar: () => import('./escanerQR_visita/vincular_movil_visita.js'),
            iniciar: 'inicializarVincularMovilVisita',
            mensaje: 'mensaje_vincular_movil_visita'
        });
        await cargarModulos(modulos);
    }
}

// TITULO 3 CARGAR SECCIONES Y MOSTRAR ERRORES DE INICIO
async function cargarModulos(modulos) {
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
