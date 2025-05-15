<?php
require_once 'includes/auth.php';
require 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $erros = [];

    // Consulta única para nome, ip e mac
    $stmt = $pdo->prepare("
        SELECT nome, ip, mac FROM maquinas 
        WHERE nome = ? OR ip = ? OR mac = ?
        LIMIT 1
    ");
    $stmt->execute([$_POST['nome'], $_POST['ip'], $_POST['mac']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && empty($_POST['forcar_cadastro'])) {
        if ($row['nome'] === $_POST['nome']) {
            $erros[] = "Já existe uma máquina com este <b>nome</b>!";
        }
        if ($row['ip'] === $_POST['ip']) {
            $erros[] = "Já existe uma máquina com este <b>IP</b>!";
        }
        if ($row['mac'] === $_POST['mac']) {
            $erros[] = "Já existe uma máquina com este <b>MAC</b>!";
        }
    }

    if ($erros && empty($_POST['forcar_cadastro'])) {
        echo '<div class="alert alert-danger text-center">' . implode('<br>', $erros) . '</div>';
        // Botão para cadastrar mesmo assim
        echo '
        <form method="POST" style="text-align:center;">
            ' . 
            // Repita todos os campos do POST como campos ocultos
            array_reduce(array_keys($_POST), function($carry, $key) {
                if ($key === 'submit') return $carry;
                return $carry . '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($_POST[$key]).'">';
            }, '') . '
            <input type="hidden" name="forcar_cadastro" value="1">
            <button type="submit" name="submit" class="btn btn-warning mt-2">Cadastrar mesmo assim</button>
        </form>
        ';
    } else if (!$erros || !empty($_POST['forcar_cadastro'])) {
        try {
            // Iniciar transação
            $pdo->beginTransaction();

            // Inserir na tabela `maquinas`
            $stmtMaquinas = $pdo->prepare("
                INSERT INTO maquinas 
                (nome, status, ip, mac, comentario, chamado, mesh, wsus, av, ocs, regional, data_cadastro)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtMaquinas->execute([
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
                intval($_POST['regional']),  // Certifique-se de que 'regional' seja um inteiro
                $_POST['data_cadastro']
            ]);

            // Obter o ID da máquina recém-inserida
            $maquinaId = $pdo->lastInsertId();

            // Inserir na tabela `contato`
            $stmtContato = $pdo->prepare("
                INSERT INTO contato (maquina_id, contato_recente, contato_anterior)
                VALUES (?, ?, ?)
            ");
            $stmtContato->execute([
                $maquinaId,
                $_POST['data_contato'], // Data de contato fornecida pelo usuário
                null                    // contato_anterior será NULL
            ]);

            // Confirmar transação
            $pdo->commit();

            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            // Reverter transação em caso de erro
            $pdo->rollBack();
            die("Erro ao cadastrar: " . $e->getMessage());
        }
    }
}

// Buscar regionais
$regionais = $pdo->query("SELECT id, nome, comentario FROM regionais")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <title>Cadastro de Máquina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #f8f9fa;
        }

        .container {
            max-width: 800px;
        }

        .form-control,
        .form-select,
        .form-control:focus,
        .form-select:focus {
            background-color: #23272b;
            color: #f8f9fa;
            border-color: #343a40;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-primary,
        .btn-secondary {
            color: #fff;
        }

        label {
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Cadastro de Máquina</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status:</label>
                <select name="status" class="form-select" required>
                    <option value="OK">OK</option>
                    <option value="Fazendo">Fazendo</option>
                    <option value="Em Chamado">Em Chamado</option>
                    <option value="OFF">OFF</option>
                </select>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">IP:</label>
                    <input type="text" name="ip" id="ip" class="form-control">
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

            <div class="mb-3">
                <label class="form-label">Regional:</label>
                <select name="regional" class="form-select" id="regional" required>
                    <?php foreach ($regionais as $regional): ?>
                        <option value="<?= $regional['id'] ?>" data-comentario="<?= htmlspecialchars($regional['comentario']) ?>">
                            <?= htmlspecialchars($regional['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="regional-nome" class="mt-2 text-info"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Data de Cadastro:</label>
                <input type="date" name="data_cadastro" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Último Contato:</label>
                <input type="datetime-local" name="data_contato" class="form-control">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Cadastrar</button>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let regionaisJson = {};
        let nomeParaId = {};
        let idParaComentario = {};
        const select = document.getElementById('regional');
        const regionalNomeDiv = document.getElementById('regional-nome');
        for (let i = 0; i < select.options.length; i++) {
            const nome = select.options[i].textContent.trim();
            const id = select.options[i].value;
            nomeParaId[nome] = id;
            idParaComentario[id] = select.options[i].getAttribute('data-comentario');
        }

        fetch('regionais.json')
            .then(response => response.json())
            .then(data => { regionaisJson = data; });

        function atualizarRegional() {
            const ip = document.getElementById('ip').value.trim();
            const ipBase = ip.split('.').slice(0, 3).join('.') + '.0';
            if(regionaisJson[ipBase]) {
                const nomeRegional = regionaisJson[ipBase].trim();
                if (nomeParaId[nomeRegional]) {
                    select.value = nomeParaId[nomeRegional];
                    // Mostra nome + cidades
                    regionalNomeDiv.innerHTML = `<b>${nomeRegional}</b><br><small>${idParaComentario[nomeParaId[nomeRegional]] || ''}</small>`;
                } else {
                    regionalNomeDiv.textContent = nomeRegional;
                }
            } else {
                regionalNomeDiv.textContent = "Regional não encontrada para este IP.";
            }
        }

        document.getElementById('ip').addEventListener('input', atualizarRegional);
        document.getElementById('ip').addEventListener('blur', atualizarRegional);
    });
    </script>
</body>

</html>