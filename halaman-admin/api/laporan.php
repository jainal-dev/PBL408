<?php
/* MONITORING LAPORAN (admin): semua laporan pengguna + hasil validasi antar-user.
   Read-only. Filter opsional: ?lokasi=TA & ?kondisi=kosong */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$lokasi  = $_GET['lokasi']  ?? '';
$kondisi = $_GET['kondisi'] ?? '';

try {

    $sql = "
        SELECT
            l.laporan_id,
            u.nama  AS pelapor,
            p.lokasi,
            s.nomor_slot,
            l.kondisi_dilaporkan,
            l.deskripsi,
            l.waktu_laporan,
            COALESCE(SUM(v.status_validasi = 'setuju'),  0) AS setuju,
            COALESCE(SUM(v.status_validasi = 'ditolak'), 0) AS ditolak
        FROM lapora_user l
        JOIN user u        ON l.user_id = u.user_id
        JOIN slot_parkir s ON l.slot_id = s.slot_id
        JOIN parkir p      ON s.parkir_id = p.parkir_id
        LEFT JOIN validasi v ON v.laporan_id = l.laporan_id
    ";

    $where  = [];
    $params = [];

    if ($lokasi !== '') {
        $where[] = 'p.lokasi = :lokasi';
        $params[':lokasi'] = $lokasi;
    }

    if (in_array($kondisi, ['kosong', 'terisi', 'rusak'], true)) {
        $where[] = 'l.kondisi_dilaporkan = :kondisi';
        $params[':kondisi'] = $kondisi;
    }

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= '
        GROUP BY l.laporan_id
        ORDER BY l.waktu_laporan DESC
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    /* master lokasi (selalu ada walau belum ada laporan) */
    $lokasiList = $pdo->query("SELECT lokasi FROM parkir ORDER BY lokasi")
        ->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'data'    => $data,
        'lokasi'  => $lokasiList
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
