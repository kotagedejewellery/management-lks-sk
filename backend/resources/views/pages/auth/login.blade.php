<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke LKS Santri Karya.">
    <title>Masuk — LKS Santri Karya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('lks-auth.css') }}?v={{ filemtime(public_path('lks-auth.css')) }}">
</head>
<body class="auth-body">
    @php
        $feedbackMessage = session('status') ?: $errors->first('email') ?: $errors->first('password');
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
                <p>Catatan harian yang tertib</p>
                <h1 id="auth-intro-title">Ritme baik dimulai dari satu amalan hari ini.</h1>
                <span class="auth-rule" aria-hidden="true"></span>
                <p class="auth-support">Masuk untuk mencatat amalan, melihat capaian, dan menjaga rekam periode LKS tetap utuh.</p>
            </div>
            <p class="auth-footnote">Akun dibuat dan dikelola oleh Admin LKS.</p>
        </section>

        <section class="auth-panel" aria-labelledby="login-title">
            <div class="auth-card">
                <div class="auth-card-head">
                    <p>Masuk ke akun Anda</p>
                    <h2 id="login-title">Selamat datang kembali.</h2>
                    <span>Gunakan email dan password sementara yang diberikan Admin.</span>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="auth-form" data-login-form>
                    @csrf
                    <div class="auth-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@perusahaan.com">
                    </div>
                    <div class="auth-field">
                        <div class="auth-label-row"><label for="password">Password</label>@if (Route::has('password.request'))<a href="{{ route('password.request') }}">Lupa password?</a>@endif</div>
                        <div class="password-control">
                            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan password">
                            <button class="password-toggle" type="button" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan password">
                                <svg class="password-toggle-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.8"/></svg>
                                <svg class="password-toggle-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 5.2A10.4 10.4 0 0 1 12 5c6.1 0 9.5 7 9.5 7a17.1 17.1 0 0 1-3.1 3.8M6.2 6.2A17.2 17.2 0 0 0 2.5 12S5.9 19 12 19a9.9 9.9 0 0 0 2.2-.3M9.8 9.8a3.1 3.1 0 0 0 4.4 4.4"/></svg>
                                <span class="sr-only" data-password-toggle-label>Tampilkan password</span>
                            </button>
                        </div>
                    </div>
                    <label class="auth-check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Ingat saya di perangkat ini</span></label>
                    <button class="auth-submit" type="submit" data-test="login-button"><span class="auth-submit-label">Masuk ke LKS</span><span class="auth-submit-loading" aria-live="polite" hidden><i aria-hidden="true"></i><span>Memeriksa akses…</span></span></button>
                </form>
                <p class="auth-help">Belum menerima akun? Hubungi Admin LKS untuk dibuatkan akses.</p>
            </div>
        </section>
    </main>
    <script>
        const loginForm = document.querySelector('[data-login-form]');
        const passwordInput = document.querySelector('#password');
        const passwordToggle = document.querySelector('[data-password-toggle]');

        passwordToggle?.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'text';
            passwordInput.type = isVisible ? 'password' : 'text';
            passwordToggle.setAttribute('aria-pressed', String(!isVisible));
            const label = isVisible ? 'Tampilkan password' : 'Sembunyikan password';
            passwordToggle.setAttribute('aria-label', label);
            passwordToggle.querySelector('[data-password-toggle-label]').textContent = label;
            passwordInput.focus({ preventScroll: true });
        });

        loginForm?.addEventListener('submit', () => {
            if (!loginForm.checkValidity()) return;
            const submitButton = loginForm.querySelector('.auth-submit');
            loginForm.classList.add('is-submitting');
            loginForm.setAttribute('aria-busy', 'true');
            submitButton.querySelector('.auth-submit-label').hidden = true;
            submitButton.querySelector('.auth-submit-loading').hidden = false;
            submitButton.disabled = true;
        });

        window.addEventListener('pageshow', () => {
            const submitButton = loginForm?.querySelector('.auth-submit');
            if (!submitButton) return;
            loginForm.classList.remove('is-submitting');
            loginForm.removeAttribute('aria-busy');
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
