<!-- TITULO CONTENEDOR VINCULAR MOVIL VISITA -->
<section class="contenedor_vincular_movil_visita" id="contenedor_vincular_movil_visita" aria-labelledby="titulo_vincular_movil_visita">
    <!-- TITULO ENCABEZADO VINCULAR MOVIL VISITA -->
    <div class="encabezado_vincular_movil_visita">
        <!-- TITULO TITULOS VINCULAR MOVIL VISITA -->
        <div class="titulos_vincular_movil_visita">
            <span class="etiqueta_seccion">Registro desde el teléfono</span>
            <h2 id="titulo_vincular_movil_visita">Vincular móvil</h2>
        </div>
        <button type="button" class="boton_principal_visita" id="generar_qr_movil_visita" disabled>Mostrar QR para mi teléfono</button>
    </div>
    <p>Escanea el QR con la cámara de tu teléfono. Comprueba que el código sea igual en ambas pantallas y autoriza la conexión desde este computador.</p>

    <!-- TITULO QR VINCULAR MOVIL VISITA -->
    <div class="qr_vincular_movil_visita" id="qr_vincular_movil_visita" hidden></div>

    <!-- TITULO CONFIRMACION VINCULAR MOVIL VISITA -->
    <div class="confirmacion_vincular_movil_visita" id="confirmacion_vincular_movil_visita" hidden>
        <span>Confirma que este código aparece en tu teléfono:</span>
        <strong class="codigo_vincular_movil_visita" id="codigo_vincular_movil_visita"></strong>
        <button type="button" class="boton_principal_visita" id="aprobar_movil_visita">Es mi teléfono: autorizar</button>
    </div>
    <button type="button" class="boton_secundario_visita" id="revocar_movil_visita" hidden>Cancelar vinculación</button>
    <p class="mensaje_formulario_visita" id="mensaje_vincular_movil_visita" role="status" aria-live="polite">Comprobando disponibilidad del acceso móvil...</p>
    <p class="ayuda_vincular_movil_visita">El QR vence en 3 minutos y se usa una sola vez. El acceso dura hasta 8 horas, vence tras 30 minutos sin actividad y se cierra al desvincular o cerrar tu sesión en el computador.</p>
</section>
