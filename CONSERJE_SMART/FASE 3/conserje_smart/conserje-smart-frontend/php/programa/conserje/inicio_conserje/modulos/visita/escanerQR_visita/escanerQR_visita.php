<!-- TITULO CONTENEDOR ESCANER VISITA -->
<section class="contenedor_escaner_visita" id="contenedor_escaner_visita" aria-labelledby="titulo_escaner_visita">
    <!-- TITULO ENCABEZADO ESCANER VISITA -->
    <div class="encabezado_escaner_visita">
        <!-- TITULO TITULOS ESCANER VISITA -->
        <div class="titulos_escaner_visita">
            <span class="etiqueta_seccion">Lectura móvil</span>
            <h2 id="titulo_escaner_visita">Escanear QR</h2>
        </div>
        <!-- TITULO ACCIONES ESCANER VISITA -->
        <div class="acciones_escaner_visita">
            <button type="button" class="boton_principal_visita" id="iniciar_escaner_visita">Abrir cámara</button>
            <button type="button" class="boton_secundario_visita" id="detener_escaner_visita" disabled>Detener cámara</button>
        </div>
    </div>
    <!-- TITULO INSTRUCCIONES ESCANER VISITA -->
    <p class="instrucciones_escaner_visita">Acerca el QR del carnet, evita reflejos y mantén el teléfono firme. El ingreso se confirma en el computador.</p>
    <!-- TITULO VISOR ESCANER VISITA -->
    <div class="visor_escaner_visita">
        <!-- TITULO VIDEO ESCANER VISITA -->
        <video id="video_escaner_visita" class="video_escaner_visita" playsinline muted hidden aria-label="Vista de la cámara para leer QR"></video>
    </div>

    <!-- TITULO CONTROLES CAMARA VISITA -->
    <div class="controles_camara_visita" id="controles_camara_visita" hidden>
        <!-- TITULO CONTROL ZOOM VISITA -->
        <label class="control_zoom_visita" id="control_zoom_visita" hidden>
            <span>Acercamiento</span>
            <input id="zoom_camara_visita" type="range" min="1" max="4" step="0.1" value="1">
            <output id="valor_zoom_visita" for="zoom_camara_visita">1.0×</output>
        </label>
        <button id="linterna_camara_visita" type="button" class="boton_secundario_visita" aria-pressed="false" hidden>Encender luz</button>
    </div>

    <!-- TITULO MENSAJE ESCANER VISITA -->
    <p id="mensaje_escaner_visita" class="mensaje_formulario_visita" role="status" aria-live="polite"></p>
</section>

