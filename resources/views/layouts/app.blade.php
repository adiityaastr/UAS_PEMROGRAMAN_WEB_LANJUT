<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Perpustakaan') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        // Check local storage for theme
        const currentTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
    </script>
</head>

<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="logo">Perpustakaan</div>
            <nav>
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>

                @can('isAdmin')
                    <a href="{{ route('books.index') }}"
                        class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <span>Buku</span>
                    </a>
                @endcan

                <a href="{{ route('members.index') }}"
                    class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                    <span>Anggota</span>
                </a>

                <a href="{{ route('loans.index') }}"
                    class="nav-link {{ request()->routeIs('loans.index') ? 'active' : '' }}">
                    <span>Peminjaman</span>
                </a>

                @can('isPetugas')
                    <a href="{{ route('loans.create') }}"
                        class="nav-link {{ request()->routeIs('loans.create') ? 'active' : '' }}">
                        <span>Pinjam Baru</span>
                    </a>
                @endcan

                <a href="{{ route('loans.report') }}"
                    class="nav-link {{ request()->routeIs('loans.report') ? 'active' : '' }}">
                    <span>Laporan</span>
                </a>
            </nav>

            <div style="margin-top: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <button id="theme-toggle" class="theme-toggle" title="Ganti Tema">
                        <span id="theme-icon">☀️</span>
                    </button>
                </div>

                <div style="margin-bottom: 1rem; color: var(--text-muted);">
                    <strong>{{ Auth::user()->name }}</strong> <br>
                    <small>{{ ucfirst(Auth::user()->role) }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="width: 100%">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            @if(session('success'))
                <div class="glass fade-in"
                    style="background: rgba(16, 185, 129, 0.2); border-color: var(--success-color); margin-bottom: 1rem; color: var(--success-color);">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="glass fade-in"
                    style="background: rgba(239, 68, 68, 0.2); border-color: var(--danger-color); margin-bottom: 1rem; color: var(--danger-color);">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const toggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        function updateIcon(theme) {
            themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
        }

        updateIcon(localStorage.getItem('theme') || 'dark');

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    </script>
</body>

</html>