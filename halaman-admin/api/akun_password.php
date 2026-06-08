<?php
/* Ubah password admin yang sedang login (verifikasi password lama dulu) */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$d = json_decode(file_get_contents('php://input'), true);

$current = $d['current'] ?? '';
$baru    = $d['baru']    ?? '';
$user_id = $_SESSION['user']['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak valid']);
    exit;
}

if ($current === '' || $baru === '') {
    echo json_encode(['success' => false, 'message' => 'Password saat ini & password baru wajib diisi']);
    exit;
}

if (strlen($baru) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password baru minimal 6 karakter']);
    exit;
}

try {
    $st = $pdo->prepare('SELECT password FROM user WHERE user_id = ? LIMIT 1');
    $st->execute([$user_id]);
    $row = $st->fetch();

    if (!$row || !password_verify($current, $row['password'])) {
        echo json_encode(['success' => false, 'message' => 'Password saat ini salah']);
        exit;
    }

    $hash = password_hash($baru, PASSWORD_BCRYPT);
    $pdo->prepare('UPDATE user SET password = ? WHERE user_id = ?')->execute([$hash, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Password berhasil diperbarui']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
