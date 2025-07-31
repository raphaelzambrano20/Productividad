<?php
require_once 'auth.php';
require_login(); // Validar sesión
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Consulta de Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: #e9ecef;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .main-content {
      padding: 50px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .consulta-card {
      background-color: #ffffff;
      max-width: 420px;
      width: 100%;
      border-radius: 14px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
      overflow: hidden;
    }

    .consulta-card .card-header {
      background: linear-gradient(90deg, #003366, #005580);
      color: white;
      text-align: center;
      padding: 20px;
    }

    .consulta-card .card-header h2 {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 600;
    }

    .user-tag {
      background-color: #f1f3f5;
      color: #333;
      padding: 10px 15px;
      border-bottom: 1px solid #dee2e6;
      font-size: 0.95rem;
      font-weight: 500;
    }

    .btn-success {
      background-color: #003a5e;
      border: none;
    }

    .btn-success:hover {
      background-color: #09245eff;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .alert-info, .alert-danger {
      font-size: 0.95rem;
    }

    @media (max-width: 576px) {
      .consulta-card {
        margin: 0 10px;
      }
    }
  </style>
</head>
<body>

<?php include 'sinebar.php'; ?>

<div class="main-content">
  <div class="consulta-card">
    <div class="card-header">
      <h2><i class="fas fa-search me-2"></i>Consultar Producto</h2>
    </div>
    <div class="card-body p-4">
      <!-- FORMULARIO DE BUSQUEDA -->
      <form method="GET" action="">
        <div class="mb-3">
          <label for="codigo" class="form-label">Código del Producto:</label>
          <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Ej: PRD001" required>
        </div>
        <button type="submit" class="btn btn-success w-100"><i class="fas fa-search me-1"></i>Buscar</button>
      </form>
      <hr>

      <!-- RESULTADOS DE BUSQUEDA -->
      <div id="resultado">
        <?php
        if (isset($_GET['codigo'])) {
          $codigo = $_GET['codigo'];
          $conn = new mysqli("localhost", "root", "", "sistema_inventario");

          $stmt = $conn->prepare("SELECT * FROM productos WHERE codigo = ?");
          $stmt->bind_param("s", $codigo);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($row = $result->fetch_assoc()) {
            echo "<div class='alert alert-info'><strong>Producto encontrado:</strong><br>";
            echo "Código: " . htmlspecialchars($row['codigo']) . "<br>";
            echo "Nombre: " . htmlspecialchars($row['nombre']) . "</div>";

            echo "<form id='formGuardar'>
                    <input type='hidden' name='codigo' value='".htmlspecialchars($row['codigo'])."'>
                    <input type='hidden' name='nombre' value='".htmlspecialchars($row['nombre'])."'>
                    <div class='mb-3'>
                      <label for='cantidad' class='form-label'>Cantidad:</label>
                      <input type='number' class='form-control' name='cantidad' id='cantidad' required>
                    </div>
                    <button type='submit' class='btn btn-primary w-100'><i class='fas fa-save me-1'></i>Guardar Movimiento</button>
                  </form>
                  <div id='mensaje' class='mt-3'></div>";
          } else {
            echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
          }

          $stmt->close();
          $conn->close();
        }
        ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const formGuardar = document.getElementById('formGuardar');
    if (formGuardar) {
      formGuardar.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(formGuardar);

        fetch('guardar.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(data => {
          document.getElementById('mensaje').innerHTML =
            "<div class='alert alert-success'>Movimiento guardado exitosamente.</div>";
          formGuardar.reset();
        })
        .catch(err => {
          document.getElementById('mensaje').innerHTML =
            "<div class='alert alert-danger'>Error al guardar.</div>";
        });
      });
    }
  });
</script>
</body>
</html>
