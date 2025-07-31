<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$codigo = $_POST['codigo'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$usuario = $_SESSION['user']['username'];

if ($codigo && $fecha) {
    $stmt = $conn->prepare("DELETE FROM movimientoo WHERE codigo_producto = ? AND fecha = ? AND usuario = ?");
    $stmt->bind_param("sss", $codigo, $fecha, $usuario);
    $stmt->execute();
}

header("Location: movimientos_empleado.php?eliminado=1");
exit;

