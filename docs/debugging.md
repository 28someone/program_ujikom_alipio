# Catatan Debugging

Debugging yang dilakukan pada pengembangan aplikasi:

1. Memastikan middleware role aktif di `bootstrap/app.php` agar admin dan user tidak saling mengakses menu yang tidak sesuai.
2. Menyesuaikan tabel `users` karena aplikasi membutuhkan `username`, `student_id`, dan `role`.
3. Memperbaiki alur stok buku supaya:
   - stok berkurang saat peminjaman,
   - stok bertambah saat pengembalian,
   - stok tetap sinkron saat status transaksi diubah admin,
   - data buku tidak bisa dihapus saat masih ada transaksi aktif.
4. Memastikan route admin dan user terpisah namun tetap memakai data transaksi yang sama.
5. Menyiapkan data awal untuk admin, user, kategori, buku, dan transaksi contoh agar aplikasi langsung bisa diuji.
