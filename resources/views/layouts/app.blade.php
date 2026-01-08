<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Perpustakaan') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Check local storage for theme
        const currentTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        // Update logo and icon on page load
        document.addEventListener('DOMContentLoaded', function() {
            const logoImg = document.getElementById('logo-image');
            if (logoImg) {
                if (currentTheme === 'dark') {
                    logoImg.src = "{{ asset('images/logo-dark.png') }}";
                } else {
                    logoImg.src = "{{ asset('images/logo.png') }}";
                }
            }
            
            // Update theme icon
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');
            if (sunIcon && moonIcon) {
                if (currentTheme === 'dark') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        });
    </script>
</head>

<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="logo">
                <img id="logo-image" src="{{ asset('images/logo-dark.png') }}" alt="Universitas Dian Nusantara" class="logo-image">
            </div>
            <nav>
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>

                @can('isAdmin')
                    <a href="{{ route('books.index') }}"
                        class="nav-link {{ request()->routeIs('books.index') || request()->routeIs('books.edit') ? 'active' : '' }}">
                        <span>Kelola Buku</span>
                    </a>
                    <a href="{{ route('books.create') }}"
                        class="nav-link {{ request()->routeIs('books.create') ? 'active' : '' }}">
                        <span>Tambah Buku Baru</span>
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

                @can('isPetugasOrAdmin')
                    <a href="{{ route('loans.create') }}"
                        class="nav-link {{ request()->routeIs('loans.create') ? 'active' : '' }}">
                        <span>Pinjam Baru</span>
                    </a>
                @endcan

                <a href="{{ route('loans.report') }}"
                    class="nav-link {{ request()->routeIs('loans.report') ? 'active' : '' }}">
                    <span>Laporan</span>
                </a>

                @can('isPetugasOrAdmin')
                    <a href="{{ route('fine-payments.index') }}"
                        class="nav-link {{ request()->routeIs('fine-payments.*') ? 'active' : '' }}">
                        <span>Pembayaran Denda</span>
                    </a>
                @endcan
            </nav>

            <div style="margin-top: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <button id="theme-toggle" class="theme-toggle" type="button" title="Ganti Tema">
                        <span id="theme-icon">
                            <svg id="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg id="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
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

            @if(session('error'))
                <div class="glass fade-in"
                    style="background: rgba(239, 68, 68, 0.2); border-color: var(--danger-color); margin-bottom: 1rem; color: var(--danger-color);">
                    {{ session('error') }}
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
        const logoImage = document.getElementById('logo-image');
        const html = document.documentElement;

        function updateIcon(theme) {
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');
            if (sunIcon && moonIcon) {
                if (theme === 'dark') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        }

        function updateLogo(theme) {
            if (logoImage) {
                if (theme === 'dark') {
                    logoImage.src = "{{ asset('images/logo-dark.png') }}";
                } else {
                    logoImage.src = "{{ asset('images/logo.png') }}";
                }
            }
        }

        // Sinkronkan ikon & logo dengan tema yang sedang aktif
        (function syncThemeState() {
            const activeTheme = html.getAttribute('data-theme') || 'dark';
            updateIcon(activeTheme);
            updateLogo(activeTheme);
        })();

        // Toggle tema saat tombol diklik
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const currentThemeAttr = html.getAttribute('data-theme');
                const newTheme = currentThemeAttr === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
                updateLogo(newTheme);
            });
        }
    </script>
</body>

</html>