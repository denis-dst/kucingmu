# KucingMu Design System & Brand Identity

Dokumen ini mendefinisikan identitas visual, palet warna, tipografi, dan aturan antislop untuk aplikasi **KucingMu** (Sistem Informasi & E-Surveillance Kesehatan Kucing Komunitas).

---

## 1. Dial Visual Antislop
- **ENERGY: 2** (Fokus, nyaman di mata, bersih, dan profesional).
- **RHYTHM: 2** (Variasi layout fungsional antara form sensus, tabel medis, dan kartu keanggotaan).
- **MOTION: 1** (Mikro-interaksi halus dan tenang tanpa animasi berlebih).

---

## 2. Palet Warna (Color Palette)

Palet warna menggunakan kombinasi hangat dan lembut yang nyaman di mata:

| Token | Nilai / Kelas Tailwind | Penggunaan |
|---|---|---|
| **Primary Brand** | `#0f766e` (`teal-700`), `#115e59` (`teal-800`) | Tombol utama, brand mark, link aktif |
| **Hero Background** | `bg-gradient-to-br from-teal-50 to-sky-50` | Kartu hero panel dashboard (lembut, tidak silau) |
| **Landing Hero** | `bg-gradient-to-br from-teal-900 via-teal-800 to-sky-800` | Header landing page publik |
| **Surface / Card** | `#ffffff` (`bg-white`), `#f8fafc` (`bg-slate-50`) | Card latar data, tabel, container form |
| **Border** | `#e2e8f0` (`border-slate-200`), `#ccfbf1` (`border-teal-100`) | Garis pembatas kartu |
| **Text Primary** | `#0f172a` (`text-slate-900`), `#1e293b` (`text-slate-800`) | Judul dan teks utama (kontras tinggi) |
| **Text Secondary** | `#475569` (`text-slate-600`), `#64748b` (`text-slate-500`) | Keterangan pendukung |
| **Triage / Warning** | `#b45309` (`amber-700`), `#fef3c7` (`amber-50`) | Status pending verifikasi KTAM |
| **Success** | `#047857` (`teal-700`), `#f0fdfa` (`teal-50`) | Status terverifikasi, KTAM terbit |

---

## 3. Tipografi
- **Headings / Judul**: `Outfit`, sans-serif (Tegas, modern, ramah).
- **Body & Formulir**: `Inter`, system-ui, sans-serif (Keterbacaan optimal).

---

## 4. Komponen & Styling
- **Hero Card**: Menggunakan `bg-gradient-to-br from-teal-50 to-sky-50` dengan border `border-teal-100` dan teks `text-slate-900` untuk kenyamanan visual.
- **Content Card**: `bg-white` dengan radius `rounded-2xl` dan border `border-slate-200`.
- **Button Primary**: `bg-teal-700` dengan hover `bg-teal-800` dan teks putih tebal.
- **Button Secondary**: `bg-white` dengan border `border-slate-200` dan teks `text-slate-700`.
- **Form Input**: `border-slate-300 focus:border-teal-600 focus:ring-teal-100`.
