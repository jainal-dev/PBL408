<?php
/* Daftar semua slot + lokasi (untuk Manajemen Slot) */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

try {
    $rows = $pdo->query("
        SELECT s.slot_id, s.nomor_slot, s.status_slot,
               p.parkir_id, p.lokasi
        FROM slot_parkir s
        JOIN parkir p ON s.parkir_id = p.parkir_id
        ORDER BY p.lokasi, s.nomor_slot
    ")->fetchAll();

    echo json_encode(["success" => true, "data" => $rows]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
