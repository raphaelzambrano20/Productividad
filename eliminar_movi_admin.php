<?php
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM movimientoo WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  // Redirecciona con las fechas para mantener la búsqueda
  $fecha_inicio = urlencode($_POST['fecha_inicio']);
  $fecha_fin = urlencode($_POST['fecha_fin']);
  header("Location: consultar_movimientos.php?eliminado=1&fecha_inicio=$fecha_inicio&fecha_fin=$fecha_fin");
  exit;
}
?>
