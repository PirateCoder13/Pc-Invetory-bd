<?php
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteStmt = $pdo->prepare("DELETE FROM maquinas WHERE id = ?");
    if ($deleteStmt->execute([$id])) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao excluir máquina.";
    }
}

require_once 'includes/conn.php';

// Processar pesquisa
$pesquisa = $_GET['pesquisa'] ?? '';
$where = [];
$params = [];

if (!empty($pesquisa)) {
    $where[] = "(m.nome LIKE :pesquisa OR m.ip LIKE :pesquisa)";
    $params[':pesquisa'] = "%$pesquisa%";
}

// Montar consulta
$sql = "
    SELECT 
        m.id, m.nome, m.status, m.ip, m.mac, 
        m.comentario, m.chamado, m.data_cadastro,
        m.mesh, m.wsus, m.av, m.ocs,
        r.nome AS regional_nome, 
        r.comentario AS regional_comentario,
        c.contato_recente
    FROM maquinas m
    LEFT JOIN regionais r ON m.regional = r.id
    LEFT JOIN contato c ON m.id = c.maquina_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY m.id";

// Executar consulta
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $maquinas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Máquinas</title>
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

        .input-group-text,
        .btn-primary,
        .btn-success,
        .btn-danger,
        .btn-warning {
            border: none;
        }

        .btn-primary,
        .btn-success,
        .btn-danger,
        .btn-warning {
            color: #fff;
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
            <h2>Controle de Máquinass</h2>
            <div>
                <a href="cadastro.php" class="btn btn-success btn-sm">Nova Máquina</a>
                <a href="controle-checklist/download_excel.php" class="btn btn-info btn-sm text-white">Download</a>
                <a href="maquinas_fazendo.php" class="btn btn-warning btn-sm text-dark">Fazendo</a>
                <a href="maquinas_duplicada.php" class="btn btn-warning btn-sm text-dark">Duplicadas</a>
                <a href="maquinas_dia.php" class="btn btn-warning btn-sm text-dark">Dia</a>
                <form method="post" class="d-inline">
                    <button type="submit" name="logout" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>

        <!-- Formulário de Pesquisa -->
        <form class="mb-4">
            <div class="input-group">
                <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar por nome ou IP"
                    value="<?= htmlspecialchars($pesquisa) ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <!-- Tabela de Máquinas -->
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
                                <a href="index.php?delete=<?= $maquina['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir esta máquina?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>