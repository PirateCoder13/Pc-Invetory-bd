<?php
session_start(); // Inicia a sessão, se ainda não foi iniciada

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    // Redireciona para a página de login, se não estiver logadologin, se não estiver logado
    header('Location: /login.php');
    exit();
}
?>