<?php
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

// 1) Fechas del filtro
$fecha_inicio = isset($_GET['fecha_inicio']) && $_GET['fecha_inicio'] !== ""
    ? $_GET['fecha_inicio'] . " 00:00:00"
    : date("Y-m-01 00:00:00");

$fecha_fin = isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== ""
    ? $_GET['fecha_fin'] . " 23:59:59"
    : date("Y-m-t 23:59:59");

// 2) Total de unidades en el rango
$stmtTotal = $conn->prepare("SELECT SUM(cantidad) AS total FROM movimientoo WHERE fecha BETWEEN ? AND ?");
$stmtTotal->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmtTotal->execute();
$totalRango = (float)($stmtTotal->get_result()->fetch_assoc()['total'] ?? 0);
$stmtTotal->close();

// 3) Nº de días del rango (INCLUYENDO extremos)  ★★ Fórmula correcta ★★
$fiDate = new DateTime(substr($fecha_inicio, 0, 10));
$ffDate = new DateTime(substr($fecha_fin, 0, 10));
$diasRango = $fiDate->diff($ffDate)->days + 1;   // siempre ≥ 1

// 4) Promedios globales (según tus reglas)
$promedioDia     = $diasRango > 0 ? round($totalRango / $diasRango, 2) : 0;
$promedioHora    = round($promedioDia / 5, 2);
$promedioMinuto  = round($promedioHora / 60, 2);

// 5) Usuario Top
$stmtTop = $conn->prepare("
    SELECT usuario, SUM(cantidad) AS total
    FROM movimientoo
    WHERE fecha BETWEEN ? AND ?
    GROUP BY usuario
    ORDER BY total DESC
    LIMIT 1
");
$stmtTop->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmtTop->execute();
$topRow = $stmtTop->get_result()->fetch_assoc();
$stmtTop->close();

$usuarioTop = [
    'nombre' => $topRow['usuario'] ?? 'N/A',
    'total'  => (float)($topRow['total'] ?? 0)
];

// 6) Datos por día (para gráfica)
$stmtDias = $conn->prepare("
    SELECT DATE(fecha) AS dia, SUM(cantidad) AS total
    FROM movimientoo
    WHERE fecha BETWEEN ? AND ?
    GROUP BY dia
    ORDER BY dia
");
$stmtDias->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmtDias->execute();
$resDias = $stmtDias->get_result();
$datosPorDia = [];
while ($r = $resDias->fetch_assoc()) {
    $datosPorDia[] = [
        'dia'   => $r['dia'],
        'total' => (float)$r['total']
    ];
}
$stmtDias->close();

// 7) Datos por usuario (totales + promedios por usuario)
$stmtUsuarios = $conn->prepare("
    SELECT usuario, SUM(cantidad) AS total
    FROM movimientoo
    WHERE fecha BETWEEN ? AND ?
    GROUP BY usuario
    ORDER BY usuario
");
$stmtUsuarios->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmtUsuarios->execute();
$resUsuarios = $stmtUsuarios->get_result();
$datosPorUsuario = [];
while ($r = $resUsuarios->fetch_assoc()) {
    $totalU       = (float)$r['total'];
    $promDiaU     = $diasRango > 0 ? round($totalU / $diasRango, 2) : 0;
    $promHoraU    = round($promDiaU / 5, 2);
    $promMinU     = round($promHoraU / 60, 2);

    $datosPorUsuario[] = [
        'usuario'        => $r['usuario'],
        'total'          => $totalU,
        'promedioDia'    => $promDiaU,
        'promedioHora'   => $promHoraU,
        'promedioMinuto' => $promMinU
    ];
}
$stmtUsuarios->close();

// 8) Salida JSON
echo json_encode([
    'total'           => $totalRango,
    'diasRango'       => $diasRango,
    'promedioDia'     => $promedioDia,
    'promedioHora'    => $promedioHora,
    'promedioMinuto'  => $promedioMinuto,
    'usuarioTop'      => $usuarioTop,
    'datosPorDia'     => $datosPorDia,
    'datosPorUsuario' => $datosPorUsuario
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
