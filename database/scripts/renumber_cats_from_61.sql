-- ============================================================================
-- SCRIPT UPDATE NOMOR ID KUCING & RELASI TABEL (MULAI DARI ID 61)
-- Database: KucingMu
-- ============================================================================
-- Keterangan:
-- 1. Script ini menggeser seluruh ID kucing yang ada saat ini agar berurutan mulai dari 61 (61, 62, 63, ...).
-- 2. Mengupdate kolom foreign key `cat_id` pada:
--    - appointments
--    - medical_records
--    - ktam_cards
--    - cat_photos
-- 3. Mengupdate kolom `unique_code` pada tabel `cats` (format: 34.kcg.0061, dst).
-- 4. Menyetel AUTO_INCREMENT tabel `cats` ke nilai ID berikutnya agar input data baru otomatis melanjutkan (misal 62+).
-- ============================================================================

START TRANSACTION;

-- Nonaktifkan Foreign Key Check sementara
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Buat temporary table untuk pemetaan ID Lama -> ID Baru (Mulai 61)
DROP TEMPORARY TABLE IF EXISTS temp_cat_id_mapping;

CREATE TEMPORARY TABLE temp_cat_id_mapping AS
SELECT 
    c.id AS old_id,
    60 + ROW_NUMBER() OVER (ORDER BY c.id ASC) AS new_id,
    COALESCE(c.wilayah_code, '34') AS wilayah_code,
    c.unique_code AS old_unique_code,
    CONCAT(COALESCE(c.wilayah_code, '34'), '.kcg.', LPAD(60 + ROW_NUMBER() OVER (ORDER BY c.id ASC), 4, '0')) AS new_unique_code
FROM cats c;

-- 2. Geser ID ke nilai offset besar sementara untuk menghindari duplikasi key
UPDATE cats c
JOIN temp_cat_id_mapping m ON c.id = m.old_id
SET c.id = m.old_id + 1000000;

UPDATE appointments a
JOIN temp_cat_id_mapping m ON a.cat_id = m.old_id
SET a.cat_id = m.old_id + 1000000;

UPDATE medical_records mr
JOIN temp_cat_id_mapping m ON mr.cat_id = m.old_id
SET mr.cat_id = m.old_id + 1000000;

UPDATE ktam_cards kc
JOIN temp_cat_id_mapping m ON kc.cat_id = m.old_id
SET kc.cat_id = m.old_id + 1000000;

UPDATE cat_photos cp
JOIN temp_cat_id_mapping m ON cp.cat_id = m.old_id
SET cp.cat_id = m.old_id + 1000000;

-- 3. Update ke ID Baru yang definitif (mulai 61)
UPDATE cats c
JOIN temp_cat_id_mapping m ON c.id = (m.old_id + 1000000)
SET c.id = m.new_id,
    c.unique_code = m.new_unique_code;

UPDATE appointments a
JOIN temp_cat_id_mapping m ON a.cat_id = (m.old_id + 1000000)
SET a.cat_id = m.new_id;

UPDATE medical_records mr
JOIN temp_cat_id_mapping m ON mr.cat_id = (m.old_id + 1000000)
SET mr.cat_id = m.new_id;

UPDATE ktam_cards kc
JOIN temp_cat_id_mapping m ON kc.cat_id = (m.old_id + 1000000)
SET kc.cat_id = m.new_id,
    kc.ktam_number = CASE 
        WHEN kc.ktam_number = m.old_unique_code THEN m.new_unique_code 
        ELSE kc.ktam_number 
    END;

UPDATE cat_photos cp
JOIN temp_cat_id_mapping m ON cp.cat_id = (m.old_id + 1000000)
SET cp.cat_id = m.new_id;

-- 4. Set AUTO_INCREMENT pada tabel cats agar data baru berikutnya otomatis mulai setelah ID tertinggi
SET @next_id = (SELECT COALESCE(MAX(id) + 1, 61) FROM cats);
SET @sql = CONCAT('ALTER TABLE cats AUTO_INCREMENT = ', GREATEST(@next_id, 61));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Hapus temporary table
DROP TEMPORARY TABLE IF EXISTS temp_cat_id_mapping;

-- Aktifkan kembali Foreign Key Check
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

-- Verifikasi hasil
SELECT id, name, unique_code, created_at FROM cats ORDER BY id ASC;
