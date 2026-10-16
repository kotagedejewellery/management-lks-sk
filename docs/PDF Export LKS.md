# Ekspor PDF LKS

## Akses dan preview

- Santri Karya dan Leader dapat membuka **Preview PDF LKS** miliknya sendiri dari **LKS Saya**, lalu mengunduhnya dari dialog preview.
- Admin dapat membuka preview **Rekap PDF** dari halaman **Laporan Periode** serta preview PDF individu dari baris peserta pada **Rekap**, lalu mengunduhnya dari dialog yang sama.
- Dokumen hanya tersedia untuk periode aktif atau periode tertutup. Semua nilai dihitung dari snapshot periode yang sama dengan rekap aplikasi.
- Preview dan unduhan memakai satu file PDF hasil ekspor yang sama. Hak akses dan endpoint ekspor tidak berubah.
- Bila sesi pengguna berakhir atau respons bukan PDF, preview menampilkan pesan pemulihan; pengguna perlu masuk kembali bila sesi telah habis.

## Dokumen dan verifikasi QR

Header PDF LKS individu memakai logo Yayasan Riyadhul Jannah Indonesia secara utuh di kiri dan logo Kotagede Jewellery secara utuh di kanan. Di tengah tercantum judul **Lembar Kendali Spiritual Santri Karya PT. Kotagede Jewellery Group**, dengan **Yayasan Riyadhul Jannah Indonesia** di bawahnya. Rentang periode tetap tersedia pada data identitas dokumen agar header tetap ringkas dan PDF tetap satu halaman.

PDF individu mencantumkan identitas pejabat berikut:

| Urutan | Label | Jabatan |
| --- | --- | --- |
| 1 | Disetujui oleh | General Manager |
| 2 | Diketahui oleh | Kabid. Pengembangan Spiritual |
| 3 | Diterima oleh | Santri Karya |

Leader Tim ditampilkan sebagai data identitas, bukan penandatangan dokumen. PDF rekap Admin mencantumkan identitas dua pejabat.

Pada setiap baris peserta, PDF rekap Admin juga mencantumkan kategori LKS (Reguler atau Magang) dan ambang nilai aktual peserta agar status Tuntas dapat ditelusuri.

Admin mengelola **nama dan jabatan** pejabat dari **Pengaturan → Dokumen & Verifikasi**. Tidak ada unggahan gambar TTD. Saat periode diaktifkan, sistem menyimpan snapshot nama dan jabatan yang berlaku; perubahan setelahnya hanya berlaku pada periode berikutnya.

PDF individu dari periode tertutup memuat QR verifikasi. Saat PDF tersebut pertama kali dibuat, sistem menerbitkan satu rekam verifikasi untuk kombinasi peserta dan periode; preview dan unduhan berikutnya memakai rekam yang sama. QR mengarah ke halaman publik yang menyatakan dokumen diterbitkan oleh sistem tanpa menampilkan identitas peserta atau nilai LKS.

QR verifikasi menunjukkan asal dokumen dari sistem, bukan tanda tangan elektronik tersertifikasi.

## Rekomendasi otomatis

Rekomendasi memakai hasil kalkulator nilai yang sama untuk LKS Saya, rekap, dan PDF.

- Tanpa checklist: ajakan memulai pencatatan harian.
- Belum tuntas: nilai saat ini, selisih menuju ambang peserta berdasarkan jabatan dan kategori LKS, serta maksimal tiga aktivitas prioritas.
- Tuntas tetapi masih ada aktivitas di bawah batas minimal: arahan menjaga konsistensi pada aktivitas tersebut.
- Seluruh aktivitas tuntas: apresiasi dan arahan mempertahankan konsistensi.

Selama periode aktif, rekomendasi diperbarui dari checklist. Saat periode ditutup, rekomendasi akhir disimpan pada snapshot peserta agar riwayat dan PDF berikutnya tetap sama.
