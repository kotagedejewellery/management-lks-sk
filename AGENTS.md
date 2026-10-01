# Panduan Kerja Proyek LKS Santri Karya

Dokumen ini wajib dibaca sepenuhnya pada awal setiap sesi kerja. Aturan di bawah berlaku bagi semua agen dan kontributor proyek.

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

## Prosedur minimum

1. Baca dokumen ini secara penuh.
2. Jalankan Graphify untuk mendapatkan konteks terbaru.
3. Tentukan scope yang disetujui dan lakukan perubahan minimum yang diperlukan.
4. Untuk pekerjaan UI, jalankan Impeccable sesuai aturan di atas sebelum mengubah antarmuka.
5. Setelah memperbaiki bug, periksa seluruh penggunaan pola atau alur yang sama untuk mencegah regresi.
6. Jalankan pengujian yang relevan.
7. Jalankan pembaruan Graphify setelah semua perubahan selesai.

