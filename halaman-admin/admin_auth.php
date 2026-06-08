<?php
/* PENJAGA AREA ADMIN
   Sertakan di paling atas tiap halaman admin:
   <?php require_once __DIR__ . '/admin_auth.php'; ?>
   - Belum login        -> ke halaman login
   - Login tapi non-admin -> ke dashboard pengguna */

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/* Belum login ATAU bukan admin -> arahkan ke halaman login */
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
  header('Location: ../spark/login.php');
  exit;
}
