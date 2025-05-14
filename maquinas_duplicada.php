<?php
require_once 'includes/auth.php';
require_once 'includes/conn.php';

// Buscar máquinas com IP ou MAC duplicado agrupadas
$sql = "
    SELECT m.*
    FROM maquinas m
    WHERE m.ip IN (
        SELECT ip FROM maquinas WHERE ip IS NOT NULL AND ip != '' GROUP BY ip HAVING COUNT(*) > 1
    )
    OR m.mac IN (
        SELECT mac FROM maquinas WHERE mac IS NOT NULL AND mac != '' GROUP BY mac HAVING COUNT(*) > 1
    )
    ORDER BY m.ip, m.mac, m.nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$maquinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por IP e por MAC
$agrupadas = [];
foreach ($maquinas as $m) {
    if (!empty($m['ip'])) {
        $agrupadas['IP: ' . $m['ip']][] = $m;
    }
    if (!empty($m['mac'])) {
        $agrupadas['MAC: ' . $m['mac']][] = $m;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Máquinas Duplicadas na Rede</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .table-group-header {
            background: #343a40;
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Máquinas Duplicadas na Rede (IP ou MAC)</h2>
            <div>
                <a href="index.php" class="btn btn-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="table-responsive">
            <?php if (empty($agrupadas)): ?>
                <div class="alert alert-success mt-3">Nenhuma máquina duplicada encontrada.</div>
            <?php else: ?>
                <table class="table table-striped table-hover table-dark align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>IP</th>
                            <th>MAC</th>
                            <th>Status</th>
                            <th>Regional</th>
                            <th>MESH</th>
                            <th>Comentário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agrupadas as $chave => $grupo): ?>
                            <tr class="table-group-header">
                                <td colspan="9"><?= htmlspecialchars($chave) ?></td>
                            </tr>
                            <?php foreach ($grupo as $maquina): ?>
                                <tr>
                                    <td><?= htmlspecialchars($maquina['id']) ?></td>
                                    <td><?= htmlspecialchars($maquina['nome']) ?></td>
                                    <td><?= htmlspecialchars($maquina['ip']) ?></td>
                                    <td><?= htmlspecialchars($maquina['mac']) ?></td>
                                    <td><?= htmlspecialchars($maquina['status']) ?></td>
                                    <td><?= htmlspecialchars($maquina['regional'] ?? 'N/A') ?></td>
                                    <td>
                                        <?= $maquina['mesh'] === 'S'
                                            ? '<span class="text-success">Instalado</span>'
                                            : '<span class="text-danger">Não instalado</span>' ?>
                                    </td>
                                    <td><?= htmlspecialchars($maquina['comentario'] ?? '') ?></td>
                                    <td>
                                        <a href="editar.php?id=<?= $maquina['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>