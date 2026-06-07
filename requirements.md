# Requirements Document
## Sistem Web Data Absensi Karyawan Berbasis PHP Laravel

**Versi:** 1.0  
**Tanggal:** Juni 2026  
**Penulis:** Grok (xAI) berdasarkan permintaan detail  
**Tujuan Dokumen:** Memberikan spesifikasi lengkap, terstruktur, dan actionable untuk pengembangan aplikasi web absensi karyawan yang modern, aman, interaktif, dan scalable.

---

## 1. Project Overview & Business Context

### 1.1 Latar Belakang
Perusahaan membutuhkan sistem digital untuk menggantikan proses absensi manual atau fingerprint sederhana yang rawan kesalahan, proxy, dan sulit dilacak. Karyawan terdiri dari tiga kategori utama:
- **Security** (penjaga keamanan)
- **Cleaning Service** (petugas kebersihan)
- **Helper** (pekerja pendukung/serabutan)

Setiap karyawan memiliki atribut wajib: **NIK** (Nomor Induk Karyawan - unik), **No Telp** (untuk notifikasi & kontak darurat).

Fitur inti yang diminta:
- Clock In & Clock Out via **Scan QR Code** (modern, contactless, cepat di mobile)
- Manajemen **Cuti** (Sakit, Ijin, Alfa)
- **Lemburan** (Overtime)
- **Pelacakan Lokasi & Aktivitas** real-time atau near-real-time
- **Payroll** otomatis berbasis data absensi + shift + lembur
- **Jadwal Kerja (Shift)** yang fleksibel

### 1.2 Tujuan Bisnis
- Meningkatkan akurasi data absensi (>98%)
- Mengurangi waktu admin HR untuk input & rekap manual
- Meningkatkan transparansi & akuntabilitas (lokasi + aktivitas log)
- Otomatisasi payroll untuk mengurangi kesalahan hitung & keterlambatan gaji
- Mendukung compliance & audit (log lengkap)
- Pengalaman karyawan modern (self-service via HP)

### 1.3 Target Pengguna (User Roles)
1. **Super Admin / Owner** — Full access, konfigurasi sistem, laporan eksekutif
2. **HR Officer** — Kelola karyawan, approve cuti/lembur, generate payroll, monitoring
3. **Supervisor / Manager** — Approve cuti/lembur timnya, lihat dashboard tim, monitoring lokasi
4. **Karyawan (Employee)** — Self-service: Scan QR clock in/out, ajukan cuti/lembur, lihat riwayat & payslip pribadi

### 1.4 Scope
**In Scope:**
- Web application (desktop + mobile responsive, ideal PWA untuk check-in)
- Admin panel modern + employee self-service portal
- QR-based attendance + optional geolocation verification
- Full workflow cuti, lembur, shift, payroll
- Reporting & analytics
- Activity & location logging

**Out of Scope (MVP v1):**
- Mobile native app (bisa ditambahkan nanti via API)
- Face recognition / biometrik lanjutan (bisa integrasi nanti)
- Integrasi fingerprint device fisik (meski bisa ditambah)
- Payroll transfer otomatis ke bank (manual atau integrasi payment gateway nanti)
- Multi-tenant / multi-cabang kompleks (bisa di-phase 2)

---

## 2. Functional Requirements (FR) — Detail & Nuansa

### FR-01: Manajemen Karyawan & User Account
- CRUD lengkap karyawan dengan field: Nama lengkap, NIK (unik, validasi format), No. Telp (format ID), Tipe/Jabatan (select: Security | Cleaning Service | Helper), Foto profil (upload & crop), Tanggal bergabung, Status (Aktif/Nonaktif), Alamat, dll.
- Role & Permission granular (menggunakan Filament Shield atau Spatie Permission).
- Import massal dari Excel/CSV + template download.
- Export daftar karyawan + QR code batch (PDF siap cetak untuk ID card).
- Soft delete + restore untuk karyawan keluar.
- **Nuansa/Edge Case:** NIK harus unik & immutable setelah dibuat. Saat karyawan nonaktif, semua jadwal & akses otomatis dinonaktifkan. Foto opsional tapi recommended untuk verifikasi manual.

### FR-02: Autentikasi, Otorisasi & Keamanan
- Login via Email atau NIK + Password (atau magic link).
- Role-based access control (RBAC) ketat di setiap halaman & action.
- Password reset via email.
- Session timeout + remember me.
- Opsional: 2FA (TOTP via package) untuk HR & Admin.
- **Nuansa:** Semua aksi sensitif (approve payroll, delete record) butuh konfirmasi + log. IP whitelist opsional untuk admin.

### FR-03: Clock In & Clock Out via Scan QR (Core Feature)
- **QR Generation:** Setiap karyawan punya QR unik (encode: employee_id + hashed secret + timestamp optional). Bisa di-generate ulang. QR bisa ditampilkan di profile atau dicetak.
- **Scanning Page:** Halaman khusus `/check-in` atau di employee portal.
  - Desain modern, full-screen friendly untuk kiosk atau HP.
  - Tombol besar "Buka Kamera & Scan QR".
  - Preview kamera dengan guide box (corner markers) + animasi scan line.
  - Fallback: Input manual NIK + PIN atau search karyawan (untuk admin).
- **Proses setelah scan berhasil:**
  1. Decode QR → validasi karyawan aktif.
  2. Cek apakah sudah clock-in hari ini / shift berjalan.
  3. **Opsional Geolocation Check:** Jika diaktifkan, ambil lat/lng dari browser. Bandingkan dengan radius kantor (configurable, misal 100-500m). Jika di luar radius → warning atau block (policy perusahaan).
  4. Record attendance: `clock_in` atau `clock_out`, lokasi, accuracy, device info, IP.
  5. Update status real-time di dashboard.
  6. Notifikasi sukses dengan animasi (checkmark + confetti ringan) + tampilkan nama karyawan + waktu.
- **Status Otomatis:**
  - Present (tepat waktu)
  - Late (terlambat > X menit dari shift start)
  - Early Out (pulang lebih cepat)
- **Edge Cases:**
  - Lupa clock-out → Admin bisa manual close dengan alasan + notifikasi ke karyawan.
  - Multiple shift/hari → sistem support flexible (bisa clock in/out berkali-kali dengan catatan shift).
  - QR rusak/expired → Admin bisa regenerate atau manual entry.
  - Anti-proxy: Sarankan + lokasi check + optional selfie capture (bisa ditambah nanti).

### FR-04: Manajemen Cuti (Sakit, Ijin, Alfa)
- **Tipe Cuti:**
  - **Sakit**: Butuh alasan + upload bukti (foto surat dokter/resep). Bisa auto-approve atau butuh verifikasi.
  - **Ijin**: Dengan alasan bebas. Butuh approval.
  - **Alfa**: Tidak hadir tanpa ijin. Bisa di-mark otomatis oleh sistem jika tidak ada attendance record + tidak ada cuti approved, atau manual oleh HR.
- **Alur:**
  1. Karyawan ajukan via form (pilih tipe, rentang tanggal, reason, upload bukti).
  2. Notifikasi ke Supervisor/HR.
  3. Approval/Reject dengan komentar.
  4. Update attendance record otomatis (status = On Leave / Alfa).
- **Saldo & Kuota:** Bisa dikonfigurasi per tipe atau total hari/tahun. Tampilkan sisa saldo saat request.
- **Kalender View:** Global + per karyawan (overlap warning).
- **Nuansa:** Alfa sangat mempengaruhi payroll (potongan). Sistem bisa suggest "potential Alfa" list harian untuk HR review. Bukti wajib untuk Sakit > 2 hari.

### FR-05: Lemburan (Overtime)
- **Cara Masuk:**
  - Otomatis: Jika clock_out > shift_end + durasi > threshold (misal 30 menit).
  - Manual request oleh karyawan atau supervisor.
- Form request: Tanggal, jam mulai-selesai, alasan, link ke attendance record.
- Approval workflow mirip cuti.
- **Perhitungan:** Jam lembur = total durasi disetujui. Rate bisa 1.5x, 2x, atau custom per hari/libur.
- **Nuansa:** Bisa ada limit max jam lembur per minggu/bulan untuk compliance. Laporan biaya lembur bulanan.

### FR-06: Jadwal Kerja (Shift Management)
- **Shift Definition:** Nama shift (Pagi, Siang, Malam, Custom), Jam mulai & selesai, Hari aktif (checkbox atau json), Lokasi default, Color coding.
- **Assignment:**
  - Individual atau massal ke karyawan.
  - Effective date range + recurring (mingguan).
  - Pivot table `employee_shift`.
- **View:**
  - Calendar interaktif (FullCalendar integration) — drag & drop assignment jika memungkinkan.
  - List view + filter.
- **Fitur Lanjutan:**
  - Shift swap request antar karyawan (dengan approval).
  - Conflict detection (satu karyawan 2 shift overlap).
  - Auto-generate attendance template berdasarkan shift.
- **Nuansa:** Security sering malam/weekend, Cleaning pagi/siang. Sistem harus fleksibel untuk rotasi.

### FR-07: Pelacakan Lokasi & Aktivitas
- **Location Capture:** Saat clock in/out (jika izin diberikan browser). Simpan: lat, lng, accuracy, timestamp.
- **Office Location Config:** Admin set koordinat pusat + radius per lokasi/cabang.
- **Display:**
  - Di detail attendance: Peta statis atau interaktif (Leaflet) dengan pin + radius lingkaran.
  - History map per karyawan (polyline jika multiple points, atau markers).
  - Dashboard monitoring: Peta dengan posisi karyawan terkini (update via last attendance atau polling).
- **Activity Log:**
  - Semua aksi user tercatat (login, scan QR, ajukan cuti, approve, edit data, dll).
  - Kolom: User, Action, Model affected, Old/New values (audit), IP, User Agent, Timestamp.
  - Filter & search powerful.
- **Privacy & Compliance:**
  - Consent checkbox saat pertama kali minta lokasi.
  - Hanya HR/Admin bisa lihat lokasi detail.
  - Log akses ke data lokasi.
  - Opsi anonymize atau blur koordinat jika diperlukan.
- **Nuansa/Edge:** Akurasi GPS HP bervariasi (indoor jelek). Sistem harus handle "approximate location". Jika lokasi tidak tersedia → tetap izinkan clock in tapi flag "No GPS data".

### FR-08: Payroll & Kompensasi
- **Komponen Gaji (Configurable per employee atau global + override):**
  - Gaji Pokok (harian atau bulanan)
  - Tunjangan tetap
  - Rate Lembur
  - Potongan: Alfa (full day), Telat (per menit atau flat), Cuti Unpaid, dll.
- **Perhitungan Otomatis:**
  - Periode: Bulanan (atau custom).
  - Worked Days/Hours = hitung dari attendance valid dalam shift.
  - OT Hours = dari overtime records.
  - Deductions & Additions otomatis.
  - Net Pay = Gross - Deductions + Allowances.
- **Workflow:**
  1. Generate draft payroll (button "Calculate & Preview").
  2. Review per karyawan atau summary.
  3. Approve / Adjust manual (dengan log alasan).
  4. Finalize → generate PDF payslip individual.
  5. Export rekap untuk finance.
- **Fitur:**
  - History payroll per karyawan.
  - "Payslip" view/download untuk karyawan.
  - Email payslip otomatis (opsional).
- **Nuansa Penting:** Perhitungan harus **auditable** & **reproducible**. Gunakan transaksi database + snapshot data attendance saat generate. Ada "Re-calculate" button jika ada koreksi attendance belakangan. Support prorata untuk karyawan join/ resign mid-period.

### FR-09: Dashboard, Reporting & Analytics
- **Main Dashboard (role-based):**
  - KPI Cards: Total Karyawan Aktif, Kehadiran Hari Ini (%), Karyawan Terlambat, Pending Cuti/Lembur, Total OT Bulan Ini, Estimasi Payroll Bulan Ini.
  - Charts: Trend kehadiran 30 hari (line), Distribusi status (pie), Heatmap keterlambatan per hari/jam, Biaya OT.
  - Quick links & recent activity feed.
- **Reports:**
  - Attendance Report (filter tanggal, karyawan, tipe, status) → table + export Excel/PDF.
  - Leave Usage Report.
  - Payroll Summary & Detail.
  - Punctuality Report.
  - Custom report builder sederhana.
- **Export:** Excel (Maatwebsite/Excel), PDF (DomPDF), CSV.
- **Scheduled Reports:** Kirim via email mingguan/bulanan (queue).

### FR-10: Notifikasi & Komunikasi
- In-app notifications (Filament built-in + custom).
- Email (Laravel Mail + queue).
- Opsional: WhatsApp Business API atau gateway lokal untuk reminder clock-in, approval urgent, payslip ready.
- Reminder otomatis: "Jangan lupa clock out", "Shift Anda mulai dalam 1 jam", dll.

---

## 3. Non-Functional Requirements (NFR)

| Aspek              | Requirement                                                                 | Metrik / Cara Ukur                  |
|--------------------|-----------------------------------------------------------------------------|-------------------------------------|
| **Performance**    | Load dashboard < 2s, table 1000 rows < 1s, QR scan response < 3s           | Lighthouse score > 90, query optimization, caching |
| **Security**       | OWASP Top 10 compliant, role checks di semua endpoint, encryption untuk sensitive data | Penetration test, Filament security practices |
| **Usability**      | Mobile-first, < 3 klik untuk clock in, intuitive untuk non-tech user       | User testing, task completion rate |
| **Reliability**    | 99.5% uptime, data consistency via DB transactions, graceful error handling | Monitoring (Sentry), automated tests |
| **Maintainability**| Clean Architecture (Service/Repository pattern opsional), comprehensive tests, good docs | Code review, Pest coverage > 70% |
| **Scalability**    | Support 5000+ karyawan & 100 concurrent users tanpa rewrite besar           | Horizontal scaling, queue workers, CDN |
| **Accessibility**  | WCAG 2.1 AA minimal                                                         | Automated + manual audit            |

---

## 4. Tech Stack & Architecture Recommendation

**Paling Direkomendasikan (Modern + Produktif):**
- **Backend:** Laravel 12 (PHP 8.3+)
- **Admin Panel & UI Framework:** **FilamentPHP v3 atau v4** (sangat cocok — lihat desain.md untuk detail)
- **Interactivity:** Livewire 3 + Alpine.js 3 (TALL Stack)
- **Styling:** Tailwind CSS + custom theme (untuk depth & modern look)
- **Database:** MySQL 8.0+ atau PostgreSQL 15+
- **Queue & Cache:** Redis (untuk payroll batch, notifications, rate limiting)
- **File Storage:** Laravel Storage (local atau S3-compatible)
- **QR Code:**
  - Generate: `endroid/qr-code` atau `simplesoftwareio/simple-qrcode`
  - Scan (client): `mebjas/html5-qrcode` (pure JS, bagus untuk mobile)
- **Maps & Location:** Leaflet.js (gratis, ringan) + browser Geolocation API
- **Charts:** Filament Chart Widgets atau Chart.js / ApexCharts
- **Permissions:** `bezhansalleh/filament-shield` (sangat recommended)
- **PDF:** `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`
- **Excel:** `maatwebsite/excel`
- **Testing:** Pest PHP + Laravel Dusk (untuk browser QR flow)
- **Monitoring:** Sentry.io (error + performance)
- **Deployment:** Laravel Forge / Ploi / Docker + Nginx + Supervisor untuk queue

**Arsitektur Umum:**
- Monolith Laravel dengan Filament Panel(s)
- Bisa multi-panel: `/admin` (HR) + `/employee` (self-service) jika diperlukan
- Service Layer untuk business logic kompleks (PayrollCalculator, AttendanceService, dll.)
- Event + Listener atau Job untuk side effects (kirim notif, update cache, log activity)
- Observer untuk model events (auto flag Alfa, dll.)

---

## 5. Database Schema (High-Level Entities)

**Core Tables:**
- `users` (auth) — linked ke `employees` via `user_id` (atau polymorphic)
- `employees`
  - `id`, `user_id`, `nik` (unique), `nama`, `no_telp`, `tipe` (enum), `foto_path`, `join_date`, `status`, `base_salary_type`, `base_salary_amount`, `office_location_id`, timestamps, soft deletes
- `shifts`
- `employee_shift` (pivot dengan `effective_from`, `effective_until`)
- `attendances`
  - `employee_id`, `shift_id`, `clock_in`, `clock_out`, `lat`, `lng`, `accuracy`, `status` (present/late/early_out/on_leave), `notes`, `created_by_id`
- `leave_requests`
  - `employee_id`, `type` (sakit/ijin/alfa), `start_date`, `end_date`, `reason`, `evidence_path`, `status`, `approved_by_id`, `comments`
- `overtime_records` atau `overtime_requests`
- `payrolls`
  - `employee_id`, `period_start`, `period_end`, `worked_days`, `worked_hours`, `ot_hours`, `gross_pay`, `deductions`, `net_pay`, `status`, `pdf_path`
- `activity_logs` (polymorphic atau dedicated)
- `office_locations` (untuk multi-site radius)
- `notifications` (Laravel built-in + custom)

Gunakan Eloquent relationships lengkap + accessors/mutators untuk computed fields (misal attendance_rate).

---

## 6. Key User Flows (Contoh)

**Flow Karyawan Clock In:**
1. Buka link check-in (bisa bookmark atau dari notif)
2. Izinkan kamera + lokasi (first time)
3. Scan QR dari ID card atau yang ditampilkan di HP
4. Sistem validasi + record + tampilkan sukses + waktu
5. Dashboard karyawan update otomatis

**Flow HR Generate Payroll:**
1. Pilih periode
2. Klik "Generate Draft"
3. Review summary + drill-down per karyawan (bisa adjust)
4. Approve
5. Download rekap Excel + kirim payslip email (queue)

---

## 7. Risiko, Asumsi & Rekomendasi Implementasi

- **Asumsi:** Karyawan punya HP dengan kamera & internet (atau kiosk di lokasi). Perusahaan punya koordinat kantor yang jelas.
- **Risiko Utama & Mitigasi:**
  - GPS tidak akurat indoor → Fallback ke manual confirmation + log.
  - Karyawan lupa bawa HP/QR → Fallback manual entry + foto.
  - Proxy scan → Kombinasi lokasi + random spot check + log aktivitas.
- **Phase Implementation:** Mulai dari Employee + Attendance + QR (MVP 4-6 minggu), lalu tambah Cuti, Shift, Payroll bertahap.
- **Testing Prioritas:** QR scan flow (berbagai device), Payroll calculation edge cases, concurrent access saat generate payroll.

Dokumen ini sudah sangat detail dan lengkap. Siap untuk dijadikan acuan development team.

---

**Catatan Akhir:**  
Requirements ini dirancang agar **mudah diimplementasikan dengan FilamentPHP** sehingga development cepat namun hasil UI/UX tetap modern dan interaktif (lihat `desain.md` untuk panduan visual & interaksi detail). 

Jika butuh ERD visual (draw.io / dbdiagram), wireframe Figma, atau API spec tambahan — beri tahu!