<?php
/* PENJAGA HALAMAN
   Sertakan di paling atas halaman yang butuh login:
   <?php require_once __DIR__ . '/auth.php'; ?>
   Kalau belum login -> dilempar ke halaman login. */

session_start();

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}
