<?php

header("Content-Type: application/json");

require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

$laporan_id = $data["laporan_id"] ?? 0;
$user_id = $data["user_id"] ?? 0;

try {

    $cek = $pdo->prepare("
        SELECT sudah_edit
        FROM lapora_user
        WHERE laporan_id = ?
        AND user_id = ?
    ");

    $cek->execute([
        $laporan_id,
        $user_id
    ]);

    $laporan = $cek->fetch();

    if (!$laporan) {

        echo json_encode([
            "success" => false,
            "message" => "Laporan tidak ditemukan"
        ]);
        exit;
    }

    if ($laporan["sudah_edit"] == 1) {

        echo json_encode([
            "success" => false,
            "message" => "Laporan hanya bisa diedit 1 kali"
        ]);
        exit;
    }

    $update = $pdo->prepare("
        UPDATE lapora_user
        SET
            slot_id = ?,
            kondisi_dilaporkan = ?,
            deskripsi = ?,
            sudah_edit = 1
        WHERE laporan_id = ?
    ");

    $update->execute([
        $data["slot_id"],
        $data["kondisi"],
        $data["deskripsi"],
        $laporan_id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Laporan berhasil diperbarui"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}