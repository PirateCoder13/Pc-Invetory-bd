<?php
include_once __DIR__ . '/../includes/conn.php';

// Fetch actions from the database, ordered by date
$result = $conn->query("SELECT * FROM acoes ORDER BY data_acao DESC");

$actionsByMonth = [];
while ($row = $result->fetch_assoc()) {
    $monthYear = date("Y-m", strtotime($row['data_acao']));
    if (!isset($actionsByMonth[$monthYear])) {
        $actionsByMonth[$monthYear] = [];
    }
    $actionsByMonth[$monthYear][] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Histórico de Ações</title>
</head>
<body>

<h1>Histórico de Ações</h1>

<?php foreach ($actionsByMonth as $monthYear => $actions): ?>
    <h2><?php echo date("F Y", strtotime($monthYear . "-01")); ?></h2>
    <ul>
        <?php foreach ($actions as $action): ?>
            <li><?php echo $action['descricao']; ?> - <?php echo date("d/m/Y H:i", strtotime($action['data_acao'])); ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="download_excel.php?month=<?php echo $monthYear; ?>">Download Excel (<?php echo date("F Y", strtotime($monthYear . "-01")); ?>)</a>
<?php endforeach; ?>

</body>
</html>













