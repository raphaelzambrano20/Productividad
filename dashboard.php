<?php
require_once 'auth.php';
require_login();
require_role(['admin']); // solo administradores

$user = current_user();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Sistema Inventario</title>

  <!-- Bootstrap / Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

  <style>
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    .content { margin-left: 80px; padding: 0; }

    /* Topbar */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 100;
      background: #1c2331;
      color: #fff;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .topbar .btn-primary {
      background: #0d6efd;
      border: none;
      font-size: 0.9rem;
      padding: 6px 14px;
    }
    .topbar .btn-primary:hover {
      background: #0b5ed7;
    }

    

    /* KPI Cards */
    .kpi-card {
      background: linear-gradient(135deg, #1c2331, #2c3e50);
      color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,.1);
      padding: 16px;
      text-align: center;
      border-top: 3px solid #0d6efd;
      transition: transform 0.2s;
    }
    .kpi-card:hover {
      transform: translateY(-4px);
    }
    .kpi-title { font-size: .9rem; color: #cbd3da; }
    .kpi-value { font-size: 2rem; font-weight: 700; color: #fff; }

    /* Chart Card */
    .chart-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,.05);
      padding: 16px;
      height: 300px;
    }
    .chart-card canvas { height: 100% !important; }

    /* Table Card */
    .table-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,.05);
      padding: 16px;
    }
    table.dataTable thead th {
      background: #0d6efd;
      color: #fff;
      border: none;
    }

    @media (max-width: 991.98px) {
      .content { margin-left: 0; }
    }

    .usuario-top-card {
      background: linear-gradient(135deg, #1f2937, #111827);
      color: #f9fafb;
      border: none;
      border-radius: 1rem;
      transition: transform 0.2s ease-in-out;
    }

    .usuario-top-card:hover {
      transform: scale(1.02);
      box-shadow: 0 0 15px rgba(100, 255, 218, 0.3);
    }

    .usuario-top-nombre {
      font-size: 1.5rem;
      font-weight: bold;
      color: #38bdf8; /* Azul claro */
    }

    .usuario-top-cantidad {
      font-weight: 600;
      color: #facc15; /* Amarillo pastel */
    }

    .bg-gradient-top {
      background: linear-gradient(to right, #3b82f6, #06b6d4);
      font-weight: 600;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
    }

  </style>
</head>
<body>
<?php include 'sinebar.php'; ?>

<div class="content">


  <!-- TOPBAR -->
  <div class="topbar">
    <form id="formFiltro" class="row gx-2 gy-1 align-items-center">
      <div class="col-auto"><input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control form-control-sm"></div>
      <div class="col-auto"><input type="date" name="fecha_fin" id="fecha_fin" class="form-control form-control-sm"></div>
      <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i>Filtrar</button></div>
    </form>
  </div>



  <!-- MAIN -->
  <div class="container-fluid py-4">
    <!-- KPIs -->
    <div class="row g-3 mb-3">
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-title">Total Unidades</div><div class="kpi-value" id="total-unidades">0</div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-title">Promedio / Día</div><div class="kpi-value" id="promedio-dia">0</div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-title">Promedio / Hora</div><div class="kpi-value" id="promedio-hora">0</div></div></div>
      <div class="col-6 col-md-3"><div class="kpi-card"><div class="kpi-title">Promedio / Minuto</div><div class="kpi-value" id="promedio-minuto">0</div></div></div>
    </div>

    <!-- Gráficas -->
    <div class="row g-3">
      <div class="col-md-6 col-lg-4"><div class="chart-card"><h6>Unidades por Día</h6><canvas id="chartDias"></canvas></div></div>
      <div class="col-md-6 col-lg-4"><div class="chart-card"><h6>Unidades por Usuario</h6><canvas id="chartUsuarios"></canvas></div></div>
      <div class="col-md-6 col-lg-4"><div class="chart-card"><h6>Promedio por Hora</h6><canvas id="chartUsuariosHora"></canvas></div></div>
      <div class="col-md-6 col-lg-4"><div class="chart-card"><h6>Promedio por Minuto</h6><canvas id="chartUsuariosMinuto"></canvas></div></div>
    </div><br>


    <div class="col-md-3">
      <div class="card usuario-top-card shadow">
        <div class="card-header text-white bg-gradient-top">Usuario Top</div>
        <div class="card-body">
          <h5 class="card-title usuario-top-nombre" id="usuarioTopNombre">---</h5>
          <p class="card-text">Total: <span class="usuario-top-cantidad" id="usuarioTopCantidad">0</span> unidades</p>
        </div>
      </div>
    </div>
      
    

    <!-- Tabla -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="table-card">
          <h6>Detalle por Usuario</h6>
          <table id="tablaUsuarios" class="table table-striped table-bordered" style="width:100%">
            <thead><tr><th>Usuario</th><th>Total</th><th>Prom. Día</th><th>Prom. Hora</th><th>Prom. Minuto</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
Chart.register(ChartDataLabels);
let chartDias, chartUsuarios, chartUsuariosHora, chartUsuariosMinuto;

function generarPaletaColores(cantidad) {
  const base = ['#9c84f2ff','#f28e2b','#e15759','#76b7b2','#59a14f','#edc948','#b07aa1','#ff9da7','#9c755f','#bab0ac'];
  return Array.from({length: cantidad}, (_,i) => base[i % base.length]);
}

function initCharts() {
  chartDias = new Chart(document.getElementById('chartDias'), {
    type: 'bar',
    data: { labels: [], datasets: [{ label: '', data: [], backgroundColor: [] }] },
    options: { responsive: true, plugins: { datalabels:{ color:'#000', anchor:'end', align:'top' } } }
  });
  chartUsuarios = new Chart(document.getElementById('chartUsuarios'), {
    type: 'pie',
    data: { labels: [], datasets: [{labal:'', data: [], backgroundColor: [] }] },
    options: { responsive: true, plugins: { datalabels:{ color:'#fff' } } }
  });
  chartUsuariosHora = new Chart(document.getElementById('chartUsuariosHora'), {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Promedio / Hora', data: [], backgroundColor: [] }] },
    options: { responsive: true, plugins: { datalabels:{ color:'#000', anchor:'end', align:'top' } } }
  });
  chartUsuariosMinuto = new Chart(document.getElementById('chartUsuariosMinuto'), {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Promedio / Minuto', data: [], backgroundColor: [] }] },
    options: { responsive: true, plugins: { datalabels:{ color:'#000', anchor:'end', align:'top' } } }
  });
}

function cargarDashboard(fecha_inicio = '', fecha_fin = '') {
  fetch(`dashboard_data.php?fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`)
    .then(r => r.json())
    .then(data => {
      document.getElementById('total-unidades').textContent  = data.total;
      document.getElementById('promedio-dia').textContent    = data.promedioDia;
      document.getElementById('promedio-hora').textContent   = data.promedioHora;
      document.getElementById('promedio-minuto').textContent = data.promedioMinuto;
      
      // Usuario Top
      document.getElementById('usuarioTopNombre').textContent = data.usuarioTop.nombre;
      document.getElementById('usuarioTopCantidad').textContent = data.usuarioTop.total;

      const c1 = generarPaletaColores(data.datosPorDia.length);
      chartDias.data.labels = data.datosPorDia.map(i => i.dia);
      chartDias.data.datasets[0].data = data.datosPorDia.map(i => i.total);
      chartDias.data.datasets[0].backgroundColor = c1;
      chartDias.update();

      const labelsU = data.datosPorUsuario.map(i => i.usuario);
      const c2 = generarPaletaColores(labelsU.length);

      chartUsuarios.data.labels = labelsU;
      chartUsuarios.data.datasets[0].data = data.datosPorUsuario.map(i => i.total);
      chartUsuarios.data.datasets[0].backgroundColor = c2;
      chartUsuarios.update();

      chartUsuariosHora.data.labels = labelsU;
      chartUsuariosHora.data.datasets[0].data = data.datosPorUsuario.map(i => i.promedioHora);
      chartUsuariosHora.data.datasets[0].backgroundColor = c2;
      chartUsuariosHora.update();

      chartUsuariosMinuto.data.labels = labelsU;
      chartUsuariosMinuto.data.datasets[0].data = data.datosPorUsuario.map(i => i.promedioMinuto);
      chartUsuariosMinuto.data.datasets[0].backgroundColor = c2;
      chartUsuariosMinuto.update();

      let tabla = $('#tablaUsuarios').DataTable();
      tabla.clear();
      data.datosPorUsuario.forEach(i => {
        tabla.row.add([i.usuario, i.total, i.promedioDia, i.promedioHora, i.promedioMinuto]);
      });
      tabla.draw();
    });
}

$(document).ready(function(){
  $('#tablaUsuarios').DataTable({ responsive:true, dom:'Bfrtip', buttons:['excel'] });
  initCharts();
  cargarDashboard();

  document.getElementById('formFiltro').addEventListener('submit', function(e){
    e.preventDefault();
    const fi = document.getElementById('fecha_inicio').value;
    const ff = document.getElementById('fecha_fin').value;
    cargarDashboard(fi, ff);
  });
});
</script>
</body>
</html>
