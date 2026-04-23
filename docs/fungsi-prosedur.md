# Dokumentasi Fungsi dan Prosedur

## Autentikasi

- `AuthController::showLogin()` menampilkan halaman login.
- `AuthController::login()` memvalidasi username dan password lalu membuat sesi login.
- `AuthController::showRegister()` menampilkan form daftar anggota.
- `AuthController::register()` menyimpan user baru dengan role `member`.
- `AuthController::logout()` menghapus sesi login.

## Dashboard

- `DashboardController::__invoke()` menampilkan dashboard admin atau user sesuai role.

## Buku

- `BookController::index()` menampilkan data buku dan fitur pencarian.
- `BookController::create()` menampilkan form tambah buku.
- `BookController::store()` menyimpan buku baru.
- `BookController::edit()` menampilkan form edit buku.
- `BookController::update()` memperbarui data buku dan menjaga konsistensi stok tersedia.
- `BookController::destroy()` menghapus buku jika tidak ada transaksi aktif.

## Anggota

- `MemberController::index()` menampilkan data anggota.
- `MemberController::store()` menambahkan anggota baru.
- `MemberController::update()` memperbarui data anggota.
- `MemberController::destroy()` menghapus anggota jika tidak memiliki pinjaman aktif.

## Transaksi

- `LoanController::index()` menampilkan transaksi dan pencarian berdasarkan status atau kata kunci.
- `LoanController::create()` menampilkan form input transaksi admin.
- `LoanController::store()` membuat transaksi peminjaman dan mengurangi stok buku.
- `LoanController::edit()` menampilkan form edit transaksi admin.
- `LoanController::update()` memperbarui transaksi dan menyesuaikan stok jika status berubah.
- `LoanController::destroy()` menghapus transaksi dan mengembalikan stok jika perlu.
- `LoanController::borrowForm()` menampilkan katalog pinjam untuk user.
- `LoanController::returnBook()` memproses pengembalian buku dan menambah stok.
