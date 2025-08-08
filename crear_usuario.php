<?php 
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';
// crear_usuario.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password_plain = $_POST['password'];
    $role = $_POST['role'];

    $hash = password_hash($password_plain, PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "sistema_inventario");
    $stmt = $conn->prepare("INSERT INTO usuario (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hash, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario creado correctamente');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style_general.css">
  
 
</head>
<body>
  <nav> <?php include 'navbar.php'; ?></nav>


  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
          <div class="card-header">
            <h2><i class="fa-solid fa-user-plus me-2"></i>Crear Usuario</h2>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-user me-1"></i>Usuario</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-lock me-1"></i>Clave</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-user-gear me-1"></i>Rol</label>
                <select name="role" class="form-control" required>
                  <option value="admin">Administrador</option>
                  <option value="empleado">Empleado</option>
                </select>
              </div>
              <button type="submit" class="btn btn-custom w-100">
                <i class="fa-solid fa-plus me-2"></i>Crear Usuario
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
