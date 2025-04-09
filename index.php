<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Configurações de conexão
$host = 'localhost';
$dbname = 'controle_maquinas';
$username = 'root';
$password = '';

// Conexão PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

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
        r.nome AS regional_nome,
        c.contato_recente
    FROM maquinas m
    LEFT JOIN regionais r ON m.regional = r.id
    LEFT JOIN contato c ON m.id = c.maquina_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY m.nome";

// Executar consulta
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $maquinas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

// Header
require 'includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Controle de Máquinas</h2>
        <div>
            <a href="cadastro.php" class="btn btn-success btn-sm">Nova Máquina</a>
            <form method="post" class="d-inline">
                <button type="submit" name="logout" class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>

    <!-- Formulário de Pesquisa -->
    <form class="mb-4">
        <div class="input-group">
            <input type="text" name="pesquisa" class="form-control" 
                placeholder="Pesquisar por nome ou IP" value="<?= htmlspecialchars($pesquisa) ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </form>

    <!-- Tabela de Máquinas -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
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
                    <th>Último Contato</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maquinas as $maquina): ?>
                    <tr>
                        <td><?= htmlspecialchars($maquina['nome']) ?></td>
                        <td><?= htmlspecialchars($maquina['ip']) ?></td>
                        <td>
                            <span class="badge bg-<?= $maquina['status'] === 'Ativa' ? 'success' : 'danger' ?>">
                                <?= htmlspecialchars($maquina['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($maquina['regional_nome'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($maquina['mac']) ?></td>
                        <td><?= htmlspecialchars($maquina['mesh']) ?></td>
                        <td><?= htmlspecialchars($maquina['av']) ?></td>
                        <td><?= htmlspecialchars($maquina['wsus']) ?></td>
                        <td><?= htmlspecialchars($maquina['ocs']) ?></td>
                        <td><?= htmlspecialchars($maquina['comentario']) ?></td>
                        <td><?= htmlspecialchars($maquina['chamado']) ?></td>
                        <td>
                            <?= $maquina['contato_recente'] 
                                ? date('d/m/Y H:i', strtotime($maquina['contato_recente'])) 
                                : 'N/A' ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $maquina['id'] ?>" 
                               class="btn btn-warning btn-sm">Editar</a>
                            <a href="excluir.php?id=<?= $maquina['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Footer
require 'includes/footer.php';
?>
