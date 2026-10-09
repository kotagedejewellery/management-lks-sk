<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Atur password baru akun LKS Santri Karya.">
    <title>Atur Password Baru — LKS Santri Karya</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('brand/favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('brand/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('lks-auth.css') }}?v={{ filemtime(public_path('lks-auth.css')) }}">
</head>
<body class="auth-body">
    @php
        $rawFeedbackMessage = $errors->first('email') ?: $errors->first('password') ?: $errors->first('token');
        $feedbackMessage = match ($rawFeedbackMessage) {
            'passwords.token', 'This password reset token is invalid.' => 'Tautan reset tidak valid atau telah kedaluwarsa. Minta tautan baru untuk melanjutkan.',
            default => $rawFeedbackMessage,
        };
    @endphp
    @if ($feedbackMessage)
        <div class="auth-feedback auth-feedback-error" role="alert" aria-live="assertive" data-auth-feedback>
            <span class="auth-feedback-mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 7v6m0 4h.01M5.4 19h13.2c1.2 0 1.9-1.3 1.3-2.3L13.3 5.3c-.6-1-2-1-2.6 0L4.1 16.7C3.5 17.7 4.2 19 5.4 19Z"/></svg></span>
            <p>{{ $feedbackMessage }}</p>
            <button class="auth-feedback-close" type="button" data-dismiss-feedback aria-label="Tutup pesan"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17"/></svg></button>
        </div>
    @endif

    <main class="auth-shell">
        <section class="auth-intro" aria-labelledby="auth-intro-title">
            <a class="auth-brand" href="{{ route('home') }}" aria-label="LKS Santri Karya">
                <img class="auth-mark" src="{{ asset('brand/yayasan-rji-mark.png') }}" alt="">
                <span><strong>LKS</strong><small>Santri Karya</small></span>
            </a>
            <div class="auth-intro-copy">
                <p>Akses aman</p>
                <h1 id="auth-intro-title">Buat password baru untuk melanjutkan dengan aman.</h1>
                <span class="auth-rule" aria-hidden="true"></span>
                <p class="auth-support">Password baru akan menggantikan password lama dan mengeluarkan sesi lama dari akun Anda.</p>
            </div>
            <p class="auth-footnote">Gunakan password yang panjang, unik, dan tidak dibagikan kepada orang lain.</p>
        </section>

        <section class="auth-panel" aria-labelledby="reset-password-title">
            <div class="auth-card">
                <div class="auth-card-head">
                    <p>Atur ulang akses akun</p>
                    <h2 id="reset-password-title">Password baru</h2>
                    <span>Masukkan email akun dan password baru Anda untuk menyelesaikan pemulihan akses.</span>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="auth-form" data-reset-password-form>
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <div class="auth-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', request('email')) }}" required autofocus autocomplete="email" placeholder="nama@perusahaan.com">
                    </div>
                    <div class="auth-field">
                        <label for="password">Password baru</label>
                        <div class="password-control">
                            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Masukkan password baru">
                            <button class="password-toggle" type="button" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan password baru">
                                <svg class="password-toggle-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.8"/></svg>
                                <svg class="password-toggle-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 5.2A10.4 10.4 0 0 1 12 5c6.1 0 9.5 7 9.5 7a17.1 17.1 0 0 1-3.1 3.8M6.2 6.2A17.2 17.2 0 0 0 2.5 12S5.9 19 12 19a9.9 9.9 0 0 0 2.2-.3M9.8 9.8a3.1 3.1 0 0 0 4.4 4.4"/></svg>
                                <span class="sr-only" data-password-toggle-label>Tampilkan password baru</span>
                            </button>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation">Konfirmasi password baru</label>
                        <div class="password-control">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi password baru">
                            <button class="password-toggle" type="button" data-password-toggle aria-controls="password_confirmation" aria-pressed="false" aria-label="Tampilkan konfirmasi password">
                                <svg class="password-toggle-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.8"/></svg>
                                <svg class="password-toggle-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 5.2A10.4 10.4 0 0 1 12 5c6.1 0 9.5 7 9.5 7a17.1 17.1 0 0 1-3.1 3.8M6.2 6.2A17.2 17.2 0 0 0 2.5 12S5.9 19 12 19a9.9 9.9 0 0 0 2.2-.3M9.8 9.8a3.1 3.1 0 0 0 4.4 4.4"/></svg>
                                <span class="sr-only" data-password-toggle-label>Tampilkan konfirmasi password</span>
                            </button>
                        </div>
                    </div>
                    <button class="auth-submit" type="submit" data-test="reset-password-button">
                        <span class="auth-submit-label">Simpan password baru</span>
                        <span class="auth-submit-loading" aria-live="polite" hidden><i aria-hidden="true"></i><span>Menyimpan password…</span></span>
                    </button>
                </form>
                <p class="auth-help">Setelah disimpan, Anda perlu masuk kembali menggunakan password baru.</p>
                <p class="auth-return">Tautan tidak lagi berlaku? <a href="{{ route('password.request') }}">Minta tautan reset baru</a></p>
            </div>
        </section>
    </main>

    <script>
        const resetPasswordForm = document.querySelector('[data-reset-password-form]');

        document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const input = document.getElementById(toggle.getAttribute('aria-controls'));
                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', String(!isVisible));
                const label = isVisible ? `Tampilkan ${input.labels[0].textContent.toLowerCase()}` : `Sembunyikan ${input.labels[0].textContent.toLowerCase()}`;
                toggle.setAttribute('aria-label', label);
                toggle.querySelector('[data-password-toggle-label]').textContent = label;
                input.focus({ preventScroll: true });
            });
        });

        resetPasswordForm?.addEventListener('submit', () => {
            if (!resetPasswordForm.checkValidity()) return;
            const submitButton = resetPasswordForm.querySelector('.auth-submit');
            resetPasswordForm.classList.add('is-submitting');
            resetPasswordForm.setAttribute('aria-busy', 'true');
            submitButton.querySelector('.auth-submit-label').hidden = true;
            submitButton.querySelector('.auth-submit-loading').hidden = false;
            submitButton.disabled = true;
        });

        window.addEventListener('pageshow', () => {
            const submitButton = resetPasswordForm?.querySelector('.auth-submit');
            if (!submitButton) return;
            resetPasswordForm.classList.remove('is-submitting');
            resetPasswordForm.removeAttribute('aria-busy');
            submitButton.querySelector('.auth-submit-label').hidden = false;
            submitButton.querySelector('.auth-submit-loading').hidden = true;
            submitButton.disabled = false;
        });

        document.querySelector('[data-dismiss-feedback]')?.addEventListener('click', (event) => {
            event.currentTarget.closest('[data-auth-feedback]')?.remove();
        });
    </script>
</body>
</html>
