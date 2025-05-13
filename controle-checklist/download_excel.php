<?php

require_once __DIR__ . '/../includes/conn.php';

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
} elseif (isset($_GET['data_inicio']) && isset($_GET['data_fim'])) {
    $dataInicio = $_GET['data_inicio'];
    $dataFim = $_GET['data_fim'];

    $query = "SELECT * FROM maquinas WHERE data_cadastro BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$dataInicio, $dataFim]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=maquinas_' . $dataInicio . '_a_' . $dataFim . '.csv');

    $output = fopen('php://output', 'w');
    // Cabeçalhos
    fputcsv($output, [
        'Nome',
        'Status',
        'IP',
        'MAC',
        'Comentário',
        'Chamado',
        'Data Cadastro',
        'Mesh',
        'Antivirus',
        'WSUS',
        'OCS'
    ]);
    foreach ($result as $maquina) {
        // Converte S/N para Instalado/Não instalado
        $mesh = ($maquina['mesh'] === 'S') ? 'Instalado' : 'Não instalado';
        $av = ($maquina['av'] === 'S') ? 'Instalado' : 'Não instalado';
        $wsus = ($maquina['wsus'] === 'S') ? 'Instalado' : 'Não instalado';
        $ocs = ($maquina['ocs'] === 'S') ? 'Instalado' : 'Não instalado';

        fputcsv($output, [
            $maquina['nome'],
            $maquina['status'],
            $maquina['ip'],
            $maquina['mac'],
            $maquina['comentario'],
            $maquina['chamado'],
            $maquina['data_cadastro'],
            $mesh,
            $av,
            $wsus,
            $ocs
        ]);
    }
    fclose($output);
    exit;
} else {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Exportar Máquinas por Data de Cadastro</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="container mt-5">
        <h2>Exportar Máquinas por Data de Cadastro</h2>
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="data_inicio" class="form-label">Data Início:</label>
                <input type="date" name="data_inicio" id="data_inicio" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="data_fim" class="form-label">Data Fim:</label>
                <input type="date" name="data_fim" id="data_fim" class="form-control" required>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success">Baixar .csv</button>
                <a href="../index.php" class="btn btn-secondary">Voltar ao Início</a>
            </div>
        </form>
    </body>

    </html>
    <?php
}
?>