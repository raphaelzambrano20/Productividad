<?php
require_once 'auth.php';

$rol = $_SESSION['user']['role'] ?? '';
$username = $_SESSION['user']['username'] ?? 'Usuario';
?>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
  .navbar {
    background: linear-gradient(to right, #0a2342, #122b49);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  }

  .navbar-brand {
    font-size: 1.8rem;
    font-weight: 800;
    color: #ffffff !important;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
    margin-right: 2rem;
    transition: color 0.3s;
  }

  .navbar-brand:hover {
    color: #dbe6f6 !important;
  }

  .nav-link {
    font-weight: 500;
    color: #e0e0e0 !important;
    transition: background 0.3s, color 0.3s;
  }

  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
    border-radius: 5px;
    color: #ffffff !important;
  }

  .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
  }

  .dropdown-item {
    font-weight: 500;
    color: #333333;
  }

  .dropdown-item:hover {
    background-color: #eeeeee;
    color: #0d47a1;
  }

  .bi {
    margin-right: 6px;
  }

  @media (max-width: 991.98px) {
    .navbar-nav .nav-link {
      padding-left: 1rem;
    }
    .navbar-brand {
      margin-bottom: 0.5rem;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <i class="bi bi-box-fill"></i> Productividad
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 border-start ps-3">
        <?php if ($rol === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="crear_usuario.php"><i class="bi bi-person-plus-fill"></i> Crear Usuario</a></li>
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-seam"></i> Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="consultar_movimientos.php"><i class="bi bi-arrow-left-right"></i> Movimientos</a></li>
        <?php elseif ($rol === 'empleado'): ?>
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-seam"></i> Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="movimientos_empleado.php"><i class="bi bi-clock-history"></i> Movimientos</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($username); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="usuarioDropdown">
            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
