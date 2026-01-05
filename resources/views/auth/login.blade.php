<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }
    </style>
    <script>
        const currentTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
    </script>
</head>

<body>
    <div class="glass login-card fade-in">
        <div class="logo" style="text-align: center;">Sistem Perpustakaan</div>

        @if($errors->any())
            <div
                style="color: var(--danger-color); margin-bottom: 1rem; text-align: center; background: rgba(239, 68, 68, 0.1); padding: 0.5rem; border-radius: 0.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}"
                    placeholder="nama@contoh.com">
            </div>
            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%">Masuk</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: var(--text-muted);">
            <p>Admin: admin@example.com / password</p>
            <p>Petugas: petugas@example.com / password</p>
        </div>
    </div>
</body>

</html>