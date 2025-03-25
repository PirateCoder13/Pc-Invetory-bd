<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE login = ?");
    $stmt->execute([$login]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        header("Location: /");
        exit();
    } else {
        $erro = "Credenciais inválidas!";
    }
}

$titulo = "Login";
require 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <h2 class="mb-4">Login</h2>
        
        <?php if(isset($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Login</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>