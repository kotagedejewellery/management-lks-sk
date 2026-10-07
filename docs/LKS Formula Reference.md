# Referensi Rumus LKS

Dokumen ini memetakan rumus yang digunakan pada spreadsheet sumber LKS.

- Sumber: [2026_LKS BM - tab OKT.26](https://docs.google.com/spreadsheets/d/1Hedn9p4DrXBLswjeQIO0c8HqUs_qRM00ewWsAYbXSJg/edit?gid=1595425212#gid=1595425212)
- Status: dokumentasi referensi; spreadsheet sumber tidak diubah.
- Notasi: `r` berarti nomor baris peserta pada matriks pengisian.

## Struktur spreadsheet

| Bagian | Lokasi | Fungsi |
| --- | --- | --- |
| Matriks pengisian LKS | Kolom A-GN | Checkbox per aktivitas dan tanggal, total checklist, serta nilai per aktivitas setiap Santri Karya. |
| Lembar Kendali Spiritual individu | Kolom sekitar GP-GY | Detail seorang peserta: identitas, capaian, ketuntasan, nilai, total, predikat, dan rekomendasi. |
| Rekap nilai | Kolom HH-HU | Ringkasan nilai aktivitas dan nilai akhir seluruh peserta. |

Matriks diulang untuk empat kelompok: Leader Ikhwan, Leader Akhwat, Staff Ikhwan, dan Staff Akhwat.

## Aktivitas dan rumus matriks

| Aktivitas | Kolom checkbox | Rumus total | Kolom nilai | Rumus nilai |
| --- | --- | --- | --- | --- |
| Absen Subuh | D:Z | `=COUNTIF(D[r]:Z[r],TRUE)` | AB | `=AA[r]/target*100` |
| Shalawat Munjiyat | AC:AY | `=COUNTIF(AC[r]:AY[r],TRUE)` | BA | `=AZ[r]/target*100` |
| Shalat Tepat Waktu di Jam Karya | BB:BX | `=COUNTIF(BB[r]:BX[r],TRUE)` | BZ | `=BY[r]/target*100` |
| Dzikir Pagi Petang | CA:CW | `=COUNTIF(CA[r]:CW[r],TRUE)` | CY | `=CX[r]/target*100` |
| Tilawah Harian 2 Halaman | CZ:DV | `=COUNTIF(CZ[r]:DV[r],TRUE)` | DX | `=DW[r]/target*100` |
| Shalat Dhuha | DY:EU | `=COUNTIF(DY[r]:EU[r],TRUE)` | EW | `=EV[r]/target*100` |
| Tahajud | EX:FG | `=COUNTIF(EX[r]:FG[r],TRUE)` | FI | `=FH[r]/target*100` |
| Tilawati | FJ:FN | `=COUNTIF(FJ[r]:FN[r],TRUE)` | FP | `=FO[r]/target*100` |
| Subuhan di Masjid | FQ:GE | `=COUNTIF(FQ[r]:GE[r],TRUE)` | GG | `=GF[r]/target*100` |
| Puasa Kamis | GH:GL | `=COUNTIF(GH[r]:GL[r],TRUE)` | GN | `=GM[r]/target*100` |

Kolom tanggal pada blok aktivitas lanjutan merujuk ke blok tanggal awal, misalnya `=D7`, `=E7`, dan seterusnya. Dengan demikian, seluruh aktivitas memakai kalender yang sama.

## Parameter aktivitas dan cakupan kelompok

| Aktivitas | Minimal tuntas | Maksimal capaian | Berlaku untuk |
| --- | ---: | ---: | --- |
| Absen Subuh | 14 | 20 | Semua kelompok |
| Shalawat Munjiyat | 14 | 20 | Semua kelompok |
| Shalat Tepat Waktu di Jam Karya | 14 | 20 | Semua kelompok |
| Dzikir Pagi Petang | 14 | 20 | Semua kelompok |
| Tilawah Harian 2 Halaman | 14 | 20 | Semua kelompok |
| Shalat Dhuha | 14 | 20 | Semua kelompok |
| Tahajud | 6 | 8 | Semua kelompok |
| Tilawati | 3 | 4 | Semua kelompok |
| Subuhan di Masjid | 9 | 12 | Leader Ikhwan dan Staff Ikhwan |
| Puasa Kamis | 3 | 4 | Leader Ikhwan dan Leader Akhwat |

| Kelompok | Jumlah komponen | Target nilai total | Standar minimal yang dicantumkan |
| --- | ---: | ---: | ---: |
| Leader Ikhwan | 10 | 1.000 | 90% |
| Leader Akhwat | 9 | 900 | 90% |
| Staff Ikhwan | 9 | 900 | 85% |
| Staff Akhwat | 8 | 800 | 85% |

## Rumus lembar kendali individu

| Tujuan | Contoh rumus aktual | Keterangan |
| --- | --- | --- |
| Ambil nama | `=VLOOKUP($HB$7,$A$8:$GN$12,2,0)` | Mengambil nama dari nomor peserta yang dipilih. |
| Ambil tim | `=VLOOKUP($HB$7,$A$8:$GN$12,3,0)` | Mengambil tim dari nomor peserta yang dipilih. |
| Ambil capaian aktivitas | `=VLOOKUP($HB$7,$A$8:$GN$12,27,0)` | Mengambil total checkbox aktivitas dari matriks. Indeks kolom berubah per aktivitas. |
| Ambil nilai aktivitas | `=VLOOKUP($HB$7,$A$8:$GN$12,28,0)` | Mengambil nilai aktivitas dari matriks. Indeks kolom berubah per aktivitas. |
| Status aktivitas | `=IF(GW10>=GU10,"Tuntas","Belum Tuntas")` | Tuntas bila jumlah capaian mencapai atau melampaui nilai minimal. |
| Total nilai | `=SUM(GY10:GY19)` | Menjumlah nilai seluruh aktivitas yang berlaku. |
| Target nilai keseluruhan | `=100*10` | Nilai maksimal: 100 dikali jumlah komponen. Formula berubah menjadi `=100*9` atau `=100*8` sesuai kelompok. |
| Nilai akhir | `=GY20/GY21` | Total nilai dibagi target nilai keseluruhan. |
| Predikat | `=IF(GY23>=91%,"A",IF(GY23>=81%,"B",IF(GY23>=71%,"C",IF(GY23>=61%,"D","E"))))` | A: 91-100; B: 81-90; C: 71-80; D: 61-70; E: di bawah 61. |
| Rekomendasi | `=VLOOKUP(...)` | Mengambil nilai rekomendasi yang sudah tersedia di matriks; tidak dihitung otomatis. |

## Rumus rekap organisasi

| Tujuan | Contoh rumus aktual | Keterangan |
| --- | --- | --- |
| Menyalin nilai aktivitas | `=AB8`, `=BA8`, `=BZ8`, dan seterusnya | Mengambil nilai per aktivitas dari matriks utama. |
| Nilai total peserta | `=SUM(HH9:HQ9)` | Menjumlah nilai komponen yang berlaku bagi seorang peserta. |
| Persentase peserta | `=HR9/$GY$21` | Nilai peserta dibagi target nilai total kelompoknya. Referensi target berbeda untuk setiap kelompok. |
| Nilai rata-rata rekap | `=AVERAGE(HS9:HS19)` | Merata-ratakan persentase peserta dalam rekap. |
| Keputusan rekap | `=IF(HS21>=85%,"ACHIEVE","NOT ACHIEVE")` | Rekap dinyatakan tercapai bila rata-ratanya minimal 85%. |

## Penerapan pada sistem

Sistem memakai rumus nilai aktivitas yang sama dengan spreadsheet: `min(checklist ÷ target, 100%)`, lalu merata-ratakan aktivitas yang berlaku bagi peserta. Ketuntasan akhir diterapkan per peserta sesuai level snapshot periode: Leader memakai ambang 90% dan Staff 85% secara default. Kedua ambang disimpan pada setiap periode agar riwayat tidak berubah ketika kebijakan periode berikutnya disesuaikan.

Rekap organisasi menampilkan rata-rata, jumlah tuntas, dan jumlah belum tuntas sebagai indikator agregat. Rekap tidak lagi memberi keputusan global `ACHIEVE` atau `NOT ACHIEVE`, karena setiap peserta dapat memiliki ambang kelulusan yang berbeda.

## Catatan validasi spreadsheet

1. Nilai setiap aktivitas dihitung dari `jumlah checklist ÷ target × 100`.
2. Ketuntasan aktivitas memakai batas minimal aktivitas, bukan keharusan mencapai nilai 100.
3. Nilai standar 90% atau 85% dicantumkan pada lembar individu. Dalam rumus yang terbaca, nilai tersebut tidak dipakai langsung untuk menentukan predikat akhir; predikat selalu memakai rentang A–E.
4. Rekap organisasi pada spreadsheet memakai ambang tetap 85%, termasuk untuk kelompok Leader yang pada lembar individunya mencantumkan standar 90%. Sistem tidak mengikuti keputusan global ini; status peserta mengikuti ambang level jabatannya.
5. Formula `BZ9` pada Leader Ikhwan saat ini adalah `=COUNTIF(AC9:AX9,TRUE)`. Rentang ini milik Shalawat Munjiyat, bukan Shalat Tepat Waktu yang umumnya memakai `BB:BX`; catat sebagai anomali pada spreadsheet sumber.
