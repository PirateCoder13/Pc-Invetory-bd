<?php
require_once 'includes/auth.php';
require 'includes/conn.php';

$maquina = [];
$regionais = [];

try {
    // Buscar dados da máquina
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("
            SELECT m.*, c.contato_recente, c.contato_anterior 
            FROM maquinas m
            LEFT JOIN contato c ON m.id = c.maquina_id
            WHERE m.id = ?
        ");
        $stmt->execute([$_GET['id']]);
        $maquina = $stmt->fetch();

        if (!$maquina) {
            header('Location: index.php');
            exit();
        }
    }

    // Buscar regionais
    $stmt = $pdo->query("SELECT id, nome FROM regionais");
    $regionais = $stmt->fetchAll();

    // Processar atualização
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $dadosMaquinas = [
            ':id' => $_POST['id'],
            ':nome' => $_POST['nome'],
            ':status' => $_POST['status'],
            ':ip' => $_POST['ip'],
            ':mac' => $_POST['mac'],
            ':comentario' => $_POST['comentario'],
            ':chamado' => $_POST['chamado'],
            ':mesh' => $_POST['mesh'],
            ':wsus' => $_POST['wsus'],
            ':av' => $_POST['av'],
            ':ocs' => $_POST['ocs'],
            ':regional' => $_POST['regional'],
            ':data_cadastro' => $_POST['data_cadastro']
        ];

        $stmtMaquinas = $pdo->prepare("
            UPDATE maquinas SET 
                nome = :nome,
                status = :status,
                ip = :ip,
                mac = :mac,
                comentario = :comentario,
                chamado = :chamado,
                mesh = :mesh,
                wsus = :wsus,
                av = :av,
                ocs = :ocs,
                regional = :regional,
                data_cadastro = :data_cadastro
            WHERE id = :id
        ");

        $stmtMaquinas->execute($dadosMaquinas);

        // Atualizar contato
        $dadosContato = [
            ':maquina_id' => $_POST['id'],
            ':contato_recente' => $_POST['contato_recente']
        ];

        $stmtContato = $pdo->prepare("
            UPDATE contato SET 
                contato_recente = :contato_recente
            WHERE maquina_id = :maquina_id
        ");

        $stmtContato->execute($dadosContato);

        header('Location: index.php');
        exit();
    }

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Máquina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #f8f9fa;
        }
        .form-control, .form-select, .form-control:focus, .form-select:focus {
            background-color: #23272b;
            color: #f8f9fa;
            border-color: #343a40;
        }
        .btn-primary { background-color: #0d6efd; border: none; }
        .btn-secondary { background-color: #6c757d; border: none; }
        .btn-primary, .btn-secondary { color: #fff; }
        label { margin-top: 0.5rem; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Editar Máquina</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $maquina['id'] ?>">

        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($maquina['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Status:</label>
            <select name="status" class="form-select" required>
                <option value="OK" <?= $maquina['status'] === 'OK' ? 'selected' : '' ?>>OK</option>
                <option value="Fazendo" <?= $maquina['status'] === 'Fazendo' ? 'selected' : '' ?>>Fazendo</option>
                <option value="Em Chamado" <?= $maquina['status'] === 'Em Chamado' ? 'selected' : '' ?>>Em Chamado</option>
                <option value="OFF" <?= $maquina['status'] === 'OFF' ? 'selected' : '' ?>>OFF</option>
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label>IP:</label>
                <input type="text" name="ip" class="form-control" value="<?= htmlspecialchars($maquina['ip']) ?>">
            </div>
            <div class="col-md-6">
                <label>MAC:</label>
                <input type="text" name="mac" class="form-control" value="<?= htmlspecialchars($maquina['mac']) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label>Comentário:</label>
            <textarea name="comentario" class="form-control"><?= htmlspecialchars($maquina['comentario']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Chamado:</label>
            <input type="text" name="chamado" class="form-control" value="<?= htmlspecialchars($maquina['chamado']) ?>">
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label>Mesh:</label>
                <select name="mesh" class="form-select">
                    <option value="S" <?= $maquina['mesh'] === 'S' ? 'selected' : '' ?>>Sim</option>
                    <option value="N" <?= $maquina['mesh'] === 'N' ? 'selected' : '' ?>>Não</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>WSUS:</label>
                <select name="wsus" class="form-select">
                    <option value="S" <?= $maquina['wsus'] === 'S' ? 'selected' : '' ?>>Sim</option>
                    <option value="N" <?= $maquina['wsus'] === 'N' ? 'selected' : '' ?>>Não</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>AV:</label>
                <select name="av" class="form-select">
                    <option value="S" <?= $maquina['av'] === 'S' ? 'selected' : '' ?>>Sim</option>
                    <option value="N" <?= $maquina['av'] === 'N' ? 'selected' : '' ?>>Não</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>OCS:</label>
                <select name="ocs" class="form-select">
                    <option value="S" <?= $maquina['ocs'] === 'S' ? 'selected' : '' ?>>Sim</option>
                    <option value="N" <?= $maquina['ocs'] === 'N' ? 'selected' : '' ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Regional:</label>
            <select name="regional" class="form-select" required>
                <?php foreach ($regionais as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $maquina['regional'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Data de Cadastro:</label>
            <input type="date" name="data_cadastro" class="form-control"
                value="<?= htmlspecialchars($maquina['data_cadastro']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Último Contato:</label>
            <input type="datetime-local" name="contato_recente" class="form-control"
                value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($maquina['contato_recente']))) ?>">
        </div>

        <button type="submit" name="update" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
</body>
</html>