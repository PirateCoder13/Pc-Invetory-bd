<?php
session_start();

// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'controle_maquinas';
$username = 'root';
$password = ''; // Senha vazia, se não foi alterada

// Configuração do DSN e opções do PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Tentativa de conexão com o banco de dados
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Erro de conexão com o banco de dados: ' . $e->getMessage());
}

// Título da página
$titulo = "Dashboard";

// Inclusão do cabeçalho
require 'includes/header.php';

// Lógica de pesquisa
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$where = '';
$params = [];

if ($pesquisa) {
    $where = "WHERE nome LIKE ? OR ip LIKE ?";
    $params = ["%$pesquisa%", "%$pesquisa%"];
}

// Consulta SQL para obter máquinas com informações de regional e contato
$sql = "
    SELECT m.*, r.nome as regional_nome, 
           c.contato_recente, c.contato_anterior
    FROM Maquinas m
    LEFT JOIN Regionais r ON m.regional = r.id
    LEFT JOIN Contato c ON m.id = c.maquina_id
    $where
    ORDER BY m.nome
";

// Preparação e execução da consulta SQL
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$maquinas = $stmt->fetchAll();

?>

<!-- HTML da página -->
<h2 class="mb-4">Máquinas Cadastradas</h2>

<form class="mb-4">
    <div class="input-group">
        <input type="text" name="pesquisa" class="form-control"
            placeholder="Pesquisar por nome ou IP" value="<?= htmlspecialchars($pesquisa) ?>">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
    </div>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>IP</th>
            <th>Status</th>
            <th>Último Contato</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($maquinas as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['nome']) ?></td>
                <td><?= htmlspecialchars($m['ip']) ?></td>
                <td><span class="badge bg-<?= $m['status'] == 'Ativa' ? 'success' : 'danger' ?>">
                        <?= htmlspecialchars($m['status']) ?>
                    </span></td>
                <td>
                    <?php if ($m['contato_recente']): ?>
                        <?= date('d/m/Y H:i', strtotime($m['contato_recente'])) ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <a href="maquinas/editar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="maquinas/excluir.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Tem certeza?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require 'includes/footer.php'; ?>