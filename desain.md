# Desain Document (UI/UX & Visual Design)
## Sistem Web Data Absensi Karyawan — Laravel + Filament

**Versi:** 1.0  
**Fokus:** Modern, Interaktif, Memiliki Kedalaman Visual (bukan flat), Micro-interactions & Animations, Terinspirasi dari praktik terbaik enterprise dashboard 2025-2026.  
**Tidak Terlihat "AI-generated" atau Generic:** Desain ini mengadopsi pendekatan layered, contextual, dan purposeful dengan referensi nyata dari proyek open-source berkualitas tinggi.

---

## 1. Design Philosophy & Principles

### 1.1 Core Values
- **Professional yet Approachable** — Cocok untuk Security, Cleaning Service, dan Helper (bukan hanya white-collar).
- **Data-Dense but Scannable** — Banyak informasi tapi mudah dicerna dalam 3 detik.
- **Delightful Interactions** — Setiap aksi memberikan feedback visual yang memuaskan (bukan sekadar "loading").
- **Trust & Transparency** — Lokasi & aktivitas terasa aman & jelas, bukan "mengawasi".
- **Mobile-First + Desktop Powerful** — Karyawan scan QR via HP, HR bekerja di desktop/laptop.

### 1.2 Anti-Patterns yang Dihindari
- Flat design minimalis berlebihan (hanya garis tipis & putih bersih) → terasa kosong & murah.
- Overuse gradient pelangi atau efek AI-generic.
- Animasi berlebihan yang mengganggu (parallax berat, endless loading).
- Desain yang hanya "terlihat bagus di screenshot" tapi buruk di real use (slow table, hidden actions).

### 1.3 Referensi Utama (Real Projects)
1. **[IlhamGhaza/laravel-attendance-lab](https://github.com/IlhamGhaza/laravel-attendance-lab)** — Laravel 11 + **Filament 3** dengan geolocation, shift management, absence handling. UI-nya clean, intuitive, dan production-ready. Sangat relevan karena sudah pakai Filament untuk attendance + geo + shift.
2. **FilamentPHP Official** (https://github.com/filamentphp/filament — 31k+ stars) + Demo (https://demo.filamentphp.com) — Standar emas untuk Laravel admin panel modern. Tabel powerful, form reaktif, modal/slide-over halus, widget dashboard, notifikasi toasts.
3. **Volt Laravel Dashboard** (Livewire + Alpine + Bootstrap 5) & **Soft UI Dashboard Laravel Livewire** oleh Creative Tim — Contoh soft shadows, layered cards, modern feel dengan depth.
4. **Inspirasi Visual Umum:**
   - Dribbble / Behance: "HR attendance dashboard", "Employee management dashboard 2025", "Modern admin panel with maps".
   - Tren 2025-2026: Subtle glassmorphism pada card/modals (backdrop-blur + border), elevated shadows bertingkat, color-coded semantic status, micro hover lift + scale.

**Kesimpulan Referensi:** Gunakan **FilamentPHP sebagai fondasi utama**. Ini sudah memberikan 80% UI modern + interaktif secara gratis & cepat. Sisanya kita polish dengan custom theme + komponen Livewire/Alpine.

---

## 2. Tech Stack untuk UI/UX (Recommended)

| Komponen              | Pilihan                          | Alasan & Manfaat                                                                 |
|-----------------------|----------------------------------|----------------------------------------------------------------------------------|
| **Admin Panel**       | FilamentPHP v3/v4               | Tabel canggih (search, filter, bulk, sort real-time), Form reaktif, Modal/Slide-over, Dashboard widgets, Notifikasi, Themeable |
| **Interactivity**     | Livewire 3 + Alpine.js 3        | Reaktivitas tanpa full reload, transisi halus, state management mudah           |
| **Styling**           | Tailwind CSS 3.4+ + Custom Theme| Utility-first + mudah buat depth (shadows, gradients subtle, glass)             |
| **Animations**        | Alpine.js transitions + Tailwind animate + CSS keyframes | Ringan, native, controllable. Hindari library berat |
| **Charts**            | Filament Chart Widgets / Chart.js / ApexCharts | Animasi built-in, interaktif (hover, click legend)                             |
| **Maps**              | Leaflet.js (gratis)             | Ringan, customizable, cocok untuk location pins + radius circle                 |
| **QR Scanner**        | html5-qrcode (JS)               | Pure client-side, bagus di mobile, mudah di-style                              |
| **Icons**             | Heroicons (built-in Filament) atau Lucide | Konsisten, modern, accessible                                                   |
| **PDF / Export**      | DomPDF + custom styling         | Payslip & laporan terlihat profesional                                          |

**Alternatif jika ingin lebih custom (bukan Filament full):**
- Laravel + Inertia.js + Vue 3 + shadcn/ui atau Tailwind + daisyUI + FullCalendar.
Tapi **Filament jauh lebih cepat** untuk app CRUD-heavy seperti ini.

---

## 3. Visual Language & Design System

### 3.1 Color Palette (Semantic & Professional)
- **Primary (Trust & Tech):** `#1E3A8A` (deep indigo) atau `#0F766E` (teal) — gunakan untuk button utama, link, accent.
- **Success / Present:** `#10B981` (emerald)
- **Warning / Late:** `#F59E0B` (amber)
- **Danger / Alfa / Reject:** `#EF4444` (red)
- **Info / On Leave:** `#3B82F6` (blue)
- **Neutral / Background:** Slate scale (`#0F172A` dark, `#F8FAFC` light) + subtle off-white cards.
- **Accent Gradient:** Subtle linear gradient indigo → teal pada header atau KPI cards (depth effect).

**Dark Mode:** Didukung penuh (Filament native). Default light untuk kantor, dark untuk malam/shift malam.

### 3.2 Typography
- **Headings:** Inter / system-ui bold (600-700)
- **Body:** Inter atau sans-serif readable
- **Data/Numbers:** Tabular numbers (font-variant-numeric: tabular-nums) agar alignment rapi di tabel & KPI
- **Size Scale:**  text-xs → text-2xl dengan consistent line-height

### 3.3 Elevation & Depth (Anti-Flat)
- **Cards:** `shadow-lg` atau `shadow-xl` + `ring-1 ring-slate-200/50`. Pada hover: `shadow-2xl` + sedikit `translate-y-[-2px]`.
- **Modals / Slide-overs:** `backdrop-blur-md` + `shadow-2xl` + border subtle (glassmorphism ringan).
- **Buttons:** Primary = solid color + shadow-md. Secondary = outline atau soft background. Hover = scale-[1.02] + shadow increase.
- **Tables:** Row hover = `bg-slate-50` + subtle left border accent color. Selected row lebih kuat.
- **KPI Cards:** Background putih/cream + gradient subtle di border atau top accent bar. Number besar dengan count-up animation (via Alpine atau CountUp.js ringan).

### 3.4 Animations & Micro-interactions (Bukan Flat & Membosankan)
- **Page Load:** Staggered fade-in cards (Alpine x-transition dengan delay berbeda).
- **Table Actions:** Row click → smooth slide-over masuk dari kanan.
- **QR Scan Flow:**
  - Tombol scan: scale + shadow pulse saat hover.
  - Kamera modal: smooth scale-up + backdrop fade.
  - Scan berhasil: Checkmark SVG animate (draw path) + confetti ringan (canvas kecil atau CSS particles) + success toast dengan progress.
  - Clock in/out timestamp: angka berubah dengan subtle count animation.
- **Dashboard Charts:** Animate on load (grow from bottom) + hover tooltip + legend click untuk toggle series.
- **Status Badges:** `present` = subtle pulse green dot. `late` = amber dengan ikon clock.
- **Form Validation:** Error muncul dengan shake ringan + red glow.
- **Loading States:** Skeleton screens (Tailwind + Alpine) untuk tabel & dashboard, bukan spinner polos.
- **Global:** Semua transisi pakai `transition-all duration-200 ease-out` atau `ease-in-out`. Hindari >300ms kecuali deliberate (modal).

**Contoh Kode Alpine Sederhana untuk Micro-interaction:**
```html
<button x-data @click="scanQR()" 
        class="px-8 py-4 bg-indigo-600 text-white rounded-2xl shadow-lg hover:shadow-2xl hover:scale-[1.02] active:scale-[0.985] transition-all">
    Buka Kamera & Scan QR
</button>
```

---

## 4. Layout & Navigation Structure

### 4.1 Global Layout (Filament-based)
- **Sidebar (Collapsible):** 
  - Logo + Company Name
  - Dashboard
  - Karyawan
  - Absensi
  - Cuti & Alfa
  - Lemburan
  - Jadwal Shift
  - Payroll
  - Laporan & Analytics
  - Activity Log
  - Pengaturan (untuk Admin)
- **Topbar:** Global search (bisa search karyawan/attendance cepat), Notification bell (dropdown dengan unread count + list), User avatar + dropdown (Profile, Logout, Dark mode toggle).
- **Main Content Area:** Responsive padding, max-width container untuk readability.

### 4.2 Role-based View
- **Karyawan view:** Sidebar minimal (Dashboard pribadi, Riwayat Absensi, Cuti Saya, Lembur Saya, Payslip). Fokus ke self-service.
- **HR/Admin:** Full sidebar + advanced filters & bulk actions.

---

## 5. Key Screens & Interaction Details

### 5.1 Login Page
- Centered clean card dengan logo besar + subtle illustration (bisa custom SVG atau undraw.co modern line style).
- Form minimal: NIK/Email + Password.
- Background: Gradient subtle atau pattern geometris ringan (bukan foto stock generic).
- Remember me + Forgot password link.

### 5.2 Main Dashboard (Paling Penting)
**Layout:** Grid responsive (1-4 kolom).

**Bagian Atas — KPI Cards (6 kartu):**
1. Kehadiran Hari Ini (%)
2. Karyawan Hadir / Total
3. Terlambat Hari Ini
4. Pending Approval (Cuti + Lembur)
5. Total Jam Lembur Bulan Ini
6. Estimasi Total Payroll Bulan Ini

Setiap kartu: Icon besar (Heroicons), angka besar dengan count-up, trend arrow (% vs kemarin/minggu lalu), warna semantic.

**Tengah:**
- **Attendance Trend Chart** (Line + Bar hybrid, 30 hari terakhir) — interaktif.
- **Status Distribution** (Donut/Pie: Present | Late | On Leave | Alfa).
- **Quick Actions** row: Tombol besar "Scan QR Manual", "Generate Payroll Draft", "Export Laporan Hari Ini".

**Bawah:**
- Recent Activity feed (tabel ringkas atau list dengan avatar + action + time ago).
- Mini Calendar upcoming shifts atau heatmap keterlambatan.

**Interaksi:** Semua KPI clickable → langsung filter tabel di halaman terkait. Chart legend clickable.

### 5.3 Halaman Check-in / Scan QR (Pengalaman Terbaik)
**Desain Kiosk/Mobile Optimized:**
- Header sederhana: Logo + "Absensi Digital" + Waktu real-time.
- Area utama: Card besar dengan border tebal atau gradient accent.
  - Judul: "Scan QR Code Anda"
  - Tombol utama sangat prominent (besar, rounded-3xl, shadow-xl).
  - Di bawah: "Atau gunakan NIK manual" (link ke form sederhana).
- Saat tombol diklik → Modal full atau section expand dengan:
  - Video preview kamera (rounded-2xl, border, shadow dalam).
  - Overlay guide: 4 corner markers + garis scan animasi (CSS).
  - Teks instruksi jelas.
- **Setelah Scan Sukses (State Paling Delightful):**
  - Background card berubah ke emerald soft.
  - Animasi checkmark besar (SVG path animate).
  - Nama karyawan + "Berhasil Clock In pukul XX:XX"
  - Info lokasi (jika ada): "Di dalam area kantor ✓"
  - Tombol "Clock Out" muncul jika sudah clock in.
  - Auto close modal setelah 4 detik atau tombol "Kembali ke Dashboard".
- **Fallback & Error States:** Pesan ramah + saran (misal "QR tidak valid. Hubungi HR" atau "Lokasi terlalu jauh").

**Nuansa:** Halaman ini harus terasa **cepat & menyenangkan** — ini touchpoint utama karyawan setiap hari.

### 5.4 Tabel Absensi & Detail
- Filament Table dengan:
  - Kolom: Checkbox bulk, Avatar + Nama, NIK, Tipe, Tanggal, Clock In, Clock Out, Durasi, Status (badge warna), Lokasi (ikon map jika ada), Aksi.
  - Filter advanced: Tanggal range, Tipe karyawan, Status, Shift.
  - Search global.
  - Bulk actions: Export selected, Mark as Alfa (dengan konfirmasi), dll.
- **Row Click / View Action:** Buka **Slide-over** (dari kanan) atau Modal detail:
  - Tab: Info Umum | Timeline (clock in/out history hari itu) | Peta Lokasi (Leaflet embed dengan pin + radius office) | Catatan & Edit.
  - Peta interaktif: Zoom, marker dengan popup info waktu.

### 5.5 Manajemen Cuti & Lemburan
- Tabel dengan status badge + color.
- Form request modern: Select tipe (dengan deskripsi singkat), Date range picker (atau calendar), Textarea reason, File upload (drag & drop area dengan preview).
- Approval: Di tabel atau dedicated queue page, action "Approve" / "Reject" langsung di row (dengan modal konfirmasi + komentar wajib untuk reject).
- Kalender global cuti (FullCalendar) — warna berbeda per tipe.

### 5.6 Jadwal Shift
- **Dua View:**
  1. List + Form assignment (Filament Resource standar).
  2. **Calendar View** (FullCalendar integration via custom Filament page/widget):
     - Events = shift assignments.
     - Drag & drop untuk pindah hari/karyawan (jika diimplementasikan).
     - Click event → edit assignment.
- Color coding per shift type.

### 5.7 Payroll Page
- Header: Periode selector + tombol "Generate / Re-calculate Draft".
- Summary Cards di atas tabel.
- Tabel detail dengan kolom computed (worked days, OT hours, deductions, net pay) — sortable & filterable.
- Setiap row: Action "Lihat Detail" → slide-over dengan breakdown + tombol "Adjust" (hanya Admin) + "Download Payslip PDF".
- Bulk: "Approve All Draft" atau "Generate All Payslips".

### 5.8 Activity Log & Location History
- Tabel powerful Filament (bisa pakai package activity log seperti `spatie/laravel-activitylog` + Filament resource).
- Filter by user, action type, date.
- Untuk location: Kolom "Lihat di Peta" yang buka modal dengan Leaflet map + multiple markers jika ada history.

---

## 6. Komponen Khusus & Polish

### 6.1 Empty States
Bukan teks polos. Gunakan ilustrasi modern (bisa dari https://undraw.co atau custom SVG) + headline ramah + CTA button. Contoh: "Belum ada data absensi hari ini" + tombol "Mulai Scan QR".

### 6.2 Loading & Skeleton
- Dashboard: Skeleton KPI cards + chart placeholder.
- Tabel: Animated skeleton rows.
- QR scan: Subtle shimmer di area kamera.

### 6.3 Notifications
Filament Notifications + custom:
- Success clock in: Hijau + ikon check + nama karyawan.
- Pending approval: Amber dengan jumlah.
- Payroll ready: Link langsung ke halaman.

### 6.4 Responsive & Touch
- Semua tombol minimal 44x44px di mobile.
- Tabel: Horizontal scroll atau card collapse di mobile.
- Check-in page: Sangat touch friendly, besar.

### 6.5 Accessibility
- ARIA labels, keyboard navigation, high contrast mode, screen reader friendly (Filament sudah bagus di sini).

---

## 7. Customization & Theme Filament (Praktis)

1. Install Filament.
2. Publish & edit `filament.php` config + custom CSS di `resources/css/filament/admin/theme.css`.
3. Tambahkan custom classes untuk:
   - Glass effect pada modal: `backdrop-blur-xl bg-white/90 dark:bg-slate-900/90`
   - Elevated cards.
   - Custom button styles.
4. Buat custom page untuk Check-in (bukan resource standar) agar full control desain.
5. Gunakan Filament plugins: Shield (permission), Filament Spatie Media Library (foto), dll.

---

## 8. Testing Visual & Iterasi

- **Design Review Checklist:**
  - [ ] Semua status pakai warna + ikon konsisten.
  - [ ] Hover & active states terasa responsif.
  - [ ] Tidak ada elemen yang "mengambang" tanpa shadow/depth.
  - [ ] Mobile view tidak pecah (test di HP real atau DevTools).
  - [ ] Animasi tidak lag di perangkat menengah.
- **User Testing:** Libatkan 1-2 Security/Cleaning Service asli untuk test flow QR scan.
- **Iterasi:** Mulai dengan Filament default (sudah bagus), lalu tambah polish bertahap berdasarkan feedback.

---

## 9. Kesimpulan & Rekomendasi Akhir

Desain ini **modern, interaktif, memiliki kedalaman visual, dan penuh micro-interactions** tanpa terasa berlebihan atau generic AI. Dengan **FilamentPHP + Livewire + Tailwind + Alpine**, Anda bisa membangunnya dengan sangat cepat (MVP dalam hitungan minggu) sambil tetap mendapatkan kualitas UI/UX setara produk SaaS enterprise.

**Langkah Selanjutnya yang Disarankan:**
1. Setup Laravel + Filament + Shield.
2. Buat Resource Employee + Attendance dasar.
3. Implementasikan halaman Check-in custom + QR flow (ini yang paling "wow").
4. Tambah geolocation + Leaflet.
5. Lanjutkan ke Cuti, Shift, Payroll bertahap.
6. Polish theme & animations di akhir.

Dokumen ini melengkapi `requirements.md` secara sempurna. Dengan keduanya, tim development memiliki panduan lengkap dari business requirement hingga visual & interaksi detail.

---

**Butuh bantuan lebih lanjut?**  
Saya bisa buatkan:
- Struktur folder Laravel + contoh code snippet penting (Check-in page, Payroll calculator service, QR controller, dll.)
- ERD lengkap (Mermaid atau dbdiagram.io)
- Wireframe sederhana deskripsi
- Atau langsung mulai coding skeleton proyek

Silakan beri instruksi berikutnya! 🚀