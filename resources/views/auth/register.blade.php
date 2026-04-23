@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
    <section class="auth-card wide">
        <div>
            <p class="eyebrow">Registrasi User</p>
            <h2>Daftar anggota perpustakaan</h2>
            <p class="muted">Form ini digunakan siswa untuk membuat akun sebelum melakukan login dan peminjaman buku.</p>
        </div>

        <form action="{{ route('register.store') }}" method="POST" class="form-grid two-columns">
            @csrf
            <label>
                <span>Nama Lengkap</span>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            <label>
                <span>Username</span>
                <input type="text" name="username" value="{{ old('username') }}" required>
            </label>
            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}">
            </label>
            <label>
                <span>NIS / ID Siswa</span>
                <input type="text" name="student_id" value="{{ old('student_id') }}" required>
            </label>
            <label>
                <span>Kategori Kelas</span>
                <select name="class_category_id" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($classCategories as $classCategory)
                        <option value="{{ $classCategory->id }}" @selected(old('class_category_id') == $classCategory->id)>{{ $classCategory->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Nomor Telepon</span>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </label>
            <label class="full">
                <span>Alamat</span>
                <textarea name="address" rows="3">{{ old('address') }}</textarea>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>
            <label>
                <span>Konfirmasi Password</span>
                <input type="password" name="password_confirmation" required>
            </label>
            <button type="submit" class="button full">Daftar Sekarang</button>
        </form>

        <p class="muted">Sudah punya akun? <a href="{{ route('login') }}">Kembali ke login</a></p>
    </section>
@endsection
