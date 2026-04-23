@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <section class="login-showcase">
        <div class="login-promo">
            <p class="eyebrow light">Perpustakaan Digital</p>
            <h2>Masuk ke ruang baca modern sekolah Anda</h2>
            <p>Kelola data buku, pantau transaksi, dan lakukan peminjaman dengan tampilan yang lebih rapi, cepat, dan nyaman digunakan di localhost.</p>

            <div class="promo-points">
                <article>
                    <strong>Login Multi Role</strong>
                    <span>Admin dan user mendapatkan menu sesuai hak akses.</span>
                </article>
                <article>
                    <strong>Peminjaman Dinamis</strong>
                    <span>Stok buku otomatis berubah saat pinjam dan kembali.</span>
                </article>
                <article>
                    <strong>Pencarian Cepat</strong>
                    <span>Data buku dan transaksi lebih mudah ditemukan.</span>
                </article>
            </div>
        </div>

        <div class="auth-card login-card">
            <div>
                <p class="eyebrow">Login Aplikasi</p>
                <h2>Masuk ke sistem perpustakaan</h2>
                <p class="muted">Silakan login menggunakan akun yang sudah terdaftar untuk melanjutkan.</p>
            </div>

            <form action="{{ route('login.attempt') }}" method="POST" class="form-grid">
                @csrf
                <label>
                    <span>Username</span>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </label>
                <label class="checkbox">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                <button type="submit" class="button">Login</button>
            </form>

            <p class="muted auth-footer">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai anggota</a></p>
        </div>
    </section>
@endsection
