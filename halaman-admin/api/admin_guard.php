<?php
/* PENJAGA API ADMIN
   Sertakan di paling atas tiap API admin:
   require_once __DIR__ . '/admin_guard.php';
   Hanya session dengan role 'admin' yang boleh lanjut; selain itu 403. */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode([
    "success" => false,
    "message" => "Akses ditolak: khusus admin"
  ]);
  exit;
}
