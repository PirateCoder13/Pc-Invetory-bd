<?php
function verificaLogin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /login.php");
        exit();
    }
}

function getUsuario($pdo)
{
    if (isset($_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("SELECT id, nome FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch();
    }
    return null;
}
