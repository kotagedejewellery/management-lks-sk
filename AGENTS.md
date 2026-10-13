# Panduan Kerja Proyek LKS Santri Karya

Dokumen ini wajib dibaca sepenuhnya pada awal setiap sesi kerja. Aturan di bawah berlaku bagi semua agen dan kontributor proyek.

## Penggunaan skill wajib

1. **Pilih skill Matt Pocock yang paling relevan sebelum mulai.** Untuk setiap tugas software engineering, pilih satu skill global yang sesuai dengan jenis pekerjaan, baca seluruh `SKILL.md`, lalu ikuti alurnya. Pilih secara sempit; gunakan skill tambahan hanya bila cakupannya memang berbeda dan diperlukan.
2. **Gunakan Ponytail untuk pekerjaan kode.** Untuk implementasi, perbaikan bug, refactor, maupun review kode, gunakan Ponytail level `full` sebagai standar: petakan caller, test, fixture, konfigurasi, dan risiko pengguna; lalu buat perubahan paling kecil yang menyelesaikan kebutuhan sepenuhnya.
3. **Pertahankan guardrail proyek.** Ponytail tidak menggantikan Graphify, Impeccable, validasi, keamanan, aksesibilitas, atau pengujian proporsional yang diwajibkan di dokumen ini. Pada handoff, laporkan hal yang belum diperiksa/dikerjakan beserta risiko yang masih ada.

## Aturan kerja wajib

1. **Baca `AGENTS.md` terlebih dahulu.** Pada awal setiap sesi, baca seluruh isi `AGENTS.md` sebelum melakukan analisis, perubahan, atau menjalankan perintah proyek lainnya.
2. **Gunakan Graphify sebelum memahami kode atau arsitektur.** Jalankan pembaruan dan/atau kueri Graphify terlebih dahulu untuk memperoleh konteks proyek sebelum menelaah kode sumber atau arsitektur.
3. **Batasi pembacaan kode sumber mentah.** Baca kode sumber langsung hanya apabila konteks yang diperlukan tidak tersedia dari Graphify.
4. **Perbarui indeks Graphify setelah perubahan.** Setiap perubahan proyek—termasuk kode, konfigurasi, dan dokumentasi—wajib diikuti dengan pembaruan indeks Graphify.
5. **Gunakan Impeccable untuk UI/UX.** Setiap pekerjaan yang membentuk, mengubah, atau meninjau antarmuka dan pengalaman pengguna wajib menggunakan skill Impeccable.
6. **Muat konteks Impeccable sebelum perubahan UI.** Jalankan `impeccable context` satu kali pada setiap sesi sebelum mengubah antarmuka, lalu ikuti seluruh arahannya.
7. **Hindari overengineering.** Pilih solusi paling sederhana yang tetap memenuhi kebutuhan dan kriteria penerimaan yang disetujui.
8. **Jaga scope.** Jangan melakukan refactor, inisialisasi, improvement, atau perubahan lain di luar scope yang telah disetujui.
9. **Uji secara proporsional.** Setiap perubahan wajib diuji sesuai tingkat risikonya; aturan bisnis, hak akses, dan perhitungan wajib memiliki pengujian yang relevan.
10. **Klarifikasi ambiguitas.** Jika instruksi, data, atau scope ambigu dan dapat mengubah hasil kerja secara material, minta klarifikasi dan tunggu jawaban. Jangan membuat asumsi.
11. **Cegah regresi atas bug yang sudah diperbaiki.** Setelah memperbaiki bug, telusuri pola, komponen, handler, atau alur yang sama di seluruh proyek. Terapkan perbaikan pada seluruh kasus terkait dan lakukan validasi aman yang membuktikan penyebab bug tidak masih muncul di jalur lain. Jangan menganggap masalah bersifat satu halaman apabila implementasinya berbagi pola yang sama.

## Aturan optimasi performa wajib

1. **Caching.** Cache data yang sering dibaca agar tidak selalu mengakses database atau API; invalidasi cache ketika data sumber berubah.
2. **Pagination / Load More.** Jangan memuat ratusan atau ribuan record sekaligus; gunakan pagination atau load more di server.
3. **Lazy Loading.** Muat gambar, komponen berat, modal, chart, dan section non-kritis hanya saat dibutuhkan.
4. **Image Optimization.** Sediakan ukuran gambar sesuai penggunaan, gunakan WebP/AVIF bila didukung, kompres media, dan jangan kirim file gambar besar tanpa alasan.
5. **Database Indexing.** Index kolom yang digunakan pada `WHERE`, `JOIN`, `ORDER BY`, atau pencarian berdasarkan access pattern yang terbukti.
6. **Query Optimization.** Hindari N+1, query berulang, dan pengambilan seluruh kolom/relasi apabila tidak diperlukan.
7. **API Response Minimal.** Kirim hanya data yang digunakan frontend; jangan mengirim payload besar yang tidak dirender.
8. **Request Deduplication.** Jangan biarkan beberapa komponen meminta data yang sama berulang kali dalam satu alur tampilan.
9. **Server-side Caching / CDN.** Gunakan cache server dan CDN untuk halaman atau asset yang sering diakses, dengan tetap menjaga file privat tetap terlindungi.
10. **Code Splitting.** JavaScript yang tidak diperlukan halaman saat ini tidak boleh masuk ke initial bundle.
11. **Memoization Secukupnya.** Gunakan memoization hanya untuk perhitungan atau render yang telah terbukti berat; jangan pasang secara menyeluruh.
12. **Background Processing.** Jalankan export, email massal, generate report, dan pemrosesan file besar di queue/job agar tidak menahan request utama.

## Prosedur minimum

1. Baca dokumen ini secara penuh.
2. Jalankan Graphify untuk mendapatkan konteks terbaru.
3. Tentukan scope yang disetujui dan lakukan perubahan minimum yang diperlukan.
4. Untuk pekerjaan UI, jalankan Impeccable sesuai aturan di atas sebelum mengubah antarmuka.
5. Setelah memperbaiki bug, periksa seluruh penggunaan pola atau alur yang sama untuk mencegah regresi.
6. Jalankan pengujian yang relevan.
7. Jalankan pembaruan Graphify setelah semua perubahan selesai.

