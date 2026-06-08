<?php

header("Content-Type: application/json");

require_once "../db.php";

$user_id = $_GET["user_id"] ?? 0;

try {

    $stmt = $pdo->prepare("
        SELECT user_id, nama, email, role
        FROM user
        WHERE user_id = ?
        LIMIT 1
    ");

    $stmt->execute([$user_id]);

    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "User tidak ditemukan"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "user" => $user
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
