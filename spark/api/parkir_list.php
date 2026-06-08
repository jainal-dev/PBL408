<?php

header("Content-Type: application/json");

require_once "../db.php";

$stmt = $pdo->query("
    SELECT *
    FROM parkir
    ORDER BY lokasi
");

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll()
]);