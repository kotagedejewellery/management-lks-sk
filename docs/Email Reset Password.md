# Panduan Brevo untuk Email Reset Password

Fitur **Lupa password?** sudah memakai Laravel Fortify: pengguna meminta tautan melalui `/forgot-password`, menerima tautan satu kali pakai, lalu menetapkan password baru pada halaman reset. Token berlaku 60 menit dan permintaan baru untuk email yang sama dibatasi satu kali per 60 detik.

## Persiapan di Brevo

1. Tambahkan dan autentikasi domain pengirim pada Brevo.
2. Buat transactional sender, misalnya `no-reply@domain-anda.id`.
3. Buka **Settings → SMTP & API**, salin nilai **SMTP login**, lalu buat dan simpan **SMTP key** baru.

Gunakan SMTP key, bukan API key Brevo. Nilai key hanya boleh disimpan pada server.

## Konfigurasi production

Di server, isi `backend/.env`. Gunakan kredensial dari penyedia SMTP dan alamat pengirim yang telah diverifikasi oleh penyedia tersebut.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lks.domain-anda.id

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=SMTP_LOGIN_DARI_BREVO
MAIL_PASSWORD=SMTP_KEY_DARI_BREVO
MAIL_FROM_ADDRESS=no-reply@domain-anda.id
MAIL_FROM_NAME="LKS Santri Karya"
```

Untuk port `587`, biarkan `MAIL_SCHEME=null` seperti contoh di atas. Bila port tersebut diblokir VPS, Brevo mendukung port `2525` sebagai alternatif. Untuk TLS implisit pada port `465`, gunakan `MAIL_SCHEME=smtps`.

Jangan memasukkan SMTP key ke `.env.example`, `compose.yml`, atau repository.

## Terapkan dan verifikasi

Setelah `.env` diperbarui di VPS, jalankan dari direktori proyek:

```bash
docker compose exec -T app php artisan optimize:clear
```

Lalu buka halaman login, pilih **Lupa password?**, masukkan email akun yang aktif, dan pastikan:

1. Email masuk dari alamat `MAIL_FROM_ADDRESS`.
2. Tautan mengarah ke domain HTTPS pada `APP_URL`.
3. Password baru dapat dipakai untuk login.

`MAIL_MAILER=log` hanya tepat untuk lokal: email ditulis ke log dan tidak dikirim ke inbox. Tidak perlu menjalankan migrasi atau worker queue untuk alur reset password ini.
