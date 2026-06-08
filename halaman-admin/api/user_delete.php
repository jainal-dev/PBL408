<?php
/* Hapus pengguna beserta laporan & validasinya */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$d = json_decode(file_get_contents("php://input"), true);

$user_id = $d["user_id"] ?? 0;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "user_id wajib"]);
    exit;
}

try {
    $pdo->beginTransaction();

    /* validasi yang dibuat user ini */
    $pdo->prepare("DELETE FROM validasi WHERE validator_id = ?")->execute([$user_id]);

    /* validasi atas laporan milik user ini */
    $pdo->prepare("
        DELETE FROM validasi
        WHERE laporan_id IN (SELECT laporan_id FROM lapora_user WHERE user_id = ?)
    ")->execute([$user_id]);

    /* laporan milik user ini */
    $pdo->prepare("DELETE FROM lapora_user WHERE user_id = ?")->execute([$user_id]);

    /* user-nya */
    $pdo->prepare("DELETE FROM user WHERE user_id = ?")->execute([$user_id]);

    $pdo->commit();

    echo json_encode(["success" => true, "message" => "Pengguna dihapus"]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
