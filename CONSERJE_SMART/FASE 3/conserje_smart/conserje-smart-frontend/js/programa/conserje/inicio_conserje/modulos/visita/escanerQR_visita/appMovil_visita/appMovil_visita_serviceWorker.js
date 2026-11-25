// TITULO 1 DEFINIR LOS RECURSOS PUBLICOS PARA MOSTRAR SIN CONEXION
const nombreCacheVisita = 'conserje-smart-visita-appMovil-v5';
const rutaVistaVisita = '/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/visita/';
const paginaSinConexionVisita = rutaVistaVisita + 'escanerQR_visita/appMovil_visita/appMovil_visita_sinConexion.php';
const recursosPublicosVisita = [
    paginaSinConexionVisita,
    '/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/appMovil_visita_sinConexion.css',
    '/conserje_smart/conserje-smart-frontend/imagenes/programa/conserje/inicio_conserje/modulos/visita/background_visita.png',
    '/conserje_smart/conserje-smart-frontend/imagenes/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/icono_visita_192.png',
    '/conserje_smart/conserje-smart-frontend/imagenes/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/icono_visita_512.png'
];

// TITULO 2 PREPARAR LA PANTALLA SIN CONEXION AL INSTALAR
self.addEventListener('install', (evento) => {
    evento.waitUntil((async () => {
        const cache = await caches.open(nombreCacheVisita);
        await cache.addAll(recursosPublicosVisita);
        await self.skipWaiting();
    })());
});

// TITULO 3 RETIRAR VERSIONES ANTERIORES DE LA CACHE DE VISITA
self.addEventListener('activate', (evento) => {
    evento.waitUntil((async () => {
        const nombres = await caches.keys();
        await Promise.all(nombres.filter((nombre) => (
            (nombre.startsWith('conserje-smart-visita-appMovil-') || nombre.startsWith('conserje-smart-visita-pwa-'))
            && nombre !== nombreCacheVisita
        )).map((nombre) => caches.delete(nombre)));
        await self.clients.claim();
    })());
});

// TITULO 4 ATENDER SOLO LA NAVEGACION Y LOS RECURSOS PUBLICOS
// las visitas, las sesiones y las peticiones al backend nunca se guardan en la cache
self.addEventListener('fetch', (evento) => {
    const peticion = evento.request;
    const url = new URL(peticion.url);
    if (peticion.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (recursosPublicosVisita.includes(url.pathname)) {
        evento.respondWith((async () => {
            const cache = await caches.open(nombreCacheVisita);
            return (await cache.match(url.pathname)) || fetch(peticion);
        })());
        return;
    }

    // TITULO 5 MOSTRAR EL AVISO CUANDO NO SE PUEDA ABRIR LA APP
    if (peticion.mode === 'navigate' && url.pathname.startsWith(rutaVistaVisita)
        && !url.pathname.startsWith(rutaVistaVisita + 'backend_visita/')) {
        evento.respondWith((async () => {
            try {
                return await fetch(peticion);
            } catch {
                const cache = await caches.open(nombreCacheVisita);
                return (await cache.match(paginaSinConexionVisita)) || Response.error();
            }
        })());
    }
});
