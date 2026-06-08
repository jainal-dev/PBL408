<?php
/* Ubah role pengguna */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$d = json_decode(file_get_contents("php://input"), true);

$user_id = $d["user_id"] ?? 0;
$role    = $d["role"]    ?? "";

if (!$user_id || !in_array($role, ["admin", "pengguna"], true)) {
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
    exit;
}

try {
    $st = $pdo->prepare("UPDATE user SET role = ? WHERE user_id = ?");
    $st->execute([$role, $user_id]);

    echo json_encode(["success" => true, "message" => "Role diperbarui"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
