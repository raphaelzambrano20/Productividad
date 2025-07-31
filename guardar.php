<?php
require_once 'auth.php';
require_login(); // Debe estar logueado
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $codigo = $_POST['codigo'];
  $nombre = $_POST['nombre'];
  $cantidad = intval($_POST['cantidad']);
  $usuario = $_SESSION['user']['username']; // Guardamos el usuario logueado

  $stmt = $conn->prepare("INSERT INTO movimientoo (codigo_producto, nombre_producto, cantidad, usuario) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssis", $codigo, $nombre, $cantidad, $usuario);

  if ($stmt->execute()) {
    echo "OK"; // Respuesta simple para Ajax
  } else {
    echo "ERROR";
  }

  $stmt->close();
  $conn->close();
}
?>
