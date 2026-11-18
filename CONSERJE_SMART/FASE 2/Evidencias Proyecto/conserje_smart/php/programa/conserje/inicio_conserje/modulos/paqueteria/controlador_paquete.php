<?php
session_start();
if (!isset($pdo)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/conserje_smart/php/conexion.php';
}

$id_usuario = $_SESSION['id_usuario'] ?? 2; 
$id_condominio = $_SESSION['id_condominio_fk'] ?? 1;
$url_retorno = "/conserje_smart/php/programa/conserje/inicio_conserje/modulos/paqueteria/paqueteria.php";

// --- LIMPIEZA AUTOMÁTICA (Paquetes con más de 30 días retirados) ---
try {
    $sql_limpieza = "UPDATE paquete 
                     SET fecha_eliminacion = NOW() 
                     WHERE estado_paquete = 'Retirado' 
                     AND fecha_retiro <= (NOW() - INTERVAL 30 DAY) 
                     AND fecha_eliminacion IS NULL";
    $pdo->query($sql_limpieza);
} catch (PDOException $e) {
    // Si falla, solo ignoramos para no interrumpir al usuario
}

// --- REGISTRAR PAQUETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'registrar') {
    $id_departamento = $_POST['id_departamento'];
    $empresa = $_POST['empresa_envio'];
    $codigo = $_POST['codigo_seguimiento'] ?? null;
    $descripcion = $_POST['descripcion_paquete'] ?? null;

    try {
        $sql = "INSERT INTO paquete (id_condominio_fk, id_departamento_fk, id_usuario_fk, empresa_envio, codigo_seguimiento, descripcion_paquete) 
                VALUES (:cond, :depto, :user, :empresa, :codigo, :desc)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cond' => $id_condominio, ':depto' => $id_departamento, ':user' => $id_usuario,
            ':empresa' => $empresa, ':codigo' => $codigo, ':desc' => $descripcion
        ]);
        header("Location: " . $url_retorno . "?status=success");
        exit;
    } catch (PDOException $e) {
        header("Location: " . $url_retorno . "?status=error");
        exit;
    }
}

// --- MARCAR COMO RETIRADO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'retirar') {
    $id_paquete = $_POST['id_paquete'];
    try {
        $sql = "UPDATE paquete SET estado_paquete = 'Retirado', fecha_retiro = NOW() WHERE id_paquete = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_paquete]);
        
        header("Location: " . $url_retorno . "?status=retirado&tab=retirados");
        exit;
    } catch (PDOException $e) {
        header("Location: " . $url_retorno . "?status=error");
        exit;
    }
}
?>