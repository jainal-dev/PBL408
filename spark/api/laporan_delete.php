<?php

header("Content-Type: application/json");

require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

try {

    $stmt = $pdo->prepare("
        DELETE FROM lapora_user
        WHERE laporan_id = ?
        AND user_id = ?
    ");

    $stmt->execute([
        $data["laporan_id"],
        $data["user_id"]
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Laporan berhasil dihapus"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}