<?php
require_once 'includes/auth.php';
require_once 'includes/conn.php';

// Buscar máquinas com status 'Fazendo'
$sql = "
    SELECT 
        m.id, m.nome, m.status, m.ip, m.mac, 
        m.comentario, m.chamado, m.data_cadastro,
        m.mesh, m.wsus, m.av, m.ocs,
        r.nome AS regional_nome, 
        c.contato_recente
    FROM maquinas m
    LEFT JOIN regionais r ON m.regional = r.id
    LEFT JOIN contato c ON m.id = c.maquina_id
    WHERE m.status = 'Fazendo'
    ORDER BY m.nome
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $maquinas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Máquinas em andamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #f8f9fa;
        }

        .table-dark th,
        .table-dark td {
            color: #f8f9fa;
        }

        .form-control,
        .form-control:focus {
            background-color: #23272b;
            color: #f8f9fa;
            border-color: #343a40;
        }

        .btn-primary,
        .btn-success,
        .btn-danger,
        .btn-warning {
            color: #fff;
            border: none;
        }

        .btn-primary {
            background-color: #0d6efd;
        }

        .btn-success {
            background-color: #198754;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #23272b;
        }

        .table-striped>tbody>tr:nth-of-type(even) {
            background-color: #181a1b;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Máquinas em andamento (Status: Fazendo)</h2>
            <div>
                <a href="cadastro.php" class="btn btn-success btn-sm">Nova Máquina</a>
                <a href="controle-checklist/download_excel.php" class="btn btn-primary btn-sm">Download</a>
                <a href="index.php" class="btn btn-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-dark align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>IP</th>
                        <th>Status</th>
                        <th>Regional</th>
                        <th>MAC</th>
                        <th>MESH</th>
                        <th>AV</th>
                        <th>WSUS</th>
                        <th>OCS</th>
                        <th>COMENTARIO</th>
                        <th>CHAMADO</th>
                        <th>Data Cadastro</th>
                        <th>Último Contato</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maquinas as $maquina): ?>
                        <tr>
                            <td><?= htmlspecialchars($maquina['id']) ?></td>
                            <td><?= htmlspecialchars($maquina['nome']) ?></td>
                            <td><?= htmlspecialchars($maquina['ip']) ?></td>
                            <td>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($maquina['status']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($maquina['regional_nome'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($maquina['mac'] ?? 'N/A') ?></td>
                            <td>
                                <?= $maquina['mesh'] === 'S' ? '<span class="text-success">Instalado</span>' : '<span class="text-danger">Não instalado</span>' ?>
                            </td>
                            <td>
                                <?= $maquina['av'] === 'S' ? '<span class="text-success">Instalado</span>' : '<span class="text-danger">Não instalado</span>' ?>
                            </td>
                            <td>
                                <?= $maquina['wsus'] === 'S' ? '<span class="text-success">Instalado</span>' : '<span class="text-danger">Não instalado</span>' ?>
                            </td>
                            <td>
                                <?= $maquina['ocs'] === 'S' ? '<span class="text-success">Instalado</span>' : '<span class="text-danger">Não instalado</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($maquina['comentario'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($maquina['chamado'] ?? 'N/A') ?></td>
                            <td>
                                <?= $maquina['data_cadastro']
                                    ? date('d/m/Y', strtotime($maquina['data_cadastro']))
                                    : 'N/A' ?>
                            </td>
                            <td>
                                <?= $maquina['contato_recente']
                                    ? date('d/m/Y H:i', strtotime($maquina['contato_recente']))
                                    : 'N/A' ?>
                            </td>
                            <td>
                                <a href="editar.php?id=<?= $maquina['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($maquinas)): ?>
                <div class="alert alert-info mt-3">Nenhuma máquina com status "Fazendo" encontrada.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>