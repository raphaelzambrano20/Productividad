<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistema Inventario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .login-container {
      max-width: 900px;
      margin: auto;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .login-form {
      padding: 40px 30px;
      background-color: white;
    }
    .form-control {
      border-radius: 20px;
    }
    .btn-login {
      border-radius: 20px;
    }
    .img-side {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    @media (max-width: 768px) {
      .login-form {
        padding: 20px;
      }
      .login-container {
        box-shadow: none;
      }
    }
  </style>
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">

  <div class="container">
    <div class="row login-container">
      <div class="col-md-6 login-form">
        <div class="text-center mb-4">
          <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="User Icon" width="80">
          <h4 class="mt-3">AGENT LOGIN</h4>
        </div>

        <?php if(isset($_GET['error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form method="POST" action="login_process.php">
          <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
          <button type="submit" class="btn btn-primary btn-login w-100">LOGIN</button>
        </form>
      </div>

      <div class="col-md-6 d-none d-md-block p-0">
        <img src="https://ii.ct-stc.com/1/logos/empresas/2017/03/23/supermercados-mas-x-menos-sa-002BCBAB6B36ED3A134752thumbnail.jpeg" alt="Imagen Login" class="img-side">
      </div>
    </div>
  </div>

</body>
</html>
