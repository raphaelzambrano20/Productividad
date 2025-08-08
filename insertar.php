<?php
require_once 'auth.php';
require_login(); // Debe estar logueado
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];

    // Usar parámetros preparados para evitar SQL Injection
    $stmt = $conn->prepare("INSERT INTO productos (codigo, nombre) VALUES (?, ?)");
    $stmt->bind_param("ss", $codigo, $nombre);

    if ($stmt->execute()) {
        // Redirige con mensaje de éxito
        header("Location: nuevo_producto.php?exito=1");
        exit;
    } else {
        // Redirige con mensaje de error
        header("Location: nuevo_producto.php?error=1");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
