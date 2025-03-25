<?php
require '../../includes/conexao.php';
require '../../includes/auth.php';
verificaLogin();

$titulo = "Cadastrar Máquina";
require '../../includes/header.php';

$regionais = $pdo->query("SELECT * FROM Regionais")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO Maquinas (
                nome, ip, mac, status, comentario, chamado,
                mesh, wsus, av, ocs, regional, data_cadastro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $_POST['nome'],
            $_POST['ip'],
            $_POST['mac'],
            $_POST['status'],
            $_POST['comentario'],
            $_POST['chamado'],
            $_POST['mesh'],
            $_POST['wsus'],
            $_POST['av'],
            $_POST['ocs'],
            $_POST['regional']
        ]);
        
        header("Location: /");
        exit();
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar máquina: " . $e->getMessage();
    }
}
?>

<h2>Cadastrar Nova Máquina</h2>

<?php if(isset($erro)): ?>
<div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<form method="post">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nome da Máquina</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="Ativa">Ativa</option>
                <option value="Inativa">Inativa</option>
                <option value="Manutenção">Em Manutenção</option>
            </select>
        </div>
        
        <div class="col-md-4">
            <label class="form-label">IP</label>
            <input type="text" name="ip" class="form-control">
        </div>
        
        <div class="col-md-4">
            <label class="form-label">MAC Address</label>
            <input type="text" name="mac" class="form-control">
        </div>
        
        <div class="col-md-4">
            <label class="form-label">Regional</label>
            <select name="regional" class="form-select">
                <option value="">Selecione...</option>
                <?php foreach ($regionais as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Mesh</label>
            <select name="mesh" class="form-select">
                <option value="S">SIM</option>
                <option value="N">NÃO</option>
            </select>
        </div>
        
        <!-- Repetir para WSUS, AV, OCS -->
        
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="/" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
</form>

<?php require '../../includes/footer.php'; ?>