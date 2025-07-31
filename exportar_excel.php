<?php
require_once 'auth.php';
require_role(['admin']);
require_once 'db.php';

// Librería PhpSpreadsheet
require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Validación de parámetros
if (empty($_GET['fecha_inicio']) || empty($_GET['fecha_fin'])) {
    http_response_code(400);
    echo "Debe enviar fecha_inicio y fecha_fin";
    exit;
}

$fecha_inicio = $_GET['fecha_inicio'] . " 00:00:00";
$fecha_fin    = $_GET['fecha_fin'] . " 23:59:59";

// Consulta
$stmt = $conn->prepare("SELECT id, codigo_producto, nombre_producto, cantidad, usuario, fecha 
                        FROM movimientoo 
                        WHERE fecha BETWEEN ? AND ?
                        ORDER BY fecha DESC");
$stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt->execute();
$result = $stmt->get_result();

// Crear documento XLSX
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Movimientos');

// Encabezados
$headers = ['ID', 'Código', 'Nombre', 'Cantidad', 'Usuario', 'Fecha'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $col++;
}

// Datos
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['id']);
    $sheet->setCellValue('B' . $rowNumber, $row['codigo_producto']);
    $sheet->setCellValue('C' . $rowNumber, $row['nombre_producto']);
    $sheet->setCellValue('D' . $rowNumber, $row['cantidad']);
    $sheet->setCellValue('E' . $rowNumber, $row['usuario']);
    $sheet->setCellValue('F' . $rowNumber, $row['fecha']);
    $rowNumber++;
}

// Auto-size columnas
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

$filename = "movimientos_" . date('Ymd_His') . ".xlsx";

// Cabeceras de descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Exportar
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$stmt->close();
$conn->close();
exit;
