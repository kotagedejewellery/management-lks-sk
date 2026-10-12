# Panduan Admin Menyiapkan LKS

Panduan ini menjelaskan urutan pengisian menu **Pengaturan** dari database yang baru dibersihkan sampai Santri Karya dapat mulai mencatat LKS. Lakukan seluruh langkah di bawah sebelum membuat periode aktif.

## Hasil yang harus tercapai

Sebelum Santri Karya mulai mengisi LKS, sistem harus memiliki:

- Departemen dan tim aktif.
- Akun Santri Karya, termasuk Leader tim, dengan penempatan yang benar.
- Aktivitas LKS beserta aturan defaultnya.
- Kalender kerja untuk tahun periode.
- TTD digital General Manager dan Kabid. Pengembangan Spiritual.
- Satu periode berstatus **Aktif** dengan aktivitas periode yang sudah diperiksa. Saat aktivasi, seluruh Santri Karya aktif akan otomatis menjadi peserta periode.

> **Urutan penting:** buat struktur organisasi sebelum akun Santri Karya. Buat dan periksa seluruh konfigurasi periode ketika masih **Draft**. Setelah periode diaktifkan, konfigurasi aktivitas dan hari libur periode dikunci untuk menjaga nilai serta riwayat.

## 1. Buka Pengaturan

Masuk dengan akun Admin, lalu buka **Pengaturan**. Area yang perlu diisi adalah:

1. Struktur Organisasi
2. Data Santri Karya
3. Aktivitas LKS
4. Kalender Kerja
5. Dokumen & Tanda Tangan
6. Periode LKS

Urutan pada daftar tersebut adalah urutan kerja yang direkomendasikan.

## 2. Isi Struktur Organisasi

Buka **Pengaturan → Struktur Organisasi**.

1. Klik **Tambah departemen**.
2. Isi **kode departemen** yang singkat dan unik, misalnya `MKT`.
3. Isi nama departemen, misalnya `Marketing`.
4. Simpan, lalu klik **Tambah tim** pada departemen tersebut.
5. Isi **kode tim** yang unik dan nama tim, misalnya `MKT-DIG` dan `Digital Marketing`.
6. Ulangi sampai seluruh departemen dan tim tersedia.

### Menetapkan Leader tim

Leader ditetapkan pada **tim**, bukan dipilih satu per satu untuk setiap anggota.

1. Buat terlebih dahulu akun orang yang akan menjadi Leader pada menu **Data Santri Karya** dengan level jabatan **Leader**.
2. Kembali ke **Struktur Organisasi** dan klik **Edit** pada tim terkait.
3. Pilih orang tersebut pada pilihan **Leader Tim**, lalu simpan.

Anggota yang ditempatkan pada tim tersebut akan mengikuti Leader Tim yang sama. Jika Leader berubah ketika periode sedang aktif, data tanggung jawab anggota pada periode aktif akan diperbarui agar rekap Leader tetap tepat.

### Aturan pengelolaan struktur

- Gunakan **Arsipkan** untuk departemen atau tim yang tidak lagi dipakai tetapi sudah memiliki data atau riwayat.
- Hapus hanya struktur yang benar-benar belum digunakan. Data yang telah mempunyai anggota atau riwayat periode tidak boleh dihapus.
- Jangan menonaktifkan departemen sebelum seluruh tim aktif di dalamnya ditangani, dan jangan menonaktifkan tim yang masih memiliki Santri Karya aktif.

## 3. Buat akun Santri Karya

Buka **Pengaturan → Data Santri Karya**. Pastikan tim tujuan sudah dibuat dan masih aktif.

Untuk setiap orang:

1. Klik **Tambah Santri Karya**.
2. Isi nama dan alamat email yang aktif serta belum dipakai akun lain.
3. Buat password sementara minimal 8 karakter dan sampaikan secara pribadi kepada pemilik akun.
4. Pilih gender: **Ikhwan** atau **Akhwat**.
5. Pilih tim. Departemen akan mengikuti tim yang dipilih.
6. Pilih level jabatan: **Staff** atau **Leader**.
7. Simpan.

Pada login pertama, pengguna wajib mengganti password sementara. Setelah akun Leader dibuat, ingat untuk menetapkannya juga sebagai **Leader Tim** pada langkah sebelumnya.

Gunakan **Edit** untuk memindahkan tim, mengubah level, atau menonaktifkan akun. Akun yang sudah masuk riwayat periode sebaiknya dinonaktifkan, bukan dihapus.

## 4. Atur Aktivitas LKS dan aturan default

Buka **Pengaturan → Aktivitas LKS**. Aktivitas di halaman ini adalah master atau aturan default untuk periode baru.

Untuk setiap aktivitas, klik **Tambah aktivitas** dan isi:

| Data | Cara mengisi |
|---|---|
| Kode | Kode unik yang tidak berubah, misalnya `ABSEN_SUBUH`. |
| Nama aktivitas | Nama yang akan terlihat oleh Santri Karya. |
| Target maksimum | Jumlah capaian yang bernilai 100%. |
| Target minimal tuntas | Jumlah capaian minimum agar status aktivitas menjadi **Tuntas**. Nilainya tidak boleh melebihi target maksimum. |
| Maksimal per pekan | Isi bila aktivitas memiliki kuota mingguan; kosongkan bila tidak ada batas mingguan. |
| Jadwal pencatatan | Pilih hari yang diperbolehkan. Kosongkan seluruh pilihan bila aktivitas boleh dicatat setiap hari. |
| Berlaku untuk | Pilih gender dan level yang menjadi sasaran aktivitas. |
| Status aktif | Aktifkan hanya aktivitas yang akan menjadi pilihan default untuk periode baru. |

Nilai aktivitas dihitung otomatis dari capaian dibanding target maksimum dan dibatasi sampai 100%. Status **Tuntas** mengikuti target minimal, bukan harus selalu mencapai 100%.

### Mengelola status dan penghapusan aktivitas

- Jadikan aktivitas **Nonaktif** jika tidak ingin dipakai pada periode baru, tetapi ingin mempertahankan riwayatnya.
- Aktivitas yang hanya dipakai pada periode **Draft** masih dapat dihapus. Sistem akan menghapusnya juga dari draft terkait.
- Aktivitas yang sudah dipakai periode **Aktif** atau **Selesai** tidak dapat dihapus. Nonaktifkan saja.
- Mengubah aturan master tidak mengubah periode yang sudah aktif atau selesai; setiap periode menyimpan snapshot aturannya sendiri.

## 5. Isi Kalender Kerja

Buka **Pengaturan → Kalender Kerja**. Atur kalender per tahun sebelum membuat periode untuk tahun tersebut.

1. Pilih tahun yang akan digunakan, misalnya `2026`.
2. Tambahkan satu per satu melalui **Tambah hari libur**, atau gunakan **Impor daftar** untuk banyak tanggal.
3. Isi tanggal, nama hari libur, dan jenisnya: **Tanggal merah** atau **Cuti bersama**.
4. Untuk impor, gunakan satu baris per tanggal dengan format:

   ```text
   YYYY-MM-DD, Nama hari libur, tanggal merah
   YYYY-MM-DD, Nama hari libur, cuti bersama
   ```

Tanggal merah dan cuti bersama membuat checklist pada tanggal tersebut menjadi opsional dan tidak memengaruhi nilai LKS. Data dengan tanggal yang sama diperbarui, bukan digandakan.

> Kalender Kerja adalah referensi tahunan. Ketika periode Draft dibuat, hari libur yang berada di dalam rentang tanggal periode akan disalin sebagai snapshot periode. Jika Kalender Kerja diperbarui setelah draft dibuat, buka detail periodenya lalu gunakan **Salin ulang kalender kerja**.

## 6. Kelola Dokumen dan Tanda Tangan

Buka **Pengaturan → Dokumen & Tanda Tangan**. Halaman ini berisi dua pejabat yang menggunakan TTD digital pada PDF LKS:

- General Manager
- Kabid. Pengembangan Spiritual

Untuk masing-masing pejabat:

1. Klik **Kelola**.
2. Isi nama pejabat yang sedang menjabat.
3. Unggah file TTD digital berformat PNG (maksimal 2 MB; PNG transparan direkomendasikan).
4. Simpan dan pastikan kartu menampilkan status **TTD digital tersedia**.

TTD Santri Karya dan Leader pada PDF tetap disiapkan untuk tanda tangan manual. Kedua TTD digital pejabat wajib sudah tersedia sebelum periode dapat diaktifkan. Konfigurasi yang berlaku akan disimpan sebagai snapshot saat aktivasi periode.

## 7. Buat dan lengkapi periode Draft

Buka **Pengaturan → Periode LKS**, lalu klik **Tambah periode**.

1. Isi nama periode, misalnya `Oktober 2026`.
2. Isi tanggal mulai dan tanggal selesai.
3. Isi ambang nilai akhir Leader dan Staff sesuai kebijakan periode. Nilai awal yang umum digunakan adalah 90% untuk Leader dan 85% untuk Staff.
4. Simpan. Status periode baru adalah **Draft**.

Saat draft dibuat, sistem menyalin aktivitas master aktif dan hari libur Kalender Kerja yang tanggalnya berada dalam rentang periode. Salinan ini dapat diubah khusus pada draft tanpa mengubah master.

### Periksa aktivitas periode

Pada detail periode Draft:

1. Klik **Kelola aktivitas**.
2. Pastikan seluruh aktivitas yang seharusnya berlaku sudah ada; periksa jumlah aktivitas dengan daftar pada Aktivitas LKS.
3. Untuk tiap aktivitas, periksa target maksimum, target minimal, batas per pekan, jadwal, sasaran gender/level, serta status aktifnya.
4. Klik **Tambah aktivitas** bila ada aktivitas master yang belum dimasukkan ke draft.
5. Jangan melanjutkan aktivasi bila muncul peringatan aktivitas master aktif belum masuk ke periode. Kembali ke **Kelola aktivitas** dan putuskan secara sadar apakah aktivitas tersebut memang tidak berlaku.

Aktivitas duplikat tidak boleh ditambahkan ke periode yang sama.

### Periksa hari efektif dan libur periode

Di bagian **Hari efektif dan libur** pada detail Draft:

1. Pastikan jumlah hari libur sesuai rentang periode.
2. Jika kalender tahunan baru diperbarui, klik **Salin ulang kalender kerja**.
3. Tambahkan hari libur khusus periode melalui **Tambah hari libur**, atau gunakan **Impor daftar** bila perlu.
4. Hapus hanya hari libur snapshot yang memang tidak berlaku untuk periode tersebut.

Hanya tanggal yang berada di dalam rentang periode yang dapat masuk ke snapshot. Karena itu, misalnya daftar hari libur Januari tidak akan muncul pada periode Oktober.

### Peserta periode

Peserta tidak dipilih satu per satu ketika periode masih Draft. Saat tombol **Aktifkan** ditekan, sistem otomatis membuat peserta periode untuk seluruh akun Santri Karya yang berstatus **Aktif**.

Karena itu, sebelum aktivasi pastikan setiap orang yang akan ikut sudah aktif dan data tim, departemen, Leader, gender, serta levelnya benar. Akun yang berstatus nonaktif tidak akan dimasukkan.

Apabila ada Santri Karya baru setelah periode berjalan, Admin dapat menambahkannya dari bagian **Peserta periode** pada periode **Aktif**. Peserta tersebut hanya dapat mencatat mulai tanggal ia ditambahkan; sistem tidak membuat checklist mundur untuk tanggal sebelumnya. Peserta yang belum memiliki checklist juga dapat dikeluarkan dari periode aktif bila ditambahkan keliru.

## 8. Checklist sebelum klik Aktifkan

Gunakan checklist berikut pada periode **Draft**:

- [ ] Semua departemen, tim, dan Leader Tim benar.
- [ ] Semua akun Santri Karya aktif dan sudah ditempatkan pada tim yang benar.
- [ ] Seluruh Santri Karya yang akan mengisi berstatus aktif dan data penempatannya sudah benar; mereka akan menjadi peserta otomatis saat aktivasi.
- [ ] Aktivitas periode lengkap, aktif bila diperlukan, serta target dan sasaran pesertanya benar.
- [ ] Kalender periode telah memuat tanggal merah/cuti bersama dalam rentang periode.
- [ ] Nama dan PNG TTD digital General Manager serta Kabid. Pengembangan Spiritual sudah tersedia.
- [ ] Nama, tanggal, dan ambang nilai periode sudah diperiksa.

Setelah semua benar, klik **Aktifkan** dan konfirmasi. Sistem akan mengunci konfigurasi periode, menetapkannya sebagai periode aktif, dan peserta dapat mulai mengisi checklist LKS.

## 9. Setelah periode aktif

- Santri Karya hanya dapat mengisi aktivitas yang berlaku untuk dirinya, pada jadwal dan tanggal yang diizinkan.
- Admin dapat menambahkan Santri Karya baru ke periode aktif bila diperlukan. Catatan pengguna baru dimulai dari tanggal penambahan dan tidak berlaku mundur.
- Hari libur tetap dapat dicentang bila aktivitas dilakukan, tetapi tidak memengaruhi nilai.
- Admin memantau hasil dari Dashboard dan Rekap; koreksi data dilakukan melalui alur koreksi yang tersedia pada Rekap, bukan dengan mengubah aturan periode.
- Periode aktif dapat ditutup ketika seluruh pengisian dan koreksi selesai. Periode yang ditutup menjadi riwayat dan tidak dapat diubah.
- Untuk periode berikutnya, buat draft baru lalu ulangi pemeriksaan mulai dari aktivitas, kalender, dan peserta.

## 10. Uji siap pakai dari database bersih

Sebelum mengundang seluruh pengguna, lakukan uji singkat berikut dengan minimal satu Leader dan satu Staff:

1. Buat satu departemen, satu tim, satu akun Leader, dan satu akun Staff.
2. Tetapkan akun Leader sebagai Leader Tim.
3. Buat atau periksa aktivitas LKS, Kalender Kerja tahun berjalan, serta dua TTD digital.
4. Buat draft periode singkat, masukkan dua peserta, lalu aktifkan setelah checklist pada bagian sebelumnya terpenuhi.
5. Login sebagai masing-masing pengguna dan pastikan LKS Saya menampilkan aktivitas yang tepat.
6. Centang satu aktivitas dan satu pengisian beberapa tanggal bila tersedia; pastikan nilai dan rekap berubah.
7. Unduh PDF LKS dari akun Santri/Leader dan Admin untuk memastikan identitas serta TTD digital tampil benar.

Jika salah satu data inti belum tersedia—misalnya kartu TTD digital tidak muncul, pilihan Leader/role tidak tersedia, atau periode tidak dapat dibuat—jangan membuat data pengganti secara manual di luar aplikasi. Catat pesan error dan eskalasikan ke tim teknis, karena data referensi sistem mungkin belum disiapkan setelah pembersihan database.
