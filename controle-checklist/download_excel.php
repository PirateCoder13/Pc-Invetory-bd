php
<?php

require_once __DIR__ . '/../includes/conn.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['month'])) {
    $month = $_GET['month'];

    $startDate = $month . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));

    $query = "SELECT * FROM acoes WHERE data_acao BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Descrição');
    $sheet->setCellValue('B1', 'Data da Ação');

    $row = 2;
    while ($action = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $action['descricao']);
        $sheet->setCellValue('B' . $row, $action['data_acao']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'acoes_' . $month . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
} else {
    echo "Mês não especificado.";
}
?>