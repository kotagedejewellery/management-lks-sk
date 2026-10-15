<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Dokumen LKS</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { align-items: center; background: #eef3ee; color: #18333a; display: flex; font-family: system-ui, sans-serif; justify-content: center; margin: 0; min-height: 100vh; padding: 24px; }
        main { background: #fffefa; border: 1px solid #c7d4c8; border-radius: 16px; box-shadow: 0 18px 48px rgb(24 51 58 / 12%); max-width: 440px; padding: 34px 28px; text-align: center; width: 100%; }
        .mark { align-items: center; background: #e5f0e9; border-radius: 999px; color: #16604c; display: inline-flex; font-size: 34px; font-weight: 800; height: 72px; justify-content: center; width: 72px; }
        h1 { font-family: Georgia, serif; font-size: 27px; line-height: 1.15; margin: 20px 0 8px; }
        p { color: #53645a; line-height: 1.55; margin: 0; }
        .code { border-top: 1px solid #d9e1da; margin-top: 24px; padding-top: 18px; }
        .code strong { color: #18333a; display: block; font-size: 18px; letter-spacing: .06em; margin: 4px 0; }
        small { color: #718178; display: block; margin-top: 12px; }
    </style>
</head>
<body>
    <main>
        <div class="mark" aria-hidden="true">✓</div>
        <h1>Dokumen ini Resmi &amp; Sah</h1>
        <p>Dokumen LKS ini diterbitkan oleh sistem dan telah terverifikasi.</p>
        <div class="code"><p>Kode verifikasi</p><strong>{{ $verification->document_code }}</strong></div>
        <small>Diterbitkan {{ $verification->issued_at->locale('id')->translatedFormat('j F Y, H:i') }} WIB</small>
    </main>
</body>
</html>
