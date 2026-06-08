/* ============================================
   SETUP LAPORAN - Schema fix + Seed
   Jalankan sekali via HeidiSQL / phpMyAdmin
   ============================================ */

USE spark;

/* 1. FIX: laporan_id jadi AUTO_INCREMENT */
ALTER TABLE lapora_user
  MODIFY COLUMN laporan_id BIGINT NOT NULL AUTO_INCREMENT;

/* 2. FIX: typo enum 'manunggu' -> 'menunggu', default 'menunggu' */
ALTER TABLE lapora_user
  MODIFY COLUMN status_laporan ENUM('setuju','menunggu','ditolak')
  NOT NULL DEFAULT 'menunggu';

/* 3. TAMBAH: kolom kondisi yang dilaporkan user */
ALTER TABLE lapora_user
  ADD COLUMN kondisi_dilaporkan ENUM('kosong','terisi','rusak') NOT NULL
  AFTER slot_id;

/* 4. FIX: waktu_laporan default CURRENT_TIMESTAMP */
ALTER TABLE lapora_user
  MODIFY COLUMN waktu_laporan TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

/* 5. SEED parkir (4 area) */
INSERT INTO parkir (parkir_id, lokasi, kapasitas, status_parkir) VALUES
  (1, 'TA',     10, 'terisi'),
  (2, 'GU',     10, 'terisi'),
  (3, 'Techno', 10, 'terisi'),
  (4, 'RTM',    10, 'terisi');

/* 6. SEED slot_parkir (4 parkir x 10 slot = 40 slot) */
INSERT INTO slot_parkir (slot_id, parkir_id, status_slot, nomor_slot) VALUES
  (1, 1, 'kosong', 1),  (2, 1, 'kosong', 2),  (3, 1, 'kosong', 3),
  (4, 1, 'kosong', 4),  (5, 1, 'kosong', 5),  (6, 1, 'kosong', 6),
  (7, 1, 'kosong', 7),  (8, 1, 'kosong', 8),  (9, 1, 'kosong', 9),
  (10,1, 'kosong', 10),
  (11,2, 'kosong', 1),  (12,2, 'kosong', 2),  (13,2, 'kosong', 3),
  (14,2, 'kosong', 4),  (15,2, 'kosong', 5),  (16,2, 'kosong', 6),
  (17,2, 'kosong', 7),  (18,2, 'kosong', 8),  (19,2, 'kosong', 9),
  (20,2, 'kosong', 10),
  (21,3, 'kosong', 1),  (22,3, 'kosong', 2),  (23,3, 'kosong', 3),
  (24,3, 'kosong', 4),  (25,3, 'kosong', 5),  (26,3, 'kosong', 6),
  (27,3, 'kosong', 7),  (28,3, 'kosong', 8),  (29,3, 'kosong', 9),
  (30,3, 'kosong', 10),
  (31,4, 'kosong', 1),  (32,4, 'kosong', 2),  (33,4, 'kosong', 3),
  (34,4, 'kosong', 4),  (35,4, 'kosong', 5),  (36,4, 'kosong', 6),
  (37,4, 'kosong', 7),  (38,4, 'kosong', 8),  (39,4, 'kosong', 9),
  (40,4, 'kosong', 10);

/* Selesai. Verifikasi:
   SELECT * FROM parkir;
   SELECT COUNT(*) FROM slot_parkir;
   DESCRIBE lapora_user;
*/
