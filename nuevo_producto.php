<?php 
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style_general.css">
</head>
<body>
  <nav><?php include 'navbar.php'; ?></nav>
  <?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
  <div class="alert alert-success alert-dismissible fade show mt-3 text-center mx-auto w-50" role="alert">
    <strong>¡Éxito!</strong> Producto guardado correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
  <div class="alert alert-danger alert-dismissible fade show mt-3 text-center mx-auto w-50" role="alert">
    <strong>Error:</strong> No se pudo guardar el producto.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

  

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg mt-5">
          <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="fa-solid fa-box-open me-2"></i>Agregar Producto</h2>
          </div>
          <div class="card-body">
            <form action="insertar.php" method="post">
              <div class="mb-3">
                <label for="codigo" class="form-label"><i class="fa-solid fa-barcode me-1"></i>Código</label>
                <input type="text" name="codigo" id="codigo" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="nombre" class="form-label"><i class="fa-solid fa-tag me-1"></i>Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-custom w-100">
                <i class="fa-solid fa-save me-2"></i>Guardar Producto
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.classList.remove('show');
      alert.classList.add('fade');
    }
  }, 1000); // 4 segundos
</script>

</body>
</html>
