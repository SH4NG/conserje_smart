<link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/bitacora/menu_bitacora.css">

<!-- TITULO 1 CONTENEDOR MENU BITACORA -->
<div class="contenido_menu" id="contenido_menu">

    <!-- TITULO 2 ACCESO A OTRAS BITACORAS -->
    <a class="boton_bitacora" id="boton_bitacora"
        href="/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/novedades.php">
        Ver bitácora
    </a>

    <!-- TITULO 3 ETIQUETA DE INGRESO -->
    <div class="texto_contenido_menu" id="texto_contenido_menu">
        Ingrese su novedad:
    </div>

    <!-- TITULO 4 CAMPO DE NOVEDAD -->
    <div class="campo_novedad" id="campo_novedad">
        <textarea 
            class="input_novedad" 
            id="input_novedad" 
            placeholder="Escriba aquí su novedad..."
            rows="5"
        ></textarea>
    </div>

    <!-- TITULO 5 ACCION DE ESTRUCTURAR CON IA -->
    <div class="boton_estructurar_ia" id="boton_estructurar_ia" role="button" tabindex="0" aria-controls="contenido_vista_previa">
        Estructurar con IA
    </div>

    <!-- TITULO 6 ESTADO DEL ANALISIS -->
    <p id="estado_analisis_novedad" role="status" aria-live="polite"></p>
</div>


<script src="/conserje_smart/js/programa/conserje/inicio_conserje/modulos/bitacora/menu_bitacora.js"></script>