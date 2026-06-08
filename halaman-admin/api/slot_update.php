<?php
/* Ubah status sebuah slot */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$d = json_decode(file_get_contents("php://input"), true);

$slot_id = $d["slot_id"] ?? 0;
$status  = $d["status"]  ?? "";

if (!$slot_id || !in_array($status, ["kosong", "terisi", "rusak"], true)) {
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
    exit;
}

try {
    $st = $pdo->prepare("UPDATE slot_parkir SET status_slot = ? WHERE slot_id = ?");
    $st->execute([$status, $slot_id]);

    echo json_encode(["success" => true, "message" => "Status slot diperbarui"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
