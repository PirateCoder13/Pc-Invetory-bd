<?php
require_once 'includes/auth.php';
require_once 'includes/conn.php';

// Buscar todas as máquinas ordenadas por data de cadastro (mais recente primeiro)
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
    ORDER BY m.data_cadastro DESC, m.nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$maquinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por data de cadastro
$porDia = [];
foreach ($maquinas as $m) {
    $dia = $m['data_cadastro'] ? date('d/m/Y', strtotime($m['data_cadastro'])) : 'Sem data';
    $porDia[$dia][] = $m;
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Máquinas por Dia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #181a1b; color: #f8f9fa; }
        .table-dark th, .table-dark td { color: #f8f9fa; }
        .table-group-header {
            background: #343a40;
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Máquinas por Dia</h2>
            <div>
                <a href="index.php" class="btn btn-secondary btn-sm">Voltar</a>
            </div>
        </div>
        <div class="table-responsive">
            <?php if (empty($porDia)): ?>
                <div class="alert alert-info mt-3">Nenhuma máquina encontrada.</div>
            <?php else: ?>
                <table class="table table-striped table-hover table-dark align-middle">
                    <thead>
                        <tr>
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
                        <?php foreach ($porDia as $dia => $grupo): ?>
                            <tr class="table-group-header">
                                <td colspan="14"><?= htmlspecialchars($dia) ?></td>
                            </tr>
                            <?php foreach ($grupo as $maquina): ?>
                                <tr>
                                    <td><?= htmlspecialchars($maquina['nome']) ?></td>
                                    <td><?= htmlspecialchars($maquina['ip']) ?></td>
                                    <td>
                                        <?php
                                            $status = $maquina['status'];
                                            $badgeClass = 'secondary';
                                            if ($status === 'OFF') $badgeClass = 'danger';
                                            elseif ($status === 'Fazendo') $badgeClass = 'warning text-dark';
                                            elseif ($status === 'Em Chamado') $badgeClass = 'primary';
                                            elseif ($status === 'OK') $badgeClass = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>