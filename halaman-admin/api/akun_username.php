<?php
/* Ubah username (user.nama) admin yang sedang login */

require_once __DIR__ . '/admin_guard.php';

header('Content-Type: application/json');

require_once __DIR__ . '/../../spark/db.php';

$d = json_decode(file_get_contents('php://input'), true);

$username = trim($d['username'] ?? '');
$user_id  = $_SESSION['user']['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Sesi tidak valid']);
    exit;
}

if ($username === '') {
    echo json_encode(['success' => false, 'message' => 'Username baru wajib diisi']);
    exit;
}

if (mb_strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Username minimal 3 karakter']);
    exit;
}

try {
    $st = $pdo->prepare('UPDATE user SET nama = ? WHERE user_id = ?');
    $st->execute([$username, $user_id]);

    /* sinkronkan session */
    $_SESSION['user']['nama'] = $username;

    echo json_encode([
        'success'  => true,
        'message'  => 'Username berhasil diperbarui',
        'username' => $username
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
