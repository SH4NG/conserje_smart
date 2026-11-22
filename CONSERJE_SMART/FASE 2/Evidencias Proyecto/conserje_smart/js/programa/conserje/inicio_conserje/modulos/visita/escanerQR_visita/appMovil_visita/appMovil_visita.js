// TITULO 1 INICIALIZAR LOS ELEMENTOS DE INSTALACION
export async function inicializarVisitaAppMovil() {
    const contenedor = document.getElementById('contenedor_instalacion_visita');
    if (!contenedor) {
        return;
    }

    const boton = document.getElementById('boton_instalar_visita');
    const mensaje = document.getElementById('mensaje_instalacion_visita');
    const indicador = document.getElementById('estado_conexion_visita');
    let solicitudInstalacion = null;

    // TITULO 2 MOSTRAR EL ESTADO DE CONEXION
    function actualizarConexion() {
        indicador.textContent = navigator.onLine
            ? 'Dispositivo conectado a una red. El registro requiere acceso al servidor.'
            : 'Sin conexión. Conéctate antes de registrar una visita.';
        indicador.classList.toggle('error', !navigator.onLine);
    }

    window.addEventListener('online', actualizarConexion);
    window.addEventListener('offline', actualizarConexion);
    actualizarConexion();

    // TITULO 3 RECONOCER LA APLICACION YA INSTALADA
    function estaInstalada() {
        return window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    }

    function mostrarInstalada() {
        solicitudInstalacion = null;
        boton.hidden = true;
        mensaje.textContent = 'La app de visitas está instalada en este dispositivo.';
    }

    if (estaInstalada()) {
        mostrarInstalada();
    }

    // TITULO 4 PREPARAR LA OPCION DE INSTALACION DEL NAVEGADOR
    window.addEventListener('beforeinstallprompt', (evento) => {
        if (estaInstalada()) {
            return;
        }
        evento.preventDefault();
        solicitudInstalacion = evento;
        boton.hidden = false;
        mensaje.textContent = 'Puedes instalar esta app para abrirla desde la pantalla de inicio.';
    });

    // TITULO 5 SOLICITAR LA INSTALACION DESDE EL BOTON
    boton.addEventListener('click', async () => {
        if (!solicitudInstalacion) {
            return;
        }
        const solicitud = solicitudInstalacion;
        solicitudInstalacion = null;
        boton.hidden = true;
        try {
            await solicitud.prompt();
            const eleccion = await solicitud.userChoice;
            mensaje.textContent = eleccion.outcome === 'accepted'
                ? 'Instalación solicitada. Abre Visitas desde la pantalla de inicio.'
                : 'Puedes instalarla más adelante desde el menú del navegador.';
        } catch (error) {
            mensaje.textContent = 'Usa la opción Instalar app o Añadir a pantalla de inicio del menú del navegador.';
            console.error('No se pudo solicitar la instalación de Visita:', error);
        }
    });
    window.addEventListener('appinstalled', mostrarInstalada);

    // TITULO 6 COMPROBAR LOS REQUISITOS DE LA APLICACION MOVIL
    if (!window.isSecureContext) {
        mensaje.textContent = 'Abre el sistema mediante HTTPS para instalar la app y usar la cámara del móvil.';
        return;
    }
    if (!('serviceWorker' in navigator)) {
        mensaje.textContent = 'Puedes usar el formulario en este navegador. La instalación requiere un navegador compatible.';
        return;
    }

    // TITULO 7 REGISTRAR EL CONTROL DE CONEXION DE LA PWA
    const rutaVista = '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/visita/';
    try {
        await navigator.serviceWorker.register(rutaVista + 'Backend_visita/appMovil_visita_serviceWorker.php', {
            scope: rutaVista,
            updateViaCache: 'none'
        });
    } catch (error) {
        mensaje.textContent = 'No se pudo preparar la instalación. Puedes registrar visitas con conexión y volver a intentarlo al recargar.';
        console.error('No se pudo preparar la PWA de Visita:', error);
    }
}
