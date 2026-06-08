/* ============================================
   AUTO-HAPUS laporan (+validasinya) yang > 2 jam
   Pakai MySQL Event Scheduler (jalan tiap 10 menit)
   ============================================ */

USE spark;

/* pastikan scheduler aktif */
SET GLOBAL event_scheduler = ON;

DROP EVENT IF EXISTS ev_hapus_laporan_lama;

DELIMITER $$

CREATE EVENT ev_hapus_laporan_lama
ON SCHEDULE EVERY 10 MINUTE
COMMENT 'Hapus laporan + validasi yang lebih dari 2 jam'
DO
BEGIN

  /* hapus komentar/validasi milik laporan lama dulu (tidak ada FK cascade) */
  DELETE FROM validasi
  WHERE laporan_id IN (
    SELECT laporan_id
    FROM lapora_user
    WHERE waktu_laporan < (NOW() - INTERVAL 2 HOUR)
  );

  /* lalu hapus laporannya */
  DELETE FROM lapora_user
  WHERE waktu_laporan < (NOW() - INTERVAL 2 HOUR);

END$$

DELIMITER ;

/* Verifikasi:
   SHOW EVENTS;
*/
