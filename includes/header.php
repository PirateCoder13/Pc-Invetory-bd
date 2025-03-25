<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Controle de Máquinas' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Controle de Máquinas</a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="navbar-nav">
                    <a class="nav-link" href="/maquinas/cadastrar.php">Nova Máquina</a>
                    <a class="nav-link" href="/regionais/listar.php">Regionais</a>
                    <a class="nav-link" href="/logout.php">Sair</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container mt-4"></div>