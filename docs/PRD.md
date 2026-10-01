# Product Requirements Document

## Sistem LKS Santri Karya

| Atribut | Nilai |
|---|---|
| Versi | 1.0 |
| Tanggal | 28 September 2026 |
| Status | Draft |
| Produk | LKS Santri Karya |

## 1. Latar belakang

LKS Santri Karya digunakan untuk mencatat dan memantau aktivitas spiritual Santri Karya secara berkala. Saat ini proses dilakukan melalui Google Sheets: setiap aktivitas dicatat sebagai checklist harian, lalu dihitung menjadi nilai individu dan direkap menurut kelompok, gender, leader, hingga departemen.

Pengelolaan spreadsheet membutuhkan rekap manual dan akan semakin sulit dikendalikan saat jumlah pengguna serta periode bertambah. Sistem ini mendigitalisasi alur tersebut tanpa mengubah konsep LKS yang telah berjalan.

## 2. Tujuan produk

1. Memudahkan Santri Karya mencatat aktivitas spiritual harian.
2. Menghitung capaian dan nilai LKS secara otomatis.
3. Menampilkan status ketuntasan setiap aktivitas.
4. Menyediakan rekap nilai individu.
5. Menyediakan rekap berdasarkan kelompok atau struktur organisasi.
6. Menampilkan capaian spiritual tingkat departemen.
7. Mengurangi ketergantungan pada pengelolaan Google Sheets manual.

## 3. Ruang lingkup versi pertama

- Pengelolaan data Santri Karya.
- Pengelolaan periode LKS.
- Pengelolaan komponen dan target aktivitas spiritual.
- Pengisian aktivitas harian.
- Perhitungan capaian, nilai, dan status Tuntas/Belum Tuntas.
- Rekap individu, Ikhwan, Akhwat, leader, dan departemen.
- Grafik capaian spiritual departemen serta riwayat periode.

Sistem tidak mencakup proses di luar LKS.

## 4. Pengguna dan hak akses

### 4.1 Santri Karya

Santri Karya mencatat aktivitas harian dan melihat LKS pribadi.

- Melihat LKS pribadi.
- Mengisi checklist aktivitas.
- Melihat capaian, nilai, dan status ketuntasan.
- Melihat riwayat pribadi sesuai hak akses.

### 4.2 Leader

Leader melihat hasil anggota yang berada di bawah tanggung jawabnya.

- Melihat rekap anggota.
- Melihat nilai, persentase capaian, dan status ketuntasan anggota.

### 4.3 Admin

Admin mengelola konfigurasi serta data utama sistem.

- Mengelola data Santri Karya dan struktur organisasinya.
- Mengelola periode, aktivitas, dan target LKS.
- Melihat seluruh rekap dan laporan departemen.

## 5. Data Santri Karya

Data minimum:

| Data | Keterangan |
|---|---|
| Nama | Nama Santri Karya |
| Gender | Ikhwan atau Akhwat |
| Tim | Tim pengguna |
| Leader | Leader pengguna |
| Departemen | Departemen pengguna |
| Kategori | Kategori LKS, apabila digunakan |
| Level | Level pengguna, apabila digunakan |
| Status | Aktif atau Tidak Aktif |

## 6. Periode LKS

LKS berjalan dalam periode tertentu, umumnya bulanan, misalnya September 2026. Setiap periode memiliki tanggal mulai, tanggal selesai, daftar aktivitas, target tiap aktivitas, dan status.

| Status | Makna |
|---|---|
| Aktif | Checklist dapat diisi dan perhitungan berjalan. |
| Selesai | Periode ditutup; data tetap tersedia sebagai riwayat. |

## 7. Aktivitas spiritual

Aktivitas awal yang dicatat:

1. Absen Subuh
2. Shalawat Munjiyat
3. Shalat Tepat Waktu di Jam Kerja
4. Dzikir Pagi Petang
5. Tilawah Harian
6. Shalat Dhuha
7. Tahajud
8. Tilawati
9. Subuhan di Masjid
10. Puasa Kamis

Admin dapat mengatur aktivitas aktif dan target pada tiap periode sesuai kebijakan LKS.

## 8. Pengisian LKS harian

Santri Karya membuka periode aktif. Sistem menampilkan daftar aktivitas menurut tanggal, lalu pengguna mencentang aktivitas yang sudah dilakukan. Checklist tersimpan menurut pengguna, aktivitas, tanggal, dan periode; data tersebut menjadi dasar semua perhitungan.

| Tanggal | Dhuha | Tahajud | Tilawah | Dzikir | Subuh |
|---|---|---|---|---|---|
| 1 Sep | Ya | Ya | Ya | Ya | Ya |
| 2 Sep | Ya | Tidak | Ya | Ya | Ya |
| 3 Sep | Ya | Ya | Ya | Ya | Ya |

## 9. Perhitungan capaian dan nilai

Sistem membandingkan jumlah aktivitas yang selesai dengan target aktivitas.

Contoh: target Shalat Dhuha 20 kali dan capaian 18 kali menghasilkan capaian `18 / 20 × 100 = 90%`.

Setiap aktivitas memiliki status:

| Status | Kondisi |
|---|---|
| Tuntas | Capaian memenuhi target aktivitas. |
| Belum Tuntas | Capaian belum memenuhi target aktivitas. |

Nilai aktivitas digabungkan menjadi nilai akhir LKS. Standar ketercapaian keseluruhan saat ini adalah 90%; sistem menampilkan nilai akhir, persentase akhir, dan status ketercapaian periode.

## 10. Halaman LKS individu

Halaman LKS pribadi menampilkan:

- Identitas: nama, tim, leader, departemen, periode, kategori, dan level.
- Capaian: aktivitas, target, jumlah capaian, persentase, nilai, serta status ketuntasan.
- Hasil akhir: total nilai, persentase akhir, dan status ketercapaian.

## 11. Rekap

### Rekap Ikhwan dan Akhwat

Sistem menyediakan rekap terpisah dengan data minimum berikut.

| Nama | Tim | Leader | Nilai | Status |
|---|---|---|---:|---|
| Pengguna A | Tim A | Leader A | 92% | Tuntas |
| Pengguna B | Tim A | Leader A | 78% | Belum Tuntas |

### Rekap leader

Leader melihat anggota yang menjadi tanggung jawabnya, termasuk jumlah anggota, nilai masing-masing anggota, persentase capaian, dan status ketuntasan.

### Rekap departemen

Sistem menggabungkan hasil LKS berdasarkan departemen sebagai dasar laporan capaian spiritual organisasi.

| Departemen | Capaian |
|---|---:|
| Marketing | 91% |
| Finance | 88% |
| Production | 94% |
| IT | 90% |

### Grafik departemen

Grafik **Capaian Spiritual Departemen** menampilkan capaian setiap departemen menurut periode dan dapat membandingkan riwayat ketika data tersedia.

| Departemen | Jul | Agu | Sep |
|---|---:|---:|---:|
| Marketing | 86% | 89% | 91% |
| Finance | 82% | 85% | 88% |
| Production | 90% | 92% | 94% |

## 12. Alur utama

```text
Admin membuat periode
  → Admin menentukan aktivitas dan target
  → Santri Karya mengisi aktivitas harian
  → Sistem menghitung capaian dan status aktivitas
  → Sistem menghitung nilai individu
  → Rekap Ikhwan/Akhwat
  → Rekap Leader
  → Rekap Departemen dan grafik
```

## 13. Functional requirements

| ID | Kebutuhan |
|---|---|
| FR-01 | Pengguna dapat login dengan akun terdaftar. |
| FR-02 | Admin dapat menambah, mengubah, menonaktifkan Santri Karya serta menetapkan tim, leader, dan departemen. |
| FR-03 | Admin dapat membuat, mengatur tanggal, mengaktifkan, dan menutup periode LKS. |
| FR-04 | Admin dapat mengatur nama aktivitas, target, dan status aktif. |
| FR-05 | Santri Karya dapat mencentang aktivitas berdasarkan tanggal. |
| FR-06 | Checklist pengguna tersimpan di sistem. |
| FR-07 | Sistem menghitung capaian setiap aktivitas secara otomatis. |
| FR-08 | Sistem menentukan status Tuntas atau Belum Tuntas. |
| FR-09 | Sistem menghitung nilai LKS individu secara otomatis. |
| FR-10 | Sistem menyediakan rekap Ikhwan. |
| FR-11 | Sistem menyediakan rekap Akhwat. |
| FR-12 | Sistem menyediakan rekap anggota berdasarkan leader. |
| FR-13 | Sistem menyediakan capaian berdasarkan departemen. |
| FR-14 | Sistem menampilkan grafik capaian spiritual departemen per periode. |
| FR-15 | Pengguna berhak dapat melihat data periode sebelumnya. |

## 14. Business rules

| ID | Aturan |
|---|---|
| BR-01 | Satu pengguna memiliki satu data LKS dalam satu periode. |
| BR-02 | Checklist hanya dapat dilakukan pada periode aktif. |
| BR-03 | Setiap aktivitas dapat memiliki target berbeda. |
| BR-04 | Nilai dihitung otomatis dari checklist. |
| BR-05 | Status aktivitas ditentukan oleh pencapaian target. |
| BR-06 | Standar ketercapaian keseluruhan mengikuti konfigurasi LKS yang berlaku. |
| BR-07 | Rekap departemen bersumber dari data LKS anggota departemen tersebut. |
| BR-08 | Data periode selesai tetap disimpan sebagai riwayat. |

## 15. Halaman minimum

1. Login
2. Dashboard
3. LKS Saya
4. Riwayat LKS
5. Rekap Ikhwan
6. Rekap Akhwat
7. Rekap Leader
8. Rekap Departemen
9. Data Santri Karya
10. Pengaturan Periode
11. Pengaturan Aktivitas

## 16. Dashboard

Untuk Santri Karya, dashboard menampilkan periode aktif, progres LKS, nilai sementara, dan aktivitas yang belum mencapai target.

Untuk Leader dan Admin, dashboard menampilkan jumlah peserta, rata-rata capaian, jumlah Tuntas, jumlah Belum Tuntas, dan capaian departemen.

## 17. Non-functional requirements

- Dapat digunakan melalui browser desktop dan smartphone.
- Memiliki tampilan sederhana serta pengisian checklist yang cepat.
- Menjaga data antar pengguna terpisah sesuai hak akses.
- Menyimpan riwayat setiap periode.

## 18. Acceptance criteria

Versi awal diterima apabila:

1. Santri Karya dapat melakukan checklist aktivitas.
2. Checklist tersimpan menurut tanggal dan periode.
3. Sistem menghitung capaian secara otomatis.
4. Sistem menampilkan status Tuntas/Belum Tuntas.
5. Sistem menghasilkan nilai LKS individu.
6. Sistem membuat rekap Ikhwan dan Akhwat.
7. Leader dapat melihat rekap anggotanya.
8. Sistem menghasilkan rekap departemen dan grafiknya.
9. Data periode sebelumnya dapat dilihat kembali.

## 19. Batasan versi pertama

Gamification, AI, notifikasi kompleks, integrasi eksternal, approval bertingkat, sistem reward, dan fitur sosial tidak termasuk scope versi pertama.

## 20. Ringkasan

LKS Santri Karya mendigitalisasi alur: **input aktivitas harian → hitung capaian → nilai individu → rekap leader → rekap departemen → laporan capaian spiritual**. Fokus versi pertama adalah alur yang lebih mudah digunakan, terstruktur, dan tidak bergantung pada spreadsheet manual.

