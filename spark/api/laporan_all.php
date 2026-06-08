<?php

header("Content-Type: application/json");

require_once "../db.php";

/* opsional: filter area & sembunyikan laporan milik viewer sendiri */
$lokasi  = $_GET["lokasi"]  ?? "";
$user_id = $_GET["user_id"] ?? 0;

try {

    $sql = "
        SELECT

            l.laporan_id,
            l.user_id,
            l.slot_id,
            l.kondisi_dilaporkan,
            l.waktu_laporan,
            l.deskripsi,

            u.nama AS pelapor,

            s.nomor_slot,

            p.parkir_id,
            p.lokasi,

            COALESCE(SUM(v.status_validasi = 'setuju'),  0) AS setuju,
            COALESCE(SUM(v.status_validasi = 'ditolak'), 0) AS ditolak,
            MAX(v.validator_id = :viewer)                   AS sudah_validasi,

            (
                SELECT JSON_ARRAYAGG(JSON_OBJECT(
                    'nama',     cu.nama,
                    'status',   cv.status_validasi,
                    'komentar', cv.komentar,
                    'waktu',    cv.waktu_validasi
                ))
                FROM validasi cv
                JOIN user cu ON cv.validator_id = cu.user_id
                WHERE cv.laporan_id = l.laporan_id
                  AND cv.komentar IS NOT NULL
                  AND cv.komentar <> ''
            ) AS komentar_json

        FROM lapora_user l

        JOIN user u
            ON l.user_id = u.user_id

        JOIN slot_parkir s
            ON l.slot_id = s.slot_id

        JOIN parkir p
            ON s.parkir_id = p.parkir_id

        LEFT JOIN validasi v
            ON v.laporan_id = l.laporan_id
    ";

    $params = [":viewer" => $user_id];

    /* real-time: hanya laporan <= 2 jam terakhir (data lama tetap tersimpan, hanya disembunyikan) */
    $sql .= " WHERE l.waktu_laporan >= (NOW() - INTERVAL 2 HOUR) ";

    if ($lokasi !== "") {
        $sql .= " AND p.lokasi = :lokasi ";
        $params[":lokasi"] = $lokasi;
    }

    $sql .= "
        GROUP BY l.laporan_id
        ORDER BY l.waktu_laporan DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll();

    /* ubah komentar_json (string) jadi array komentar */
    foreach ($rows as &$r) {
        $r["komentar"] = $r["komentar_json"]
            ? json_decode($r["komentar_json"])
            : [];
        unset($r["komentar_json"]);
    }
    unset($r);

    echo json_encode([
        "success" => true,
        "data" => $rows
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
