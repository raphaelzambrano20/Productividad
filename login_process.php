<?php
session_start();
require 'db.php';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$stmt = $conn->prepare("SELECT id, username, password, role FROM usuario WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header("Location: index.php");
        exit;
    }
}
header("Location: login.php?error=Credenciales inválidas");
exit;
?>