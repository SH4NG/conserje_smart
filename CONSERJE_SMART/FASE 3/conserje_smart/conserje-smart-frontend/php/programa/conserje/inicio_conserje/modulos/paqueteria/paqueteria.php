<?php
session_start();
define('VISTA_PAQUETERIA_ACTIVA', true);
$raizProyecto = dirname(__DIR__, 7);
$rutaBackendPaqueteria = $raizProyecto . '/conserje-smart-backend/php/programa/conserje/inicio_conserje/modulos/paqueteria/';
require_once $rutaBackendPaqueteria . 'conexion.php';
require_once $rutaBackendPaqueteria . 'consultas_paquete.php';
$nombre_conserje = $_SESSION['nombre_usuario'] ?? 'María';
$id_condominio = (int) ($_SESSION['id_condominio_fk'] ?? 1);
$filtro_mes = isset($_GET['mes']) && is_scalar($_GET['mes']) ? (string) $_GET['mes'] : '';
$filtro_anio = isset($_GET['anio']) && is_scalar($_GET['anio']) ? (string) $_GET['anio'] : '';
$datosPaqueteria = obtenerDatosPaqueteria($pdo, $id_condominio, $filtro_mes, $filtro_anio);
$paquetes_pendientes = $datosPaqueteria['paquetes_pendientes'];
$paquetes_retirados = $datosPaqueteria['paquetes_retirados'];
$paquetes_historial = $datosPaqueteria['paquetes_historial'];
$departamentos = $datosPaqueteria['departamentos'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetería - Conserje Smart</title>
    
    <!-- CSS de paquetería -->
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/paqueteria/paqueteria.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/barra_lateral/barra_lateral.css">
</head>
<body>

    <!-- Barra Lateral -->
    <?php include __DIR__ . '/../../barra_lateral/barra_lateral.php'; ?>

    <!-- Contenido Principal -->
    <div class="contenido-principal">
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border-left: 5px solid #28a745; font-weight: 600;">
                ✅ Paquete registrado correctamente.
            </div>
        <?php endif; ?>

        <div class="cabecera-modulo">
            <h2>Gestión de Paquetería</h2>
            <button class="btn-amarillo" id="btnAbrirModal">
                + Registrar Paquete
            </button>
        </div>

        <!-- Incluir Tabla -->
        <?php include __DIR__ . '/componentes/tabla_paquetes.php'; ?>

    </div>

    <!-- Incluir Modal -->
    <?php include __DIR__ . '/componentes/modal_registro.php'; ?>

    <!-- JS de paquetería -->
    <script src="/conserje_smart/conserje-smart-frontend/js/programa/conserje/inicio_conserje/modulos/paqueteria/paqueteria.js"></script>
</body>
</html>