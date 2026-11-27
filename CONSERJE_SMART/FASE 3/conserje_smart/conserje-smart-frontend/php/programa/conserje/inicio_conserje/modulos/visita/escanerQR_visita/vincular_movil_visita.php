<!-- TITULO CONTENEDOR VINCULAR MOVIL VISITA -->
<section class="contenedor_vincular_movil_visita" id="contenedor_vincular_movil_visita" aria-labelledby="titulo_vincular_movil_visita" data-etapa="vincular">
    <!-- TITULO ENCABEZADO VINCULAR MOVIL VISITA -->
    <div class="encabezado_vincular_movil_visita">
        <!-- TITULO TITULOS VINCULAR MOVIL VISITA -->
        <div class="titulos_vincular_movil_visita">
            <span class="etiqueta_seccion">Del móvil al computador</span>
            <h2 id="titulo_vincular_movil_visita">Conecta tu teléfono</h2>
        </div>
        <button type="button" class="boton_principal_visita" id="generar_qr_movil_visita" disabled>Mostrar QR para mi teléfono</button>
    </div>
    <!-- TITULO ESTADO VINCULO VISUAL VISITA -->
    <p class="estado_vinculo_visual_visita" id="estado_vinculo_visual_visita" role="status">Comprobando conexión</p>
    <!-- TITULO PASOS REGISTRO MOVIL VISITA -->
    <ol class="pasos_registro_movil_visita" aria-label="Pasos para registrar con el móvil">
        <li><span aria-hidden="true">1</span>Vincula</li>
        <li><span aria-hidden="true">2</span>Escanea</li>
        <li><span aria-hidden="true">3</span>Confirma</li>
    </ol>
    <!-- TITULO INSTRUCCIONES VINCULAR MOVIL VISITA -->
    <p class="instrucciones_vincular_movil_visita">Pulsa «Mostrar QR para mi teléfono» y escanéalo con la cámara del móvil. Comprueba que el código sea igual en ambas pantallas y autoriza la conexión desde este computador.</p>


    <!-- TITULO LECTURA RECIBIDA MOVIL VISITA -->
    <section class="lectura_recibida_movil_visita" id="lectura_recibida_movil_visita" hidden>
        <span class="etiqueta_seccion">Lectura del teléfono</span>
        <h3>Carnet recibido</h3>
        <!-- TITULO IDENTIDAD RECIBIDA VISITA -->
        <dl class="identidad_recibida_visita">
            <div class="dato_identidad_visita"><dt>RUT</dt><dd id="rut_recibido_movil_visita"></dd></div>
            <div class="dato_identidad_visita"><dt>Nombre completo</dt><dd id="nombre_recibido_movil_visita"></dd></div>
        </dl>
        <p id="estado_lectura_recibida_visita" role="status" aria-live="polite"></p>
        <!-- TITULO ACCIONES LECTURA MOVIL VISITA -->
        <div class="acciones_lectura_movil_visita">
            <button type="button" class="boton_principal_visita" id="usar_lectura_movil_visita">Usar en el formulario</button>
            <button type="button" class="boton_secundario_visita" id="descartar_lectura_movil_visita">Descartar lectura</button>
        </div>
    </section>
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
    <p class="ayuda_vincular_movil_visita">El QR vence en 3 minutos y se usa una sola vez. El acceso dura hasta 8 horas, vence tras 30 minutos sin actividad. Puedes retirar el acceso con «Desvincular teléfono».</p>
</section>

