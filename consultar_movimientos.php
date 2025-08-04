<?php
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';

$mensaje = '';
if (isset($_GET['eliminado']) && $_GET['eliminado'] === '1') {
  $mensaje = 'Movimiento eliminado con éxito.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Consulta de Movimientos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    body {
      background-color: #f5f8fa;
    }
    .card-header {
      background: linear-gradient(90deg, #002244, #004c70);
      color: white;
      border-radius: 0.5rem 0.5rem 0 0;
      text-align: center;
      padding: 1rem;
    }
    thead {
      background-color: #004c70;
      color: white;
    }
    .dataTables_filter input {
      border-radius: 8px;
      padding: 5px;
    }
  </style>
</head>
<body>
<?php include 'sinebar.php'; ?>
      <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $mensaje; ?>
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
      <h2>Consulta de Movimientos</h2>
    </div>
    <div class="card-body">

      

      <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
          <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
          <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">
        </div>
        <div class="col-md-5">
          <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
          <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
      </form>

      <?php
      if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
          $fecha_inicio = $_GET['fecha_inicio'] . " 00:00:00";
          $fecha_fin = $_GET['fecha_fin'] . " 23:59:59";

          $stmt = $conn->prepare("SELECT * FROM movimientoo WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC");
          $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
              echo "<div class='mb-3 text-end'>
                      <a href='exportar_excel.php?fecha_inicio={$_GET['fecha_inicio']}&fecha_fin={$_GET['fecha_fin']}' class='btn btn-success'>
                        Exportar a Excel
                      </a>
                    </div>";

              echo "<div class='table-responsive'>
                      <table id='tablaMovimientos' class='table table-striped table-bordered text-center'>
                        <thead>
                          <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                          </tr>
                        </thead>
                        <tbody>";
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['codigo_producto']}</td>
                          <td>{$row['nombre_producto']}</td>
                          <td>{$row['cantidad']}</td>
                          <td>{$row['usuario']}</td>
                          <td>{$row['fecha']}</td>
                          <td>
                            <form method='POST' action='eliminar_movi_admin.php' onsubmit='return confirm(\"¿Seguro que deseas eliminar este movimiento?\")'>
                              <input type='hidden' name='id' value='{$row['id']}'>
                              <input type='hidden' name='fecha_inicio' value='{$_GET['fecha_inicio']}'>
                              <input type='hidden' name='fecha_fin' value='{$_GET['fecha_fin']}'>
                              <button type='submit' class='btn btn-sm btn-danger'>Eliminar</button>
                            </form>
                          </td>
                        </tr>";
              }
              echo "</tbody></table></div>";
          } else {
              echo "<div class='alert alert-warning'>No se encontraron movimientos en el rango de fechas.</div>";
          }
          $stmt->close();
      } else {
          echo "<div class='alert alert-info'>Por favor, selecciona un rango de fechas.</div>";
      }

      $conn->close();
      ?>
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
        search: "Buscar por código:",
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
        { targets: [1, 2, 3, 4, 5], searchable: false } // Solo permite buscar por el primer campo (código)
      ]
    });
  });
</script>
</body>
</html>
