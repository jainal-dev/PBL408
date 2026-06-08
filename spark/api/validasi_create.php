<?php

header("Content-Type: application/json");

require_once "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

$laporan_id   = $data["laporan_id"]   ?? 0;
$validator_id = $data["validator_id"] ?? 0;
$status       = $data["status"]       ?? "";
$komentar     = trim($data["komentar"] ?? "");

if (!$laporan_id || !$validator_id || !in_array($status, ["setuju", "ditolak"])) {
    echo json_encode([
        "success" => false,
        "message" => "Data validasi tidak lengkap"
    ]);
    exit;
}

try {

    /* ambil pemilik laporan */
    $cek = $pdo->prepare("
        SELECT user_id
        FROM lapora_user
        WHERE laporan_id = ?
    ");
    $cek->execute([$laporan_id]);
    $laporan = $cek->fetch();

    if (!$laporan) {
        echo json_encode([
            "success" => false,
            "message" => "Laporan tidak ditemukan"
        ]);
        exit;
    }

    /* tidak boleh validasi laporan sendiri */
    if ($laporan["user_id"] == $validator_id) {
        echo json_encode([
            "success" => false,
            "message" => "Tidak bisa memvalidasi laporan sendiri"
        ]);
        exit;
    }

    /* tidak boleh validasi 2 kali */
    $dobel = $pdo->prepare("
        SELECT validasi_id
        FROM validasi
        WHERE laporan_id = ?
        AND validator_id = ?
    ");
    $dobel->execute([$laporan_id, $validator_id]);

    if ($dobel->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Kamu sudah memvalidasi laporan ini"
        ]);
        exit;
    }

    /* simpan (komentar opsional -> NULL kalau kosong) */
    $stmt = $pdo->prepare("
        INSERT INTO validasi
            (laporan_id, validator_id, status_validasi, komentar)
        VALUES
            (?, ?, ?, ?)
    ");
    $stmt->execute([
        $laporan_id,
        $validator_id,
        $status,
        $komentar === "" ? null : $komentar
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Validasi tersimpan"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
