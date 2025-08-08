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
  <link rel="stylesheet" href="style_general.css">
</head>
<body>

<nav><?php include 'navbar.php'; ?></nav>

<div class="main-content">
  <div class="consulta-card">
    <div class="card-header">
      <h2><i class="fas fa-search me-2"></i>Consultar Producto</h2>
    </div>
    <div class="card-body p-4">
      <!-- FORMULARIO DE BUSQUEDA -->
      <form method="GET" action="" id="formBusqueda">
        <div class="mb-3">
          <label for="codigo" class="form-label">Código del Producto:</label>
          <div class="input-group">
            <input type="text" class="form-control" name="codigo" id="codigo" 
                   placeholder="Ej: 1234567890123 (EAN-13) o escanea código de barras" 
                   required>
            <button type="button" class="btn btn-warning" id="btnScanner">
              <i class="fas fa-camera"></i>
            </button>
          </div>
        </div>
        
        <!-- CONTENEDOR DEL ESCÁNER -->
        <div id="scanner-container" class="hidden">
          <div id="scanner"></div>
          <div class="scanner-overlay">
            <div class="scanner-line"></div>
            <div class="scanner-corners"></div>
          </div>
          <div class="text-center mt-3">
            <small class="text-muted d-block mb-2">
              <i class="fas fa-crosshairs me-1"></i>Mantén el código de barras centrado
            </small>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCloseScanner">
              <i class="fas fa-times me-1"></i>Cerrar Escáner
            </button>
          </div>
        </div>
        
        <button type="submit" class="btn btn-success w-100 mt-2"><i class="fas fa-search me-1"></i>Buscar</button>
      </form>
      <hr>

      <!-- RESULTADOS DE BUSQUEDA -->
      <div id="resultado">
        <?php
        if (isset($_GET['codigo'])) {
          $codigo = $_GET['codigo'];
        

          $stmt = $conn->prepare("SELECT * FROM productos WHERE codigo = ?");
          $stmt->bind_param("s", $codigo);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($row = $result->fetch_assoc()) {
            echo "<div class='alert alert-info' id='infoProducto'><strong>Producto encontrado:</strong><br>";
            echo "Código: " . htmlspecialchars($row['codigo']) . "<br>";
            echo "Nombre: " . htmlspecialchars($row['nombre']) . "</div>";

            echo "<div id='seccionCantidad'>
                    <form id='formGuardar'>
                      <input type='hidden' name='codigo' value='".htmlspecialchars($row['codigo'])."'>
                      <input type='hidden' name='nombre' value='".htmlspecialchars($row['nombre'])."'>
                      <div class='mb-3'>
                        <label for='cantidad' class='form-label'>Cantidad:</label>
                        <input type='number' class='form-control' name='cantidad' id='cantidad' required>
                      </div>
                      <button type='submit' class='btn btn-primary w-100'><i class='fas fa-save me-1'></i>Guardar Movimiento</button>
                    </form>
                  </div>";
          } else {
            echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
          }

          $stmt->close();
          $conn->close();
        }
        ?>
      </div>
      
      <!-- MENSAJE DE RESPUESTA -->
      <div id='mensaje' class='mt-3'></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const formGuardar = document.getElementById('formGuardar');
    const formBusqueda = document.getElementById('formBusqueda');
    const btnScanner = document.getElementById('btnScanner');
    const btnCloseScanner = document.getElementById('btnCloseScanner');
    const scannerContainer = document.getElementById('scanner-container');
    const codigoInput = document.getElementById('codigo');
    
    let scannerActive = false;

    // Funcionalidad del escáner
    btnScanner.addEventListener('click', function() {
      if (!scannerActive) {
        startScanner();
      }
    });

    btnCloseScanner.addEventListener('click', function() {
      stopScanner();
    });

    function startScanner() {
      scannerContainer.classList.remove('hidden');
      scannerActive = true;
      btnScanner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      btnScanner.disabled = true;

      // Variables para validación
      let detectionCount = {};
      const REQUIRED_CONFIRMATIONS = 2;

      Quagga.init({
        inputStream: {
          name: "Live",
          type: "LiveStream",
          target: document.querySelector('#scanner'),
          constraints: {
            width: 640,
            height: 480,
            facingMode: "environment"
          }
        },
        decoder: {
          readers: ["ean_reader"]
        },
        locate: true,
        locator: {
          patchSize: "medium",
          halfSample: true
        },
        numOfWorkers: 2,
        frequency: 10
      }, function(err) {
        if (err) {
          console.error('Error al inicializar Quagga:', err);
          alert('Error al acceder a la cámara. Por favor verifica los permisos.');
          stopScanner();
          return;
        }
        console.log("Escáner iniciado correctamente");
        Quagga.start();
      });

      Quagga.onDetected(function(data) {
        const code = data.codeResult.code;
        const format = data.codeResult.format;
        
        console.log('Código detectado:', code, 'Formato:', format);
        
        // Validar EAN-13 o EAN-8
        if ((format === 'ean_13' && /^\d{13}$/.test(code)) || 
            (format === 'ean_8' && /^\d{8}$/.test(code))) {
          
          // Validar dígito de control
          if (!validateEANCheckDigit(code, format)) {
            console.log('Dígito de control inválido, ignorando...');
            return;
          }

          // Sistema de confirmación
          if (!detectionCount[code]) {
            detectionCount[code] = 0;
          }
          detectionCount[code]++;

          console.log(`Código: ${code}, Detecciones: ${detectionCount[code]}`);

          // Confirmar después de 2 detecciones
          if (detectionCount[code] >= REQUIRED_CONFIRMATIONS) {
            console.log('Código EAN confirmado:', code);
            
            // Llenar el input
            codigoInput.value = code;
            
            // Detener escáner
            stopScanner();
            
            // Mostrar mensaje
            const mensaje = document.getElementById('mensaje');
            mensaje.innerHTML = `<div class='alert alert-success'>
              <i class='fas fa-check-circle me-2'></i>¡Código escaneado exitosamente!<br>
              <strong>Código:</strong> ${code}
            </div>`;
            
            setTimeout(function() {
              mensaje.innerHTML = '';
            }, 3000);

            // Limpiar contador
            detectionCount = {};
          }
        } else {
          console.log('Formato no válido:', format);
        }
      });

      // Función de validación de dígito de control
      function validateEANCheckDigit(code, format) {
        const digits = code.split('').map(Number);
        let sum = 0;
        
        if (format === 'ean_13') {
          for (let i = 0; i < 12; i++) {
            sum += digits[i] * (i % 2 === 0 ? 1 : 3);
          }
          const checkDigit = (10 - (sum % 10)) % 10;
          return checkDigit === digits[12];
          
        } else if (format === 'ean_8') {
          for (let i = 0; i < 7; i++) {
            sum += digits[i] * (i % 2 === 0 ? 3 : 1);
          }
          const checkDigit = (10 - (sum % 10)) % 10;
          return checkDigit === digits[7];
        }
        
        return false;
      }

      Quagga.onProcessed(function(result) {
        const drawingCtx = Quagga.canvas.ctx.overlay;
        const drawingCanvas = Quagga.canvas.dom.overlay;

        if (result) {
          drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));

          if (result.boxes) {
            result.boxes.filter(function (box) {
              return box !== result.box;
            }).forEach(function (box) {
              Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
            });
          }

          if (result.box) {
            Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
          }

          if (result.codeResult && result.codeResult.code) {
            Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
          }
        }
      });
    }

    function stopScanner() {
      if (scannerActive) {
        Quagga.stop();
        scannerContainer.classList.add('hidden');
        scannerActive = false;
        btnScanner.innerHTML = '<i class="fas fa-camera"></i>';
        btnScanner.disabled = false;
        console.log("Escáner detenido");
      }
    }

    // Funcionalidad original de guardar
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
          
          document.getElementById('codigo').value = '';
          
          setTimeout(function() {
            const mensaje = document.getElementById('mensaje');
            const infoProducto = document.getElementById('infoProducto');
            const seccionCantidad = document.getElementById('seccionCantidad');
            
            if (mensaje) mensaje.classList.add('fade-out');
            if (infoProducto) infoProducto.classList.add('fade-out');
            if (seccionCantidad) seccionCantidad.classList.add('fade-out');
            
            setTimeout(function() {
              if (mensaje) mensaje.classList.add('hidden');
              if (infoProducto) infoProducto.classList.add('hidden');
              if (seccionCantidad) seccionCantidad.classList.add('hidden');
              mensaje.innerHTML = '';
            }, 500);
            
          }, 2000);
        })
        .catch(err => {
          document.getElementById('mensaje').innerHTML =
            "<div class='alert alert-danger'>Error al guardar.</div>";
        });
      });
    }

    // Cerrar escáner con Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && scannerActive) {
        stopScanner();
      }
    });
  });
</script>
</body>
</html>