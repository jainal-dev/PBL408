<?php
/* Daftar pengguna + jumlah laporan */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

try {
    $rows = $pdo->query("
        SELECT
            u.user_id, u.nama, u.email, u.role,
            (SELECT COUNT(*) FROM lapora_user l WHERE l.user_id = u.user_id) AS jml_laporan
        FROM user u
        ORDER BY u.user_id
    ")->fetchAll();

    echo json_encode(["success" => true, "data" => $rows]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
