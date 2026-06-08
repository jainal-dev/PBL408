<?php

header("Content-Type: application/json");

require_once "../db.php";

$user_id = $_GET["user_id"] ?? 0;

try {

    $stmt = $pdo->prepare("
        SELECT

            l.laporan_id,
            l.user_id,
            l.slot_id,
            l.kondisi_dilaporkan,
            l.waktu_laporan,
            l.deskripsi,
            l.sudah_edit,

            s.nomor_slot,

            p.parkir_id,
            p.lokasi

        FROM lapora_user l

        JOIN slot_parkir s
        ON l.slot_id = s.slot_id

        JOIN parkir p
        ON s.parkir_id = p.parkir_id

        WHERE l.user_id = ?

        ORDER BY l.waktu_laporan DESC
    ");

    $stmt->execute([$user_id]);

    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll()
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}