<?php
/* STATISTIK ADMIN: status slot, list laporan, grafik harian & mingguan */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

try {

    /* ---- STATUS SLOT per area ---- */
    $slot = $pdo->query("
        SELECT
            p.lokasi,
            SUM(s.status_slot = 'kosong') AS kosong,
            SUM(s.status_slot = 'terisi') AS terisi,
            SUM(s.status_slot = 'rusak')  AS rusak,
            COUNT(*) AS total
        FROM slot_parkir s
        JOIN parkir p ON s.parkir_id = p.parkir_id
        GROUP BY p.parkir_id, p.lokasi
        ORDER BY p.lokasi
    ")->fetchAll();

    /* ---- LIST LAPORAN terbaru ---- */
    $laporan = $pdo->query("
        SELECT
            l.laporan_id,
            u.nama AS pelapor,
            p.lokasi,
            s.nomor_slot,
            l.kondisi_dilaporkan,
            l.deskripsi,
            l.waktu_laporan
        FROM lapora_user l
        JOIN user u        ON l.user_id = u.user_id
        JOIN slot_parkir s ON l.slot_id = s.slot_id
        JOIN parkir p      ON s.parkir_id = p.parkir_id
        ORDER BY l.waktu_laporan DESC
        LIMIT 15
    ")->fetchAll();

    /* ---- HARIAN: jumlah laporan per jam (24 jam terakhir) ---- */
    $perJam = $pdo->query("
        SELECT HOUR(waktu_laporan) AS jam, COUNT(*) AS jumlah
        FROM lapora_user
        WHERE waktu_laporan >= (NOW() - INTERVAL 24 HOUR)
        GROUP BY HOUR(waktu_laporan)
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    $harian = [];
    for ($h = 0; $h < 24; $h++) {
        $harian[] = [
            "label"  => sprintf("%02d", $h),
            "jumlah" => (int)($perJam[$h] ?? 0)
        ];
    }

    /* ---- MINGGUAN: jumlah laporan per hari (7 hari terakhir) ---- */
    $perHari = $pdo->query("
        SELECT DATE(waktu_laporan) AS tgl, COUNT(*) AS jumlah
        FROM lapora_user
        WHERE waktu_laporan >= (CURDATE() - INTERVAL 6 DAY)
        GROUP BY DATE(waktu_laporan)
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    $namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $mingguan = [];
    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i day"));
        $dow = (int)date('w', strtotime($tgl));
        $mingguan[] = [
            "label"  => $namaHari[$dow],
            "jumlah" => (int)($perHari[$tgl] ?? 0)
        ];
    }

    echo json_encode([
        "success"  => true,
        "slot"     => $slot,
        "laporan"  => $laporan,
        "harian"   => $harian,
        "mingguan" => $mingguan
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
