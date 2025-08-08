<?php
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';

// Exportar a Excel sin usar composer
if (isset($_POST['exportar_excel'])) {
  header("Content-Type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename=movimientos.xls");
  echo "<table border='1'>";
  echo "<tr><th>Código</th><th>Nombre</th><th>Cantidad</th><th>Usuario</th><th>Fecha</th></tr>";

  $fecha_inicio = $_POST['fecha_inicio'] . " 00:00:00";
  $fecha_fin = $_POST['fecha_fin'] . " 23:59:59";

  $stmt = $conn->prepare("SELECT codigo_producto, nombre_producto, cantidad, usuario, fecha FROM movimientoo WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC");
  $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['codigo_producto']}</td>
            <td>{$row['nombre_producto']}</td>
            <td>{$row['cantidad']}</td>
            <td>{$row['usuario']}</td>
            <td>{$row['fecha']}</td>
          </tr>";
  }
  echo "</table>";
  exit;
}

$mensaje = '';
if (isset($_GET['eliminado']) && $_GET['eliminado'] === '1') {
  $mensaje = 'Movimiento eliminado con éxito.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Movimientos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="style_general.css">
</head>
<body>
  <?php include 'navbar.php'; ?>

  <?php if ($mensaje): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $mensaje ?>
    </div>
    <script>
      setTimeout(() => {
        document.querySelector('.alert').classList.remove('show');
        document.querySelector('.alert').classList.add('d-none');
      }, 3000);
    </script>
  <?php endif; ?>

  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-header">
        <h2 class="mb-0">Consulta de Movimientos</h2>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
          <div class="col-md-5">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required value="<?= $_GET['fecha_inicio'] ?? '' ?>">
          </div>
          <div class="col-md-5">
            <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required value="<?= $_GET['fecha_fin'] ?? '' ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
          </div>
        </form>

        <?php if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])): ?>
          <?php
          $fecha_inicio = $_GET['fecha_inicio'] . " 00:00:00";
          $fecha_fin = $_GET['fecha_fin'] . " 23:59:59";

          $stmt = $conn->prepare("SELECT * FROM movimientoo WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC");
          $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
          $stmt->execute();
          $result = $stmt->get_result();
          ?>

          <?php if ($result->num_rows > 0): ?>
            <form method="POST" class="text-end mb-3">
              <input type="hidden" name="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?>">
              <input type="hidden" name="fecha_fin" value="<?= $_GET['fecha_fin'] ?>">
              <button type="submit" name="exportar_excel" class="btn btn-success">Exportar a Excel</button>
            </form>

            <div class="table-responsive">
              <table id="tablaMovimientos" class="table table-striped table-bordered text-center">
                <thead>
                  <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $row['codigo_producto'] ?></td>
                      <td><?= $row['nombre_producto'] ?></td>
                      <td><?= $row['cantidad'] ?></td>
                      <td><?= $row['usuario'] ?></td>
                      <td><?= $row['fecha'] ?></td>
                      <td>
                        <form method="POST" action="eliminar_movi_admin.php" onsubmit="return confirm('¿Seguro que deseas eliminar este movimiento?')">
                          <input type="hidden" name="id" value="<?= $row['id'] ?>">
                          <input type="hidden" name="fecha_inicio" value="<?= $_GET['fecha_inicio'] ?>">
                          <input type="hidden" name="fecha_fin" value="<?= $_GET['fecha_fin'] ?>">
                          <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">No se encontraron movimientos en el rango de fechas.</div>
          <?php endif; ?>
          <?php $stmt->close(); ?>
        <?php else: ?>
          <div class="alert alert-info">Por favor, selecciona un rango de fechas.</div>
        <?php endif; ?>
        <?php $conn->close(); ?>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#tablaMovimientos').DataTable({
        language: {
          search: "Buscar por usuario:",
          zeroRecords: "No se encontraron resultados",
          info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
          infoEmpty: "No hay registros disponibles",
          lengthMenu: "Mostrar _MENU_ registros",
          paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
          }
        },
        pageLength: 10,
        lengthChange: false,
        columnDefs: [
          { targets: [0, 1, 2, 4, 5], searchable: false } // solo busca por código
        ]
      });
    });
  </script>
</body>
</html>
