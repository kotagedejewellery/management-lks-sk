# Tech Stack — LKS Santri Karya

## 1. Keputusan utama

LKS Santri Karya dibangun sebagai aplikasi web **Laravel modular monolith**. Backend dan antarmuka berada dalam satu repositori serta satu deployment. Pendekatan ini sengaja dipilih untuk MVP internal: alur bisnis dan kontrol akses berada di satu tempat, deployment sederhana, dan tidak ada biaya koordinasi API/SPA terpisah.

| Lapisan | Pilihan | Peran |
|---|---|---|
| Bahasa backend | PHP 8.4.1+ | Versi minimum aktual dari dependensi Laravel yang terkunci. |
| Framework | Laravel 13 | Routing, autentikasi, ORM, validasi, Policy, migrasi, scheduler, dan testing. |
| Interaktivitas UI | JavaScript vanilla | Checklist, filter, modal administrasi, dan pemuatan data parsial melalui endpoint internal. |
| Rendering | Blade + prototype statis | Blade melayani shell terautentikasi; `frontend/app.js` dan CSS mengisi antarmuka operasi. |
| Styling | CSS kustom | Token visual dan breakpoint berada pada `frontend/styles.css` serta `frontend/overrides.css`. |
| JavaScript bundler | Tidak digunakan saat ini | Aset prototype disajikan oleh `PrototypeDashboardController`; Vite belum menjadi bagian runtime. |
| Database | PostgreSQL 18 | Data relasional, constraint, agregasi, dan JSONB audit. |
| Grafik | CSS/HTML | Grafik batang dan perbandingan periode dirender langsung dari data endpoint; Chart.js belum digunakan. |
| Test | Pest | Feature test, policy test, dan unit test perhitungan. |
| Lingkungan dev | Docker Compose | Menyamakan PHP, PostgreSQL, dan Node antar mesin. |
| Web server produksi | Nginx + PHP-FPM | Menjalankan aplikasi Laravel. |

Laravel menyediakan starter kit resmi untuk autentikasi. Aplikasi ini memakai UI JavaScript kustom di atas Blade, bukan Livewire. [Laravel Starter Kits](https://laravel.com/framework/docs/starter-kits).

## 2. Mengapa stack ini

### Laravel + JavaScript vanilla, bukan SPA terpisah

- LKS didominasi form, tabel, filter, dan perhitungan; kebutuhan ini cocok untuk shell Blade dengan interaksi parsial JavaScript.
- Satu model autentikasi dan otorisasi mengurangi duplikasi API token, validasi, dan state frontend.
- Developer dapat menulis aturan bisnis PHP sekali dan mengujinya langsung.
- Aplikasi tetap dapat menambahkan API terautentikasi jika aplikasi mobile benar-benar menjadi scope berikutnya.

### PostgreSQL, bukan spreadsheet atau database dokumen

- Struktur LKS memiliki relasi jelas: pengguna, struktur organisasi, periode, aktivitas, dan checklist.
- Constraint unik mencegah checklist ganda dan peserta ganda dalam satu periode.
- Agregasi rekap serta indeks komposit sesuai untuk laporan yang sering difilter.
- PostgreSQL adalah database relasional modern dengan dokumentasi resmi untuk fitur SQL dan administrasi. [Dokumentasi PostgreSQL](https://www.postgresql.org/docs/current/preface.html).

### CSS kustom + grafik HTML

- CSS kustom mempertahankan sistem tinta/kertas yang dipakai oleh prototype, tanpa dependency runtime tambahan.
- Grafik batang dan perbandingan periode dirender dari endpoint rekap yang telah terotorisasi; library chart baru dipertimbangkan jika kebutuhan visual tidak lagi dapat dipenuhi elemen HTML yang ada.

## 3. Paket dan kemampuan platform

| Kebutuhan | Solusi | Catatan |
|---|---|---|
| Login dan reset kata sandi | Laravel official starter kit | Hanya admin yang membuat/menonaktifkan akun; pendaftaran publik dinonaktifkan. |
| Otorisasi | Laravel Policies dan middleware peran | Policy menentukan kepemilikan LKS dan cakupan leader. |
| Validasi form | Laravel Form Request + validasi browser | Validasi server-side adalah sumber kebenaran. |
| Perhitungan nilai | `LksScoreCalculator` | Service PHP murni yang mudah diuji. |
| Tabel dan filter | JavaScript vanilla + pagination Laravel | Filter periode, gender, leader, dan departemen sesuai peran. |
| Grafik | CSS/HTML | Data grafik berasal dari endpoint/controller internal yang sudah terotorisasi. |
| Audit | Model `AuditLog` | Simpan event penting, bukan semua page view. |
| Date/time | Carbon + timezone aplikasi `Asia/Jakarta` | Tanggal checklist disimpan sebagai `date`; waktu audit dalam UTC. |
| Asset build | Tidak digunakan | Aset CSS/JS prototype disajikan langsung oleh controller aset. |

Tidak dipakai pada versi pertama: microservices, GraphQL, message broker, Redis wajib, event sourcing, aplikasi mobile native, Elasticsearch, atau workflow engine.

## 4. Standar engineering

### Backend

- PSR-12 dan Laravel Pint untuk format PHP.
- Eloquent untuk transaksi umum; query builder/SQL terukur hanya untuk agregasi rekap yang terbukti membutuhkan optimasi.
- Business rule berada di Service/Action dan Policy, bukan di Blade atau controller.
- Setiap perubahan schema menggunakan migration dan seeder minimal untuk role serta aktivitas awal.

### Frontend dan UX

- Blade sebagai shell default; JavaScript kustom pada `frontend/app.js` menangani interaksi yang membutuhkan data tanpa memuat ulang halaman.
- Semua halaman memenuhi mode **operate**: tugas utama terlihat jelas, status mudah dipindai, dan aksi berisiko meminta konfirmasi.
- Pengisian checklist dioptimalkan untuk layar 390 px dan desktop, dengan target sentuh memadai, indikator simpan, dan status berbasis teks, bukan warna saja.
- Seluruh pekerjaan UI mengikuti skill Impeccable dan aturan `AGENTS.md`.

### Testing

| Risiko | Jenis uji minimum |
|---|---|
| Rumus capaian/nilai/status | Pest unit test untuk target, bobot, pembatasan 100%, dan ambang 90%. |
| Akses data | Feature/Policy test untuk Santri, Leader, dan Admin. |
| Checklist | Feature test untuk periode aktif, tanggal di luar periode, aktivitas terlarang, dan constraint ganda. |
| Rekap | Feature test pada filter gender, leader, departemen, serta snapshot historis. |
| UI kritis | Browser/manual smoke test checklist di mobile dan desktop. |

## 5. Lingkungan lokal

```text
Docker Compose
├── app       PHP-FPM + Laravel
├── nginx     Web server lokal
├── postgres  Database aplikasi
└── node      Build asset saat diperlukan
```

Konfigurasi sensitif berada pada `.env`, tidak dikomit. Contoh environment yang diperlukan: `APP_KEY`, `APP_ENV`, `APP_URL`, `DB_*`, `MAIL_*`, dan timezone aplikasi.

## 6. Deployment awal

```text
Internet
  → HTTPS / Nginx
  → PHP-FPM / Laravel application
  → PostgreSQL
```

Kebutuhan produksi minimum:

- HTTPS dan redirect HTTP ke HTTPS.
- Backup PostgreSQL harian serta uji pemulihan berkala.
- `php artisan migrate --force` pada pipeline deployment.
- Scheduler Laravel aktif untuk pekerjaan rutin masa depan; tidak diperlukan worker queue permanen sebelum ada job asynchronous yang disetujui.
- Log aplikasi terpusat atau setidaknya rotasi log server.

## 7. Batas evolusi

API mobile, ekspor berat, notifikasi, atau laporan yang membutuhkan waktu lama dapat ditambahkan kemudian melalui queue dan API Laravel. Keduanya tidak dibangun lebih awal karena belum termasuk acceptance criteria MVP.

