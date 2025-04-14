<?php
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

require 'includes/conn.php';

$maquina = [];
$regionais = [];

try {
    // Buscar dados da máquina
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("
            SELECT m.*, c.contato_recente, c.contato_anterior 
            FROM maquinas m
            LEFT JOIN Contato c ON m.id = c.maquina_id
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
        $dados = [ 
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
            ':regional' => $_POST['regional']
        ];

        $stmt = $pdo->prepare("
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
                regional = :regional
            WHERE id = :id
        ");

        if ($stmt->execute($dados)) {
            header('Location: index.php');
            exit();
        }
    }

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

include 'includes/header.php';
?>

<h1>Editar Máquina</h1>
<form method="POST">
    <input type="hidden" name="id" value="<?= $maquina['id'] ?>">
    
    <div class="mb-3">
        <label>Nome:</label>
        <input type="text" name="nome" class="form-control" 
            value="<?= htmlspecialchars($maquina['nome']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Status:</label>
        <select name="status" class="form-select" required>
            <option value="Ativa" <?= $maquina['status'] === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
            <option value="Inativa" <?= $maquina['status'] === 'Inativa' ? 'selected' : '' ?>>Inativa</option>
        </select>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label>IP:</label>
            <input type="text" name="ip" class="form-control" 
                value="<?= htmlspecialchars($maquina['ip']) ?>">
        </div>
        
        <div class="col-md-6">
            <label>MAC:</label>
            <input type="text" name="mac" class="form-control" 
                value="<?= htmlspecialchars($maquina['mac']) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label>Comentário:</label>
        <textarea name="comentario" class="form-control"><?= 
            htmlspecialchars($maquina['comentario']) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Chamado:</label>
        <input type="text" name="chamado" class="form-control" 
            value="<?= htmlspecialchars($maquina['chamado']) ?>">
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

        
        <!-- Repetir para WSUS, AV e OCS -->
    </div>

    <div class="mb-3">
        <label>Regional:</label>
        <select name="regional" class="form-select" required>
            <?php foreach ($regionais as $r): ?>
                <option value="<?= $r['id'] ?>" 
                    <?= $r['id'] == $maquina['regional'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Atualizar</button>
</form>

<?php include 'includes/footer.php'; ?>
