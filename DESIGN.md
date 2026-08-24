# DESIGN.md — KucingMu Design Direction

> Panduan Arah Visual dan Design Tokens untuk Platform KucingMu (Sistem E-Surveillance & Kesehatan Kucing Komunitas).

## 1. Identitas & Karakter
- **Produk**: KucingMu — Platform E-Surveillance, Registrasi KTAM (Kartu Tanda Anggota Muhammadiyah) Kucing, dan Manajemen Rekam Medis Kucing Komunitas.
- **Audience**: Warga komunitas pemilik kucing, relawan lapangan (surveilans populasi liar), dokter hewan klinik mitra, dan administrator Majelis Lingkungan Hidup.
- **Karakter Visual**: Profesional, terstruktur, hangat, ramah, dan mengutamakan aksesibilitas di lapangan (*mobile-first fieldwork*).

## 2. Antislop Dials
- **ENERGY: 2 (Balanced)**
  - Tampilan tenang namun percaya diri. Tidak berlebihan dengan efek neon atau gradien AI, namun tetap memiliki identitas visual yang khas dan segar.
- **RHYTHM: 2 (Structured with breaks)**
  - Komposisi dinamis sesuai konteks: data tabular untuk antrian dokter, kartu profil informatif untuk kucing kesayangan, dan form terpandu langkah demi langkah untuk survei lapangan.
- **MOTION: 1 (Calm & Purposeful)**
  - Transisi mikro yang lembut untuk hover dan dialog status. Tidak ada animasi loop atau elemen melayang yang mengaburkan data.

## 3. Palet Warna (Curated & Accessible)
- **Primary Emerald/Teal**:
  - `brand-900`: `#134e4a` (Header & teks primer)
  - `brand-800`: `#115e59` (Brand bar & button hover)
  - `brand-700`: `#0f766e` (Tombol primer & link utama)
  - `brand-100`: `#ccfbf1` (Badge background)
  - `brand-50`: `#f0fdfa` (Surface highlight)
- **Neutral Slate**:
  - `slate-900`: `#0f172a` (Teks judul & teks kontras tinggi)
  - `slate-700`: `#334155` (Teks body normal — rasio kontras 10.5:1 terhadap putih)
  - `slate-500`: `#64748b` (Teks pembantu/label sekunder — rasio kontras > 4.6:1)
  - `slate-200`: `#e2e8f0` (Border & divider)
  - `slate-50`: `#f8fafc` (Background canvas)
- **Status & Triase Medis**:
  - `amber-700`: `#b45309` (Peringatan & triase kuning/pantauan)
  - `rose-700`: `#be123c` (Kondisi kritis & tindakan segera)
  - `emerald-700`: `#047857` (Kondisi sehat & verifikasi selesai)

## 4. Tipografi
- **Headings**: `Outfit`, sans-serif (ramah, modern, tegas).
- **Body & Data**: `Inter`, ui-sans-serif, system-ui (keterbacaan tinggi untuk formulir & tabel medis).

## 5. Komponen & Standar Interaksi
- **Touch Target**: Minimal 44 x 44 px untuk semua kontrol interaktif (tombol, input radio surveilans, dropdown, checkbox).
- **Focus Indicator**: Ring 3px dengan kontras tinggi (`focus-visible:ring-2 focus-visible:ring-teal-700 focus-visible:ring-offset-2`).
- **Elevasi & Shadow**: Digunakan hemat untuk membedakan modal dan sticky bottom bar. Mayoritas kartu menggunakan border halus `slate-200` pada canvas `slate-50`.
- **UI States**: Setiap tampilan data wajib menyediakan 3 state lengkap:
  1. *Empty State*: Menjelaskan mengapa data kosong dan memberikan 1 aksi solutif berikutnya.
  2. *Loading State*: Memberikan teks informatif (bukan hanya spinner bisu).
  3. *Error State*: Menjelaskan kendala dan tombol coba lagi / hubungi bantuan.
