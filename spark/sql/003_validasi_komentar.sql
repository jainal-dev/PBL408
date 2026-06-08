/* ============================================
   Tambah kolom komentar pada validasi
   Jalankan sekali via HeidiSQL / phpMyAdmin
   ============================================ */

USE spark;

ALTER TABLE validasi
  ADD COLUMN komentar TEXT NULL AFTER status_validasi;

/* Verifikasi:
   DESCRIBE validasi;
*/
