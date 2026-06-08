/* ============================================
   SETUP VALIDASI - auto_increment + default waktu
   Jalankan sekali via HeidiSQL / phpMyAdmin
   ============================================ */

USE spark;

/* 1. validasi_id jadi AUTO_INCREMENT */
ALTER TABLE validasi
  MODIFY COLUMN validasi_id BIGINT NOT NULL AUTO_INCREMENT;

/* 2. waktu_validasi default CURRENT_TIMESTAMP */
ALTER TABLE validasi
  MODIFY COLUMN waktu_validasi TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

/* Verifikasi:
   DESCRIBE validasi;
*/
