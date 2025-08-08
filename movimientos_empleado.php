<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$usuario = $_SESSION['user']['username'];
$fechaInicio = $_GET['fecha_inicio'] ?? '';
$fechaFin = $_GET['fecha_fin'] ?? '';

$registros = [];
$totalCantidad = 0;

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $sql = "SELECT codigo_producto, nombre_producto, cantidad, fecha 
            FROM movimientoo 
            WHERE usuario = ? AND DATE(fecha) BETWEEN ? AND ?
            ORDER BY fecha DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("sss", $usuario, $fechaInicio, $fechaFin);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($row = $resultado->fetch_assoc()) {
        $registros[] = $row;
        $totalCantidad += intval($row['cantidad']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Movimientos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style_general.css">
   
</head>
<body>
<nav><?php include 'navbar.php'; ?></nav>
<?php if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1): ?>

<?php endif; ?>

<div class="container">
    <h3 class="mb-4">Mis Movimientos</h3>

    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-4">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Consultar</button>
        </div>
    </form>

    <?php if (!empty($registros)): ?>
    <div class="mb-3">
        <label for="filtro_codigo" class="form-label">Filtrar por Código:</label>
        <input type="text" id="filtro_codigo" class="form-control" placeholder="Ingrese el código">
    </div>
    <div class="total-box">
        Total de unidades ingresadas: <?= $totalCantidad ?>
    </div>

    <div class="table-responsive">
        <table id="tablaMovimientos" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['codigo_producto']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($row['cantidad']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                       
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?php elseif (!empty($fechaInicio) && !empty($fechaFin)): ?>
        <div class="alert alert-warning">No se encontraron resultados para el rango de fecha seleccionado.</div>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        const tabla = $('#tablaMovimientos').DataTable({
            language: {
                search: "Buscar en todas las columnas:",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron movimientos",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
            },
            columnDefs: [
                { targets: [4], orderable: false }
            ]
        });

        // Filtro solo por columna "Código"
        $('#filtro_codigo').on('keyup', function () {
            tabla.column(0).search(this.value).draw();
        });
    });

   
</script>
</body>
</html>
