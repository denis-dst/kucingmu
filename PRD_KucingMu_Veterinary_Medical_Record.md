# PRD --- KucingMu Veterinary Medical Record

**Versi:** 1.0\
**Status:** Draft\
**Platform:** Doctor Web Dashboard, KucingMu Mobile App, Admin/Clinic
Dashboard\
**Backend:** Laravel API\
**Database:** PostgreSQL\
**Model layanan:** Telekonsultasi online dan pemeriksaan klinik\
**Standar catatan klinis:** SOAP

------------------------------------------------------------------------

## 1. Ringkasan Produk

KucingMu Veterinary Medical Record adalah modul rekam medis untuk
mendokumentasikan pemeriksaan kucing secara terstruktur, mulai dari
anamnesa, pemeriksaan objektif, assessment, diagnosis, rencana
penanganan, resep elektronik, instruksi perawatan, hingga tindak lanjut.

Modul mendukung dua jenis layanan:

-   **Telekonsultasi online:** dokter mengevaluasi informasi, gejala,
    foto/video, dan hasil pemeriksaan yang tersedia secara jarak jauh.
-   **Pemeriksaan klinik:** dokter melakukan pemeriksaan fisik langsung
    dan mencatat hasil pengukuran atau pemeriksaan penunjang.

Keduanya menggunakan struktur SOAP yang sama, tetapi sumber data dan
jenis pemeriksaan yang tersedia dapat berbeda.

### 1.1 Keputusan Produk

  -----------------------------------------------------------------------
  Aspek                               Keputusan
  ----------------------------------- -----------------------------------
  Platform dokter                     Web responsif

  Platform member                     Mobile app KucingMu

  Model layanan                       Online dan klinik

  Standar catatan                     SOAP

  Workflow input                      Stepper dengan ringkasan satu
                                      halaman

  Akses member                        Ringkasan medis, resep, dan
                                      instruksi yang diterbitkan

  Resep                               Terstruktur

  Backend                             Laravel API

  Database                            PostgreSQL

  Audit                               Perubahan rekam medis terlacak
  -----------------------------------------------------------------------

## 2. Latar Belakang dan Masalah

Pencatatan pemeriksaan yang hanya mengandalkan teks bebas berpotensi
membuat informasi keluhan, temuan klinis, diagnosis, terapi, dan
instruksi sulit ditelusuri secara konsisten. Member juga membutuhkan
akses yang jelas terhadap hasil pemeriksaan, resep, dan instruksi
perawatan setelah konsultasi.

KucingMu membutuhkan modul yang:

1.  Menstandarkan pencatatan pemeriksaan dokter.
2.  Memisahkan keluhan pemilik dari temuan objektif dan keputusan klinis
    dokter.
3.  Menghasilkan resep yang konsisten, terbaca, dan dapat diakses
    member.
4.  Menyediakan ringkasan medis dan instruksi pascakonsultasi.
5.  Menyimpan riwayat medis setiap kucing dari pemeriksaan ke
    pemeriksaan.
6.  Mendukung kontrol lanjutan untuk layanan online maupun klinik.

## 3. Tujuan dan Indikator Keberhasilan

### 3.1 Tujuan

-   Memungkinkan dokter membuat, menyimpan, meninjau, dan memfinalisasi
    rekam medis berbasis SOAP.
-   Memungkinkan dokter menerbitkan resep terstruktur dan instruksi
    perawatan.
-   Memberikan member akses ke ringkasan medis yang telah diterbitkan.
-   Menjaga integritas catatan setelah finalisasi melalui audit dan
    amendemen.
-   Menghubungkan pemeriksaan dengan pasien, member, dokter, layanan,
    dan tindak lanjut.

### 3.2 Indikator Keberhasilan

Indikator awal yang dapat dipantau setelah rilis:

-   Persentase pemeriksaan yang diselesaikan menggunakan SOAP
    terstruktur.
-   Persentase resep yang memiliki data obat, dosis, satuan, rute,
    frekuensi, dan durasi yang relevan.
-   Persentase pemeriksaan final yang memiliki ringkasan member.
-   Tingkat kegagalan penyimpanan draft atau finalisasi.
-   Jumlah insiden akses data yang tidak sah.
-   Jumlah koreksi atau amendemen rekam medis.
-   Waktu rata-rata dokter untuk melengkapi satu rekam medis.

Target numerik ditetapkan setelah baseline dan uji coba bersama dokter.

## 4. Ruang Lingkup

### 4.1 Termasuk dalam Versi Pertama (P0)

-   Pembuatan pemeriksaan dan pemilihan pasien.
-   Penentuan jenis layanan online atau klinik.
-   Anamnesa dan klarifikasi dokter.
-   Pemeriksaan objektif terstruktur.
-   Assessment dan diagnosis.
-   Rencana penanganan.
-   Resep elektronik terstruktur.
-   Instruksi perawatan dan tanda bahaya.
-   Draft, finalisasi, pembatalan, dan amendemen.
-   Ringkasan medis untuk member.
-   Riwayat pemeriksaan per kucing.
-   Audit trail untuk tindakan penting.
-   Kontrol akses berbasis peran dan relasi.

### 4.2 Pengembangan Lanjutan (P1)

-   Lampiran foto, video, dan hasil laboratorium.
-   Pengingat obat dan kontrol.
-   Master obat yang lebih lengkap.
-   Template pemeriksaan berdasarkan jenis keluhan.
-   Perbandingan parameter kesehatan dari waktu ke waktu.
-   Integrasi dengan klinik atau apotek mitra.

### 4.3 Di Luar Ruang Lingkup Awal

-   AI yang menetapkan diagnosis secara otomatis.
-   Manajemen stok dan penjualan obat.
-   Pembayaran atau klaim asuransi.
-   Integrasi laboratorium otomatis.
-   Pemantauan berbasis perangkat medis.
-   Analitik epidemiologi lanjutan.

## 5. Aktor dan Hak Akses

  -----------------------------------------------------------------------
  Aktor                               Hak akses
  ----------------------------------- -----------------------------------
  Member/pemilik                      Mengisi keluhan, melihat status
                                      konsultasi, ringkasan medis, resep,
                                      dan instruksi yang diterbitkan

  Dokter hewan                        Mengakses pemeriksaan sesuai
                                      penugasan/kewenangan, mengisi SOAP,
                                      diagnosis, resep, dan finalisasi

  Admin klinik                        Mengelola jadwal, data layanan, dan
                                      penugasan dokter sesuai kewenangan

  Admin KucingMu                      Mengelola master data dan
                                      konfigurasi; akses data medis
                                      dibatasi sesuai kebutuhan
                                      operasional

  Apoteker/mitra apotek (fase         Mengakses resep yang relevan sesuai
  berikutnya)                         izin dan integrasi
  -----------------------------------------------------------------------

### 5.1 Aturan Akses

-   Member hanya dapat melihat data kucing yang dimiliki atau yang
    secara sah berada dalam aksesnya.
-   Dokter hanya dapat membuka rekam medis sesuai penugasan atau
    kewenangannya.
-   Catatan internal dokter tidak otomatis ditampilkan kepada member.
-   Akses harus divalidasi di backend pada setiap request.
-   Data yang ditampilkan untuk member harus berasal dari
    service/resource khusus, bukan seluruh objek rekam medis.

## 6. Alur Layanan

### 6.1 Alur Umum

1.  Member memilih kucing.
2.  Member memilih layanan online atau klinik.
3.  Member mengisi keluhan dan riwayat awal.
4.  Member mengirim permintaan konsultasi atau melakukan kunjungan.
5.  Sistem menjadwalkan atau menugaskan dokter.
6.  Dokter membuka pemeriksaan.
7.  Dokter melengkapi SOAP melalui stepper.
8.  Dokter meninjau seluruh catatan pada halaman review.
9.  Dokter memvalidasi diagnosis, rencana, resep, dan instruksi.
10. Dokter memfinalisasi rekam medis.
11. Sistem menerbitkan ringkasan medis, resep, dan instruksi yang
    diizinkan.
12. Member menerima notifikasi dan dapat melihat hasilnya.
13. Jika diperlukan, dibuat rencana kontrol atau pemeriksaan lanjutan.

### 6.2 Alur Telekonsultasi Online

1.  Member memilih pasien dan mengisi keluhan.
2.  Member melampirkan foto/video bila diperlukan.
3.  Dokter meninjau anamnesa awal.
4.  Dokter mengonfirmasi keluhan dan melakukan konsultasi.
5.  Dokter mencatat hasil observasi yang benar-benar tersedia.
6.  Jika pemeriksaan fisik atau tes langsung diperlukan, dokter mencatat
    keterbatasan telekonsultasi dan menyarankan pemeriksaan klinik.
7.  Dokter melengkapi assessment, plan, resep bila sesuai, dan
    instruksi.
8.  Dokter memfinalisasi catatan dan menerbitkan ringkasan.

### 6.3 Alur Pemeriksaan Klinik

1.  Member melakukan pemesanan atau datang ke klinik.
2.  Petugas menghubungkan kunjungan dengan profil kucing.
3.  Dokter meninjau keluhan dan riwayat sebelumnya.
4.  Dokter mencatat hasil pemeriksaan fisik dan penunjang.
5.  Dokter menetapkan assessment, diagnosis, dan plan.
6.  Resep serta instruksi diterbitkan setelah diverifikasi.
7.  Ringkasan pemeriksaan tersedia untuk member.

## 7. Workflow SOAP

Workflow menggunakan stepper dengan lima bagian: Subjective, Objective,
Assessment, Plan, dan Review/Finalisasi. Dokter juga dapat membuka
ringkasan semua bagian pada satu halaman.

### 7.1 Step 1 --- Subjective (Anamnesa)

Data yang dapat dicatat:

-   Keluhan utama dari member.
-   Durasi dan perkembangan gejala.
-   Nafsu makan dan minum.
-   Buang air kecil dan buang air besar.
-   Perubahan perilaku atau aktivitas.
-   Riwayat penyakit.
-   Riwayat obat.
-   Riwayat alergi yang diketahui.
-   Riwayat vaksinasi.
-   Foto/video dan dokumen pendukung.
-   Klarifikasi dokter selama konsultasi.

**Aturan:** keluhan asli member tetap disimpan sebagai data sumber.
Klarifikasi dokter dicatat terpisah. Jangan mengubah laporan member
menjadi temuan dokter tanpa penandaan sumber.

### 7.2 Step 2 --- Objective

Data yang dapat dicatat:

-   Berat badan.
-   Suhu tubuh.
-   Denyut jantung.
-   Frekuensi napas.
-   Parameter fisik lain yang relevan.
-   Pemeriksaan fisik per sistem tubuh.
-   Temuan observasi.
-   Hasil tes penunjang.
-   Waktu pengukuran dan sumber pemeriksaan.

Setiap parameter dapat memiliki status:

-   Diperiksa.
-   Tidak diperiksa.
-   Tidak tersedia.

Temuan yang dilaporkan pemilik harus dibedakan dari hasil pemeriksaan
dokter atau hasil eksternal.

### 7.3 Step 3 --- Assessment

Data yang dapat dicatat:

-   Diagnosis utama.
-   Diagnosis tambahan.
-   Diagnosis banding.
-   Tingkat kepastian diagnosis.
-   Tingkat keparahan bila relevan.
-   Pertimbangan klinis.
-   Kebutuhan pemeriksaan lanjutan.

Diagnosis belum tentu dapat dipastikan pada konsultasi pertama. Sistem
harus mendukung status dugaan atau provisional tanpa memaksa diagnosis
definitif.

### 7.4 Step 4 --- Plan

Data yang dapat dicatat:

-   Tindakan medis.
-   Terapi.
-   Pemeriksaan lanjutan.
-   Resep elektronik.
-   Instruksi perawatan di rumah.
-   Nutrisi atau aktivitas bila relevan.
-   Tanda bahaya.
-   Jadwal kontrol.
-   Rujukan.

### 7.5 Step 5 --- Review dan Finalisasi

Halaman review harus:

-   Menampilkan seluruh isi SOAP.
-   Menampilkan resep, instruksi, dan rencana kontrol.
-   Menunjukkan bagian yang belum lengkap.
-   Memisahkan catatan internal dari informasi untuk member.
-   Meminta konfirmasi dokter sebelum finalisasi.
-   Menyimpan waktu dan identitas dokter yang memfinalisasi.

## 8. Resep Elektronik

### 8.1 Data Resep

Header resep:

-   Nomor resep.
-   Rekam medis terkait.
-   Dokter penerbit.
-   Status resep.
-   Waktu penerbitan.
-   Waktu pembatalan jika ada.
-   Alasan pembatalan.
-   Referensi resep pengganti jika ada.

Item resep:

-   Obat dari master obat atau nama obat khusus.
-   Bahan aktif jika diketahui.
-   Bentuk sediaan.
-   Konsentrasi dan satuan.
-   Nilai dosis dan satuan.
-   Basis dosis: tetap, per berat badan, atau lainnya.
-   Rute pemberian.
-   Frekuensi.
-   Durasi.
-   Jumlah obat dan satuan.
-   Instruksi penggunaan.
-   Peringatan khusus.

### 8.2 Aturan Resep

-   Satu pemeriksaan dapat memiliki beberapa resep atau item obat.
-   Resep yang telah diterbitkan tidak boleh diubah diam-diam.
-   Koreksi dilakukan dengan membatalkan atau mengganti resep, disertai
    alasan dan jejak audit.
-   Member hanya melihat resep yang diterbitkan dan diizinkan untuk
    diakses.
-   Obat yang belum tersedia di master dapat memerlukan input khusus dan
    validasi tambahan.
-   Perhitungan dosis harus menggunakan satuan yang konsisten dan
    ditinjau dokter.
-   Sistem tidak boleh menebak nilai dosis atau menggunakan aturan dosis
    manusia secara otomatis untuk hewan.

## 9. Ringkasan Medis untuk Member

Ringkasan member merupakan tampilan terpisah dari workspace dokter.

### 9.1 Konten yang Dapat Ditampilkan

-   Identitas kucing dan tanggal pemeriksaan.
-   Jenis layanan.
-   Ringkasan keluhan.
-   Temuan pemeriksaan yang dipilih untuk diterbitkan.
-   Diagnosis yang diterbitkan dokter.
-   Resep yang diterbitkan.
-   Instruksi perawatan.
-   Tanda bahaya dan kapan mencari pertolongan.
-   Jadwal kontrol atau tindak lanjut.
-   Lampiran yang diizinkan.

### 9.2 Konten Internal

Secara default, hal berikut tidak ditampilkan kepada member:

-   Catatan internal dokter.
-   Catatan audit dan perubahan.
-   Data administratif internal.
-   Data lain yang tidak disetujui untuk ringkasan member.

Jika catatan atau resep yang sudah diterbitkan berubah melalui
amendemen, sistem perlu memperbarui ringkasan dan memberi tahu member
apabila perubahan berdampak pada informasi yang sebelumnya diterbitkan.

## 10. Status dan Aturan Bisnis

### 10.1 Status Rekam Medis

  Status             Makna
  ------------------ ------------------------------------------------------------
  `draft`            Catatan baru dibuat dan dapat diedit oleh dokter berwenang
  `in_progress`      Pemeriksaan sedang berlangsung
  `awaiting_tests`   Menunggu pemeriksaan tambahan
  `completed`        Rekam medis telah difinalisasi
  `cancelled`        Pemeriksaan dibatalkan dengan alasan tercatat

Status `completed` berarti catatan telah difinalisasi, bukan berarti
kucing sudah sembuh.

Status kontrol terjadwal sebaiknya dikelola sebagai tindak lanjut, bukan
membuka kembali rekam medis yang telah selesai.

### 10.2 Validasi Finalisasi

Sebelum finalisasi, sistem harus memvalidasi:

-   Identitas pasien, dokter, dan jenis layanan tersedia.
-   Anamnesa sudah ditinjau.
-   Status pemeriksaan objektif dicatat, termasuk parameter yang tidak
    diperiksa.
-   Assessment relevan atau alasan diagnosis belum dapat dipastikan
    tersedia.
-   Plan sudah diisi, atau dokter mencatat alasan tidak ada
    tindakan/terapi yang direkomendasikan.
-   Resep yang akan diterbitkan sudah divalidasi.
-   Instruksi dan jadwal kontrol ditinjau jika relevan.
-   Dokter mengonfirmasi ringkasan yang akan dilihat member.

Pemeriksaan tambahan yang belum selesai tidak harus menghalangi
pencatatan awal, selama status dan rencana tindak lanjut dinyatakan
jelas.

### 10.3 Amendemen

Jika terdapat kesalahan setelah finalisasi:

1.  Dokter membuka rekam medis yang selesai.
2.  Memilih **Buat Amendemen**.
3.  Mengisi alasan koreksi.
4.  Memperbarui informasi yang diperbolehkan.
5.  Sistem menyimpan versi sebelumnya dan versi terbaru.
6.  Jika perubahan berdampak pada resep, resep lama dibatalkan atau
    ditandai digantikan.
7.  Member menerima pembaruan ringkasan jika perubahan memengaruhi
    informasi yang telah diterbitkan.

Catatan final tidak boleh diedit atau dihapus tanpa jejak perubahan.

## 11. Rancangan Database PostgreSQL

Rancangan berikut bersifat logis dan perlu disesuaikan dengan tabel
pasien, member, dokter, jadwal, dan layanan yang telah ada di KucingMu.

### 11.1 Entitas Utama

#### `medical_records`

Header pemeriksaan.

  Kolom                        Tipe/Aturan
  ---------------------------- ----------------------------------------------------------
  `id`                         UUID, PK
  `record_number`              VARCHAR, unique
  `cat_id`                     FK ke pasien kucing
  `member_id`                  FK ke member/pemilik
  `doctor_id`                  FK ke dokter
  `clinic_id`                  FK nullable
  `service_type`               `online` / `clinic`
  `appointment_id`             FK nullable
  `status`                     Draft, in progress, awaiting tests, completed, cancelled
  `chief_complaint`            TEXT, snapshot keluhan utama
  `started_at`                 TIMESTAMPTZ
  `completed_at`               TIMESTAMPTZ nullable
  `created_by`                 FK pengguna
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `medical_record_subjectives`

Anamnesa dan klarifikasi.

  Kolom                    Tipe/Aturan
  ------------------------ ----------------------
  `id`                     UUID, PK
  `medical_record_id`      FK unique
  `member_complaint`       TEXT
  `symptom_onset`          TIMESTAMPTZ nullable
  `symptom_history`        TEXT nullable
  `appetite_history`       TEXT nullable
  `drinking_history`       TEXT nullable
  `urination_history`      TEXT nullable
  `defecation_history`     TEXT nullable
  `medication_history`     TEXT nullable
  `allergy_history`        TEXT nullable
  `vaccination_history`    TEXT nullable
  `doctor_clarification`   TEXT nullable
  `additional_notes`       TEXT nullable

#### `medical_record_objectives`

Pemeriksaan fisik dan pengukuran.

  Kolom                  Tipe/Aturan
  ---------------------- -----------------------------------------
  `id`                   UUID, PK
  `medical_record_id`    FK
  `parameter_code`       VARCHAR
  `parameter_name`       VARCHAR
  `value_numeric`        NUMERIC nullable
  `value_text`           TEXT nullable
  `unit`                 VARCHAR nullable
  `examination_status`   Performed, not performed, unavailable
  `finding`              TEXT nullable
  `source_type`          Doctor, owner-reported, external-result
  `observed_at`          TIMESTAMPTZ nullable
  `notes`                TEXT nullable

#### `medical_record_diagnoses`

Diagnosis dan penilaian klinis.

  Kolom                  Tipe/Aturan
  ---------------------- -----------------------------------
  `id`                   UUID, PK
  `medical_record_id`    FK
  `diagnosis_code`       VARCHAR nullable
  `diagnosis_name`       VARCHAR
  `diagnosis_type`       Primary, secondary, differential
  `certainty`            Suspected, provisional, confirmed
  `severity`             VARCHAR nullable
  `clinical_reasoning`   TEXT nullable
  `is_primary`           BOOLEAN
  `created_at`           TIMESTAMPTZ

#### `medical_record_plans`

Terapi, tindakan, dan rencana pemeriksaan.

  Kolom                 Tipe/Aturan
  --------------------- --------------------------------------------
  `id`                  UUID, PK
  `medical_record_id`   FK
  `plan_type`           Treatment, procedure, test, referral
  `description`         TEXT
  `priority`            VARCHAR nullable
  `planned_at`          TIMESTAMPTZ nullable
  `status`              Planned, in progress, completed, cancelled
  `notes`               TEXT nullable

#### `prescriptions`

Header resep.

  Kolom                        Tipe/Aturan
  ---------------------------- --------------------------------------
  `id`                         UUID, PK
  `prescription_number`        VARCHAR, unique
  `medical_record_id`          FK
  `prescribed_by`              FK dokter
  `status`                     Draft, issued, cancelled, superseded
  `issued_at`                  TIMESTAMPTZ nullable
  `cancelled_at`               TIMESTAMPTZ nullable
  `cancellation_reason`        TEXT nullable
  `replaces_prescription_id`   FK nullable
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `prescription_items`

Detail obat.

  Kolom                        Tipe/Aturan
  ---------------------------- ----------------------------
  `id`                         UUID, PK
  `prescription_id`            FK
  `medicine_id`                FK nullable ke master obat
  `medicine_name_snapshot`     VARCHAR
  `active_ingredient`          VARCHAR nullable
  `dosage_form`                VARCHAR
  `concentration_value`        NUMERIC nullable
  `concentration_unit`         VARCHAR nullable
  `dose_value`                 NUMERIC nullable
  `dose_unit`                  VARCHAR nullable
  `dose_basis`                 Fixed, per_kg, other
  `administration_route`       VARCHAR
  `frequency_value`            NUMERIC nullable
  `frequency_unit`             VARCHAR nullable
  `duration_value`             NUMERIC nullable
  `duration_unit`              VARCHAR nullable
  `quantity_value`             NUMERIC nullable
  `quantity_unit`              VARCHAR nullable
  `usage_instructions`         TEXT
  `warnings`                   TEXT nullable
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `medicines`

Master obat, bukan stok apotek.

  Kolom                        Tipe/Aturan
  ---------------------------- ---------------------------
  `id`                         UUID, PK
  `name`                       VARCHAR
  `active_ingredient`          VARCHAR nullable
  `dosage_form`                VARCHAR
  `concentration_value`        NUMERIC nullable
  `concentration_unit`         VARCHAR nullable
  `species_scope`              JSONB atau relasi spesies
  `prescription_required`      BOOLEAN
  `is_active`                  BOOLEAN
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `medical_record_advice`

Instruksi perawatan.

  Kolom                        Tipe/Aturan
  ---------------------------- -----------------------------------------------------
  `id`                         UUID, PK
  `medical_record_id`          FK
  `category`                   Home_care, nutrition, warning_sign, activity, other
  `title`                      VARCHAR
  `instruction`                TEXT
  `urgency`                    Routine, prompt, urgent
  `visible_to_member`          BOOLEAN, default false
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `medical_record_followups`

Rencana kontrol.

  Kolom                        Tipe/Aturan
  ---------------------------- ---------------------------------------
  `id`                         UUID, PK
  `medical_record_id`          FK
  `followup_type`              Clinic, online, test, referral
  `scheduled_at`               TIMESTAMPTZ nullable
  `reason`                     TEXT
  `status`                     Planned, booked, completed, cancelled
  `followup_record_id`         FK nullable ke pemeriksaan berikutnya
  `outcome_notes`              TEXT nullable
  `created_at`, `updated_at`   TIMESTAMPTZ

#### `medical_record_attachments`

Lampiran medis.

  Kolom                 Tipe/Aturan
  --------------------- ---------------------------------------------
  `id`                  UUID, PK
  `medical_record_id`   FK
  `uploaded_by`         FK pengguna
  `category`            Photo, video, lab_result, imaging, document
  `storage_key`         VARCHAR, private object path
  `original_filename`   VARCHAR
  `mime_type`           VARCHAR
  `file_size_bytes`     BIGINT
  `captured_at`         TIMESTAMPTZ nullable
  `visibility`          Internal, member_summary
  `created_at`          TIMESTAMPTZ

#### `medical_record_audit_logs`

Audit aktivitas dan koreksi.

  Kolom                 Tipe/Aturan
  --------------------- ----------------
  `id`                  UUID, PK
  `medical_record_id`   FK
  `actor_id`            FK pengguna
  `action`              VARCHAR
  `entity_type`         VARCHAR
  `entity_id`           UUID nullable
  `reason`              TEXT nullable
  `changes`             JSONB nullable
  `request_id`          UUID nullable
  `occurred_at`         TIMESTAMPTZ

### 11.2 Relasi Utama

-   Member memiliki satu atau lebih kucing.
-   Kucing memiliki banyak rekam medis.
-   Dokter menangani banyak rekam medis.
-   Rekam medis memiliki satu catatan subjective dan banyak objective,
    diagnosis, plan, instruksi, follow-up, lampiran, serta audit log.
-   Rekam medis dapat memiliki banyak resep.
-   Resep memiliki satu atau lebih item resep.
-   Pemeriksaan lanjutan dapat dihubungkan ke rekam medis berikutnya.

### 11.3 Pertimbangan Database

-   Gunakan UUID untuk primary key bila konsisten dengan standar
    database yang ada.
-   Gunakan foreign key dan indeks untuk kolom relasi serta pencarian
    yang sering dilakukan.
-   Simpan pengukuran historis tanpa menimpa nilai dari pemeriksaan
    sebelumnya.
-   Simpan snapshot nama obat, konsentrasi, dan instruksi pada item
    resep.
-   Hindari penghapusan fisik rekam medis yang telah difinalisasi.
-   Gunakan `TIMESTAMPTZ` untuk waktu kejadian.
-   Terapkan constraint untuk nilai yang harus berada dalam rentang atau
    enum yang disepakati.
-   Pastikan perubahan audit tidak dapat dihapus atau dimodifikasi
    melalui alur aplikasi biasa.

## 12. Arsitektur Sistem

Komponen utama:

1.  **KucingMu Mobile App:** pengisian keluhan, melihat status,
    ringkasan medis, resep, dan instruksi.
2.  **Doctor Web Dashboard:** mengelola pemeriksaan, mengisi SOAP,
    membuat resep, dan finalisasi.
3.  **Admin/Clinic Dashboard:** mengelola jadwal, layanan, dan
    penugasan.
4.  **Laravel API:** autentikasi, otorisasi, workflow, validasi, resep,
    ringkasan, dan audit.
5.  **PostgreSQL:** penyimpanan data rekam medis, resep, tindak lanjut,
    dan audit.
6.  **Private File Storage:** penyimpanan lampiran medis dengan akses
    terbatas.

### 12.1 Prinsip Arsitektur

-   Perubahan status dan finalisasi dilakukan melalui backend.
-   Member tidak dapat menentukan sendiri diagnosis, resep, atau status
    final.
-   Operasi finalisasi dan penerbitan resep menggunakan transaksi
    database yang sesuai.
-   Penyimpanan lampiran menggunakan akses privat, bukan URL publik
    permanen.
-   API mengembalikan resource khusus sesuai peran pengguna.
-   Operasi finalisasi harus idempoten agar request berulang tidak
    membuat duplikasi.
-   Perubahan bersamaan harus dideteksi untuk mencegah data saling
    menimpa.

## 13. Rancangan API Laravel

  -----------------------------------------------------------------------------------------------------
  Method                  Endpoint                                              Fungsi
  ----------------------- ----------------------------------------------------- -----------------------
  GET                     `/api/v1/doctor/medical-records`                      Daftar pemeriksaan
                                                                                dokter

  POST                    `/api/v1/doctor/medical-records`                      Membuat pemeriksaan

  GET                     `/api/v1/doctor/medical-records/{id}`                 Detail pemeriksaan

  PATCH                   `/api/v1/doctor/medical-records/{id}`                 Memperbarui header
                                                                                pemeriksaan

  PUT                     `/api/v1/doctor/medical-records/{id}/subjective`      Simpan anamnesa

  PUT                     `/api/v1/doctor/medical-records/{id}/objective`       Simpan pemeriksaan
                                                                                objektif

  PUT                     `/api/v1/doctor/medical-records/{id}/assessment`      Simpan diagnosis dan
                                                                                penilaian

  PUT                     `/api/v1/doctor/medical-records/{id}/plan`            Simpan rencana
                                                                                penanganan

  POST                    `/api/v1/doctor/medical-records/{id}/prescriptions`   Membuat draft resep

  PATCH                   `/api/v1/doctor/prescriptions/{id}`                   Memperbarui draft resep

  POST                    `/api/v1/doctor/prescriptions/{id}/issue`             Menerbitkan resep

  POST                    `/api/v1/doctor/medical-records/{id}/finalize`        Finalisasi pemeriksaan

  POST                    `/api/v1/doctor/medical-records/{id}/amendments`      Membuat amendemen

  POST                    `/api/v1/doctor/medical-records/{id}/followups`       Membuat rencana kontrol

  GET                     `/api/v1/member/cats/{catId}/medical-history`         Riwayat pemeriksaan
                                                                                kucing

  GET                     `/api/v1/member/medical-records/{id}/summary`         Ringkasan medis member

  GET                     `/api/v1/member/prescriptions/{id}`                   Detail resep yang
                                                                                diterbitkan
  -----------------------------------------------------------------------------------------------------

### 13.1 Standar Implementasi API

-   Gunakan Laravel Form Request untuk validasi payload.
-   Gunakan Policy untuk pemeriksaan kepemilikan dan otorisasi.
-   Gunakan transaksi database pada finalisasi dan penerbitan resep.
-   Gunakan idempotency key untuk operasi yang berpotensi dikirim ulang.
-   Gunakan API Resource khusus dokter dan member.
-   Gunakan pagination untuk daftar pemeriksaan dan riwayat.
-   Gunakan format waktu ISO 8601.
-   Gunakan kode HTTP konsisten: `403` akses ditolak, `404` resource
    tidak tersedia bagi pengguna, `409` konflik status, `422` validasi
    gagal.

Contoh payload finalisasi:

``` json
{
  "confirmation": true,
  "idempotency_key": "unique-request-identifier"
}
```

Backend tetap harus menentukan data yang ditampilkan berdasarkan
kebijakan akses. Pilihan dari client tidak boleh menjadi pengganti
validasi server.

## 14. Keamanan, Privasi, dan Integritas

-   Otorisasi berbasis peran dan relasi pada setiap request.
-   Pemisahan catatan internal dokter dari ringkasan member.
-   Audit trail untuk finalisasi, penerbitan resep, pembatalan, dan
    amendemen.
-   Private storage untuk lampiran, signed URL berumur pendek, validasi
    tipe file, dan batas ukuran.
-   Pembatasan penerbitan resep kepada dokter berwenang.
-   Backup rutin dan pengujian pemulihan data.
-   Kebijakan retensi dan prosedur penanganan insiden.
-   Kebijakan akses, penyimpanan, dan pengungkapan data harus
    diselaraskan dengan ketentuan hukum yang berlaku di Indonesia serta
    kebijakan privasi KucingMu.

## 15. Acceptance Criteria

  -----------------------------------------------------------------------
  ID                      Skenario                Kriteria keberhasilan
  ----------------------- ----------------------- -----------------------
  AC-01                   Dokter membuat          Tipe layanan tersimpan
                          pemeriksaan online      sebagai `online`

  AC-02                   Dokter membuat          Tipe layanan tersimpan
                          pemeriksaan klinik      sebagai `clinic` dan
                                                  konteks klinik sesuai

  AC-03                   Member mengisi keluhan  Keluhan asli tersimpan
                                                  dan dapat dibedakan
                                                  dari klarifikasi dokter

  AC-04                   Dokter mengisi SOAP     Semua step dapat
                                                  disimpan sebagai draft
                                                  dan dibuka kembali

  AC-05                   Dokter berpindah step   Data yang sudah
                                                  dimasukkan tidak hilang

  AC-06                   Dokter menerbitkan      Data obat dan aturan
                          resep                   pakai tervalidasi serta
                                                  terhubung ke
                                                  pemeriksaan

  AC-07                   Dokter memfinalisasi    Status berubah menjadi
                                                  `completed` dan tidak
                                                  dapat diedit langsung

  AC-08                   Member membuka          Hanya informasi yang
                          ringkasan               diizinkan yang
                                                  ditampilkan

  AC-09                   Dokter mengoreksi       Versi sebelumnya tetap
                          catatan                 dapat diaudit

  AC-10                   Member membuka pasien   Akses ditolak di
                          lain                    backend

  AC-11                   Dokter membuka catatan  Akses ditolak
                          di luar kewenangannya   

  AC-12                   Request finalisasi      Tidak membuat duplikasi
                          dikirim ulang           finalisasi atau resep

  AC-13                   Kontrol dijadwalkan     Kontrol terhubung
                                                  dengan pasien dan dapat
                                                  dikaitkan dengan
                                                  pemeriksaan berikutnya

  AC-14                   Member membuka lampiran Hanya lampiran yang
                                                  diizinkan dan
                                                  diterbitkan yang dapat
                                                  diakses
  -----------------------------------------------------------------------

### 15.1 Pengujian Tambahan

-   Draft disimpan lalu sesi login berakhir.
-   Dua sesi memperbarui rekam medis yang sama.
-   Koneksi terputus ketika finalisasi berlangsung.
-   Resep dibatalkan setelah diterbitkan.
-   Master obat berubah setelah resep lama diterbitkan.
-   Status pemeriksaan berubah saat validasi belum selesai.
-   Lampiran menggunakan ekstensi yang tidak sesuai dengan tipe
    sebenarnya.
-   Member mencoba mengakses ringkasan milik kucing lain.

## 16. Roadmap Implementasi

### Fase 1 --- Fondasi Data dan Akses

-   Meninjau tabel pasien, member, dokter, jadwal, dan layanan yang
    sudah ada.
-   Membuat migration tabel rekam medis dan status.
-   Menetapkan Policy, otorisasi, dan audit dasar.
-   Menyusun master parameter pemeriksaan awal.

### Fase 2 --- Workspace Dokter

-   Membuat daftar pemeriksaan dan detail pasien.
-   Mengimplementasikan stepper SOAP.
-   Menambahkan penyimpanan draft dan validasi.
-   Membuat halaman review dan finalisasi.
-   Menangani konflik perubahan bersamaan.

### Fase 3 --- Resep dan Instruksi

-   Membuat master obat.
-   Membuat resep terstruktur.
-   Menambahkan validasi satuan dan dosis.
-   Menambahkan instruksi perawatan, tanda bahaya, dan kontrol.
-   Menangani pembatalan serta penggantian resep.

### Fase 4 --- Mobile Member

-   Menampilkan riwayat pemeriksaan.
-   Menampilkan ringkasan medis yang diterbitkan.
-   Menampilkan resep dan instruksi.
-   Menambahkan notifikasi pemeriksaan selesai dan kontrol.

### Fase 5 --- QA dan Pilot

-   Menguji akses dan keamanan.
-   Menguji workflow online dan klinik.
-   Menguji integritas resep dan audit.
-   Melakukan uji coba bersama dokter.
-   Mengumpulkan masukan dan memperbaiki UX sebelum rilis luas.

## 17. Risiko dan Mitigasi

  -----------------------------------------------------------------------
  Risiko                              Mitigasi
  ----------------------------------- -----------------------------------
  Dokter tidak konsisten mengisi data Gunakan form terstruktur, template,
                                      dan validasi yang proporsional

  Resep salah atau tidak lengkap      Validasi satuan, konsentrasi,
                                      dosis, frekuensi, dan durasi;
                                      persetujuan dokter wajib

  Data internal tampil kepada member  Gunakan resource khusus member dan
                                      pengujian akses

  Catatan berubah setelah finalisasi  Gunakan amendemen dan audit trail

  Data hilang akibat konflik sesi     Optimistic locking atau mekanisme
                                      versioning

  Perbedaan kebutuhan online dan      Gunakan struktur SOAP bersama
  klinik                              dengan metadata jenis layanan dan
                                      sumber temuan

  Master obat belum lengkap           Dukung input khusus dengan validasi
                                      dan pembatasan yang sesuai
  -----------------------------------------------------------------------

## 18. Keputusan yang Masih Perlu Dikonfirmasi

Sebelum implementasi final, tim produk dan dokter perlu menyepakati:

1.  Terminologi veteriner untuk parameter pemeriksaan, diagnosis, dan
    tingkat kepastian.
2.  Siapa yang bertanggung jawab mengelola master obat dan satuan.
3.  Aturan resep khusus dan validasi dosis untuk setiap jenis obat.
4.  Bagian SOAP yang dapat diterbitkan kepada member.
5.  Kebijakan amendemen dan pemberitahuan kepada member.
6.  Kebijakan retensi, akses, dan penyimpanan data sesuai ketentuan yang
    berlaku.
7.  Tabel existing KucingMu yang akan dipakai kembali agar tidak terjadi
    duplikasi entitas.

## 19. Kesimpulan

Fondasi yang disarankan untuk KucingMu adalah:

-   Satu entitas pemeriksaan yang menghubungkan pasien, member, dokter,
    dan jenis layanan.
-   SOAP terstruktur dengan stepper dan halaman review.
-   Resep elektronik terpisah yang terhubung dengan pemeriksaan dan
    memiliki riwayat penerbitan.
-   Ringkasan medis khusus member dengan kontrol akses di backend.
-   Follow-up dan riwayat medis untuk mendukung perawatan berkelanjutan.
-   Audit trail dan amendemen untuk menjaga integritas catatan klinis.

Urutan implementasi yang disarankan adalah `medical_records` → SOAP →
resep → ringkasan member → audit dan follow-up. Sebelum migration final
dibuat, skema perlu dibandingkan dengan struktur database KucingMu yang
sudah berjalan.
