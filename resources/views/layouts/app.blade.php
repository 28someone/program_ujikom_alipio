<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan Sekolah')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('library-theme');
            const theme = savedTheme === 'dark' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>
    <div class="page-shell">
        @auth
            <aside class="sidebar">
                <div>
                    <p class="eyebrow">Perpustakaan Sekolah</p>
                    <h1>Sistem Peminjaman Buku</h1>
                    <p class="muted">{{ auth()->user()->name }} - {{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</p>
                </div>

                <nav class="menu">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.index') || request()->routeIs('books.*') ? 'active' : '' }}">Data Buku</a>
                    <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.index') || request()->routeIs('loans.create') || request()->routeIs('loans.edit') ? 'active' : '' }}">Transaksi</a>
                    @if(auth()->user()->isMember())
                        <a href="{{ route('loans.borrow-form') }}" class="{{ request()->routeIs('loans.borrow-form') ? 'active' : '' }}">Peminjaman</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('members.index') }}" class="{{ request()->routeIs('members.*') ? 'active' : '' }}">Kelola Anggota</a>
                        <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">Log Aktivitas</a>
                    @endif
                </nav>

                <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Ubah tema">
                    <span class="theme-toggle-icon" aria-hidden="true">◐</span>
                    <span id="theme-toggle-label">Mode Gelap</span>
                </button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="button button-outline full-width">Logout</button>
                </form>
            </aside>
        @endauth

        <main class="content {{ auth()->check() ? '' : 'content-auth' }}">
            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert danger">
                    <strong>Periksa kembali input Anda.</strong>
                    <ul class="error-list">
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
        (() => {
            const root = document.documentElement;
            const toggle = document.getElementById('theme-toggle');
            const label = document.getElementById('theme-toggle-label');

            if (!toggle || !label) {
                return;
            }

            const syncLabel = () => {
                const isDark = root.getAttribute('data-theme') === 'dark';
                label.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            };

            syncLabel();

            toggle.addEventListener('click', () => {
                const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', nextTheme);
                localStorage.setItem('library-theme', nextTheme);
                syncLabel();
            });
        })();
    </script>
</body>
</html>
