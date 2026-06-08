<?php

header("Content-Type: application/json");

require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data["user_id"]) ||
    !isset($data["slot_id"]) ||
    !isset($data["kondisi"]) ||
    empty($data["slot_id"])
) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO lapora_user
        (
            user_id,
            slot_id,
            kondisi_dilaporkan,
            deskripsi
        )
        VALUES
        (
            :user_id,
            :slot_id,
            :kondisi,
            :deskripsi
        )
    ");

    $stmt->execute([
        ":user_id" => $data["user_id"],
        ":slot_id" => $data["slot_id"],
        ":kondisi" => $data["kondisi"],
        ":deskripsi" => $data["deskripsi"] ?? ""
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Laporan berhasil dibuat"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}