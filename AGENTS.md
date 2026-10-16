# Panduan Kerja Proyek LKS Santri Karya

Dokumen ini wajib dibaca sepenuhnya pada awal setiap sesi kerja. Aturan di bawah berlaku bagi semua agen dan kontributor proyek.

## Penggunaan skill wajib

1. **Pilih skill Matt Pocock yang paling relevan sebelum mulai.** Untuk setiap tugas software engineering, baca seluruh `SKILL.md` dan ikuti workflow-nya. Pilih secara sempit; gunakan skill tambahan hanya bila menangani cakupan berbeda yang benar-benar diperlukan.
2. **Gunakan Ponytail untuk pekerjaan kode.** Gunakan sebagai standar analisis dependency dan blast radius. Gunakan level `full` untuk perubahan perilaku, aturan bisnis, aliran data, permission, kode bersama, bug, atau refactor; perubahan trivial dianalisis secara proporsional.
3. **Pertahankan guardrail proyek.** Ponytail tidak menggantikan Graphify, Impeccable, validasi, keamanan, aksesibilitas, atau pengujian proporsional. Pada handoff, laporkan hal yang belum diperiksa/dikerjakan beserta risiko yang tersisa.

## Aturan kerja wajib

1. **Baca `AGENTS.md` terlebih dahulu.** Pada awal setiap sesi, baca seluruh isi `AGENTS.md` sebelum melakukan analisis, perubahan, atau menjalankan perintah proyek lainnya.
2. **Gunakan Graphify untuk perubahan non-trivial.** Sebelum memahami atau mengubah kode maupun arsitektur yang non-trivial, jalankan pembaruan dan/atau kueri Graphify untuk memperoleh konteks proyek.
3. **Batasi pembacaan kode sumber mentah.** Baca source langsung hanya apabila konteks Graphify belum cukup.
4. **Perbarui indeks Graphify setelah perubahan.** Setiap perubahan proyek—termasuk kode, konfigurasi, dan dokumentasi—wajib diikuti dengan pembaruan indeks Graphify.
5. **Gunakan Impeccable untuk UI/UX.** Setiap pekerjaan yang membentuk, mengubah, atau meninjau antarmuka dan pengalaman pengguna wajib menggunakan skill Impeccable.
6. **Muat konteks Impeccable sebelum perubahan UI.** Jalankan `impeccable context` satu kali pada setiap sesi sebelum mengubah antarmuka, lalu ikuti seluruh arahannya.
7. **Perubahan minimum.** Pilih solusi paling sederhana yang memenuhi kebutuhan dan kriteria penerimaan sepenuhnya.
8. **Jaga scope dan perilaku yang ada.** Jangan melakukan refactor, cleanup, improvement, atau inisiasi di luar scope. Jangan mengubah kontrak API, URL, schema, permission, alur UX, nilai default, atau perilaku lain yang tidak diperlukan tugas. Ikuti arsitektur, convention, komponen, utility, dan dependency yang ada; jangan menambah dependency bila stack saat ini mencukupi.
9. **Uji secara proporsional.** Setiap perubahan wajib diuji sesuai tingkat risikonya; aturan bisnis, hak akses, dan perhitungan wajib memiliki pengujian yang relevan.
10. **Klarifikasi ambiguitas material.** Minta klarifikasi hanya bila aturan bisnis, permission, operasi destruktif, interpretasi data, scope, atau pilihan perilaku dapat mengubah hasil secara material. Gunakan codebase, test, dokumentasi, design system, dan convention yang ada untuk detail yang dapat ditentukan dengan aman.
11. **Cegah regresi atas bug yang sudah diperbaiki.** Setelah memperbaiki bug, telusuri pola, komponen, handler, atau alur yang sama di seluruh proyek. Terapkan perbaikan pada seluruh kasus terkait dan lakukan validasi aman yang membuktikan penyebab bug tidak masih muncul di jalur lain. Jangan menganggap masalah bersifat satu halaman apabila implementasinya berbagi pola yang sama.

## Performa

Optimasi hanya jika relevan terhadap tugas atau ada bottleneck yang terbukti. Jangan mengorbankan simplicity demi micro-optimization. Gunakan urutan ini:

1. Hilangkan pekerjaan, query, request, atau render yang tidak diperlukan.
2. Optimalkan query dan hindari N+1.
3. Gunakan pagination untuk dataset besar.
4. Minimalkan payload API dan deduplikasi request.
5. Gunakan cache bila data sering dibaca dan invalidasinya jelas.
6. Lazy-load komponen atau media berat dan optimalkan gambar.
7. Tambahkan database index berdasarkan access pattern nyata.
8. Gunakan code splitting, memoization, CDN, atau background job hanya bila diperlukan.

## Keselamatan data

Data yang ada tidak pernah dianggap disposable, termasuk database lokal. Tanpa persetujuan eksplisit pengguna, agen dilarang menjalankan atau menambahkan:

- reset atau recreate database;
- `migrate:fresh`, database reset, `DROP DATABASE`, atau `DROP SCHEMA`;
- truncate atau penghapusan massal;
- penghapusan Docker volume database;
- destructive seed atau destructive migration;
- perubahan pada migration lama yang pernah dipakai di shared, staging, atau production.

Gunakan migration forward-only dan non-destruktif secara default. Sebelum migration, seed, atau mutasi data berisiko: verifikasi environment dan target database, pahami dampak terhadap data yang ada, lalu pilih alternatif non-destruktif. Bila kehilangan data tidak dapat dihindari, berhenti, jelaskan risikonya, dan minta persetujuan eksplisit.

Deploy, build, startup, CI/CD, Dockerfile, entrypoint, dan post-deploy tidak boleh berisi reset database.

## Validasi dan dokumentasi

1. Uji secara proporsional terhadap risiko. Prioritaskan aturan bisnis, permission, perhitungan, mutasi data, migration, perilaku bersama, dan regresi bug.
2. Jangan mengklaim sesuatu tervalidasi jika perintah atau pemeriksaannya belum benar-benar dijalankan.
3. Jika Graphify, Impeccable, test, build, atau tool wajib gagal: laporkan perintah dan error yang relevan; jangan mengklaim proses berhasil, membuat hasil tiruan, atau memasang tool pengganti tanpa kebutuhan dan persetujuan.
4. Perubahan product atau aturan bisnis: perbarui `PRODUCT.md` dan `docs/PRD.md`.
5. Perubahan arsitektur, dependency, integrasi, data, atau deployment: perbarui `docs/TECH-STACK.md` dan `docs/ARCHITECTURE.md`.
6. Perubahan UI/UX: perbarui `DESIGN.md`.
7. Perubahan database: perbarui schema, migration, dan dokumentasi deployment yang relevan.
8. Baca dokumentasi yang relevan terhadap tugas; jangan memuat seluruh dokumentasi tanpa alasan.

## Prioritas ketika aturan bertentangan

1. Keselamatan data
2. Security dan permission
3. Correctness
4. Scope dan acceptance criteria
5. Perilaku yang ada
6. Arsitektur yang ada
7. Pencegahan regresi
8. Testing
9. Performance
10. Cleanup dan improvement

Prinsip akhir: **Understand → Trace → Change minimally → Protect data → Test → Validate → Stop.**

## Prosedur minimum

1. Baca dokumen ini secara penuh.
2. Jalankan Graphify untuk mendapatkan konteks terbaru.
3. Tentukan scope yang disetujui dan lakukan perubahan minimum yang diperlukan.
4. Untuk pekerjaan UI, jalankan Impeccable sesuai aturan di atas sebelum mengubah antarmuka.
5. Setelah memperbaiki bug, periksa seluruh penggunaan pola atau alur yang sama untuk mencegah regresi.
6. Jalankan pengujian yang relevan.
7. Jalankan pembaruan Graphify setelah semua perubahan selesai.

