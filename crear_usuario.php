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
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f2f5;
    }

    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
    }

    .card-header {
      background: linear-gradient(90deg, #003366, #005580);
      color: white;
      text-align: center;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
    }

    .card-header h2 {
      margin: 0;
      padding: 20px 0;
      font-size: 1.5rem;
    }

    .form-label {
      font-weight: 600;
      color: #333;
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25);
    }

    .btn-custom {
      background-color: #0056b3;
      color: #fff;
      font-weight: 500;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #003366;
    }

    .container {
      padding-top: 60px;
    }
  </style>
</head>
<body>

  <?php include 'sinebar.php'; ?>

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
