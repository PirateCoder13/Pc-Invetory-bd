<?php

function log_maquina_apagada($maquina, $ip, $data_exclusao, $hora_exclusao) {
    include_once __DIR__ . '/../includes/conn.php';

    $query = "INSERT INTO apagadas (maquina, ip, data_exclusao, hora_exclusao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $maquina, $ip, $data_exclusao, $hora_exclusao);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}