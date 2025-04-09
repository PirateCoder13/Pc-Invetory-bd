<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require 'includes/conexao.php'; // Arquivo PDO corrigido

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO maquinas 
            (nome, status, ip, mac, comentario, chamado, mesh, wsus, av, ocs, regional)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['status'],
            $_POST['ip'],
            $_POST['mac'],
            $_POST['comentario'],
            $_POST['chamado'],
            $_POST['mesh'],
            $_POST['wsus'],
            $_POST['av'],
            $_POST['ocs'],
            $_POST['regional']
        ]);

        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}

// Buscar regionais
$regionais = $pdo->query("SELECT id, nome FROM regionais")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Máquina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">Voltar</a>
    <h1>Cadastro de Máquina</h1>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Status:</label>
            <select name="status" class="form-select" required>
                <option value="Ativa">Ativa</option>
                <option value="Inativa">Inativa</option>
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">IP:</label>
                <input type="text" name="ip" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">MAC:</label>
                <input type="text" name="mac" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Comentário:</label>
            <textarea name="comentario" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Chamado:</label>
            <input type="text" name="chamado" class="form-control">
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Mesh:</label>
                <select name="mesh" class="form-select">
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">WSUS:</label>
                <select name="wsus" class="form-select">
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">AV:</label>
                <select name="av" class="form-select">
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">OCS:</label>
                <select name="ocs" class="form-select">
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
                </select>
            </div>
        </div>
        <!-- Repetir para WSUS, AV e OCS -->

        <div class="mb-3">
            <label class="form-label">Regional:</label>
            <select name="regional" class="form-select" required>
                <?php foreach ($regionais as $regional): ?>
                    <option value="<?= $regional['id'] ?>">
                        <?= htmlspecialchars($regional['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</body>
</html>
