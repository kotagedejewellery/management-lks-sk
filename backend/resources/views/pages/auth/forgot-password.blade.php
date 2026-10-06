<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pulihkan akses akun LKS Santri Karya.">
    <title>Lupa Password — LKS Santri Karya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('lks-auth.css') }}?v={{ filemtime(public_path('lks-auth.css')) }}">
</head>
<body class="auth-body">
    @php
        $rawFeedbackMessage = session('status') ?: $errors->first('email');
        $feedbackMessage = match ($rawFeedbackMessage) {
            'passwords.sent', 'We have emailed your password reset link.' => 'Tautan reset password telah dikirimkan ke email Anda.',
            'passwords.throttled', 'Please wait before retrying.' => 'Tunggu beberapa saat sebelum meminta tautan reset password baru.',
            default => $rawFeedbackMessage,
        };
        $feedbackType = $errors->any() ? 'error' : 'success';
    @endphp
    @if ($feedbackMessage)
        <div class="auth-feedback auth-feedback-{{ $feedbackType }}" role="{{ $feedbackType === 'error' ? 'alert' : 'status' }}" aria-live="{{ $feedbackType === 'error' ? 'assertive' : 'polite' }}" data-auth-feedback>
            <span class="auth-feedback-mark" aria-hidden="true">
                @if ($feedbackType === 'error')
                    <svg viewBox="0 0 24 24"><path d="M12 7v6m0 4h.01M5.4 19h13.2c1.2 0 1.9-1.3 1.3-2.3L13.3 5.3c-.6-1-2-1-2.6 0L4.1 16.7C3.5 17.7 4.2 19 5.4 19Z"/></svg>
                @else
                    <svg viewBox="0 0 24 24"><path d="m7 12 3.2 3.2L17.5 8"/></svg>
                @endif
            </span>
            <p>{{ $feedbackMessage }}</p>
            <button class="auth-feedback-close" type="button" data-dismiss-feedback aria-label="Tutup pesan"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17"/></svg></button>
        </div>
    @endif

    <main class="auth-shell">
        <section class="auth-intro" aria-labelledby="auth-intro-title">
            <a class="auth-brand" href="{{ route('home') }}" aria-label="LKS Santri Karya">
                <span class="auth-mark" aria-hidden="true">L</span>
                <span><strong>LKS</strong><small>Santri Karya</small></span>
            </a>
            <div class="auth-intro-copy">
                <p>Pulihkan akses dengan tenang</p>
                <h1 id="auth-intro-title">Satu tautan untuk melanjutkan catatan baik Anda.</h1>
                <span class="auth-rule" aria-hidden="true"></span>
                <p class="auth-support">Kami akan mengirimkan tautan aman ke email akun Anda untuk membuat password baru.</p>
            </div>
            <p class="auth-footnote">Tautan reset hanya dapat digunakan oleh pemilik email akun.</p>
        </section>

        <section class="auth-panel" aria-labelledby="forgot-password-title">
            <div class="auth-card">
                <div class="auth-card-head">
                    <p>Pulihkan akses akun</p>
                    <h2 id="forgot-password-title">Lupa password?</h2>
                    <span>Masukkan email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur password baru.</span>
                </div>

                <form method="POST" action="{{ route('password.email') }}" class="auth-form" data-password-reset-form>
                    @csrf
                    <div class="auth-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@perusahaan.com">
                    </div>
                    <button class="auth-submit" type="submit" data-test="email-password-reset-link-button">
                        <span class="auth-submit-label">Kirim tautan reset password</span>
                        <span class="auth-submit-loading" aria-live="polite" hidden><i aria-hidden="true"></i><span>Mengirim tautan…</span></span>
                    </button>
                </form>
                <p class="auth-help">Belum menerima tautan? Periksa email dan folder spam, lalu tunggu beberapa saat sebelum meminta tautan baru.</p>
                <p class="auth-return">Ingat password Anda? <a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
            </div>
        </section>
    </main>

    <script>
        const passwordResetForm = document.querySelector('[data-password-reset-form]');

        passwordResetForm?.addEventListener('submit', (event) => {
            if (!passwordResetForm.checkValidity()) return;
            if (passwordResetForm.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            const submitButton = passwordResetForm.querySelector('.auth-submit');
            passwordResetForm.dataset.submitting = 'true';
            passwordResetForm.classList.add('is-submitting');
            passwordResetForm.setAttribute('aria-busy', 'true');
            submitButton.querySelector('.auth-submit-label').hidden = true;
            submitButton.querySelector('.auth-submit-loading').hidden = false;
            submitButton.disabled = true;
        });

        window.addEventListener('pageshow', () => {
            const submitButton = passwordResetForm?.querySelector('.auth-submit');
            if (!submitButton) return;
            delete passwordResetForm.dataset.submitting;
            passwordResetForm.classList.remove('is-submitting');
            passwordResetForm.removeAttribute('aria-busy');
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
