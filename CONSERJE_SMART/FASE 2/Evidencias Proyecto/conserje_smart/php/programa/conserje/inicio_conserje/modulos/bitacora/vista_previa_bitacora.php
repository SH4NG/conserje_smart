<?php
$nombreConserje = trim(
    (string) ($_SESSION['nombre_usuario'] ?? '') . ' ' .
    (string) ($_SESSION['apellido_usuario'] ?? '')
);

if ($nombreConserje === '') {
    $nombreConserje = 'Conserje';
}
?>
<link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/bitacora/vista_previa_bitacora.css">

<!-- TITULO 1 CONTENEDOR VISTA PREVIA BITACORA -->
    <div class="contenedor_vista_previa_bitacora" id="contenedor_vista_previa_bitacora"
        data-time-zone="America/Santiago"
        data-nombre-conserje="<?= htmlspecialchars($nombreConserje, ENT_QUOTES, 'UTF-8'); ?>">

        <!-- TITULO 2 FECHA VISTA PREVIA BITACORA -->
        <div class="fecha_vista_previa_bitacora">
            <img
                class="icono_fecha_vista_previa_bitacora"
                src="/conserje_smart/imagenes/programa/conserje/inicio_conserje/modulos/bitacora/Icono_Fecha_vista_previa_bitacora.png"
                alt=""
                aria-hidden="true">
            <time id="fecha_vista_previa_bitacora" aria-live="polite"></time>
        </div>

        <!-- TITULO 3 TURNO VISTA PREVIA BITACORA -->
        <div class="definicion_turno_vista_previa_bitacora" id="definicion_turno_vista_previa_bitacora"
            aria-live="polite">
            <span class="nombre_turno_vista_previa_bitacora" id="nombre_turno_vista_previa_bitacora"></span>
            <span class="horario_turno_vista_previa_bitacora" id="horario_turno_vista_previa_bitacora"></span>
        </div>

        <!-- TITULO 4 CONTENIDO RESULTADO BITACORA -->
        <div class="contenido_vista_previa" id="contenido_vista_previa"
            aria-label="Resultado del análisis de la novedad"></div>
    </div>

<script src="/conserje_smart/js/programa/conserje/inicio_conserje/modulos/bitacora/vista_previa_bitacora.js?v=20261015-2"></script>