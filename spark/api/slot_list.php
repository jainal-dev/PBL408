<?php

header("Content-Type: application/json");

require_once "../db.php";

$parkir_id = $_GET["parkir_id"] ?? 0;

$stmt = $pdo->prepare("
    SELECT *
    FROM slot_parkir
    WHERE parkir_id = ?
    ORDER BY nomor_slot
");

$stmt->execute([$parkir_id]);

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll()
]);