# Manual Singkat Penggunaan

## Menjalankan aplikasi

1. Buat database MySQL dengan nama `perpustakaan_sekolah`.
2. Import file [library_app.sql](/d:/xampp/htdocs/Program_Ujikom/database/library_app.sql).
3. Atur file `.env` sesuai MySQL lokal.
4. Jalankan perintah:

```bash
php artisan key:generate
php artisan serve
```

5. Buka `http://127.0.0.1:8000`.

## Akun awal

- Admin: `admin` / `password`
- User: `siswa1` / `password`

## Alur singkat

- User dapat daftar lalu login.
- Admin dapat menambah buku, mengelola anggota, dan membuat transaksi.
- User dapat memilih menu peminjaman, meminjam buku, lalu mengembalikannya dari menu transaksi.
