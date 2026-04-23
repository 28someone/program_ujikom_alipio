<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Perpustakaan',
                'email' => 'admin@perpus.test',
                'role' => 'admin',
                'phone' => '081200000001',
                'address' => 'Ruang Perpustakaan',
                'password' => Hash::make('password'),
            ]
        );

        $memberA = User::updateOrCreate(
            ['username' => 'siswa1'],
            [
                'name' => 'Alya Putri',
                'email' => 'alya@perpus.test',
                'student_id' => 'SIS-001',
                'class_name' => 'XI RPL 1',
                'role' => 'member',
                'phone' => '081200000002',
                'address' => 'Jl. Melati 1',
                'password' => Hash::make('password'),
            ]
        );

        $memberB = User::updateOrCreate(
            ['username' => 'siswa2'],
            [
                'name' => 'Bima Pratama',
                'email' => 'bima@perpus.test',
                'student_id' => 'SIS-002',
                'class_name' => 'XII TKJ 2',
                'role' => 'member',
                'phone' => '081200000003',
                'address' => 'Jl. Anggrek 2',
                'password' => Hash::make('password'),
            ]
        );

        $categories = collect([
            ['name' => 'Pemrograman', 'description' => 'Buku coding dan pengembangan perangkat lunak.'],
            ['name' => 'Jaringan', 'description' => 'Buku dasar dan lanjutan jaringan komputer.'],
            ['name' => 'Database', 'description' => 'Referensi basis data, SQL, dan perancangan sistem.'],
            ['name' => 'UI/UX', 'description' => 'Buku desain antarmuka dan pengalaman pengguna.'],
            ['name' => 'Bisnis', 'description' => 'Kepemimpinan, wirausaha, dan pengembangan diri.'],
            ['name' => 'Sains', 'description' => 'Sains populer dan pengetahuan umum.'],
            ['name' => 'Sejarah', 'description' => 'Tokoh, peristiwa, dan perjalanan sejarah.'],
            ['name' => 'Novel', 'description' => 'Buku bacaan umum dan fiksi.'],
        ])->mapWithKeys(function (array $category) {
            $slug = str($category['name'])->slug()->value();

            $model = Category::updateOrCreate(
                ['slug' => $slug],
                $category + ['slug' => $slug]
            );

            return [$slug => $model];
        });

        $books = [
            [
                'code' => 'BK-001',
                'category_id' => $categories['pemrograman']->id,
                'title' => 'Pemrograman Web Laravel',
                'author' => 'R. Nugroho',
                'publisher' => 'Informatika Nusantara',
                'year' => 2024,
                'cover_image' => 'images/book-covers/cover-1.svg',
                'rack_location' => 'A1',
                'stock_total' => 5,
                'stock_available' => 4,
                'description' => 'Panduan membangun aplikasi web modern dengan Laravel dari dasar hingga deployment.',
            ],
            [
                'code' => 'BK-002',
                'category_id' => $categories['jaringan']->id,
                'title' => 'Dasar Jaringan Komputer',
                'author' => 'T. Saputra',
                'publisher' => 'Tekno Press',
                'year' => 2023,
                'cover_image' => 'images/book-covers/cover-2.svg',
                'rack_location' => 'B2',
                'stock_total' => 4,
                'stock_available' => 4,
                'description' => 'Materi jaringan komputer untuk siswa SMK dengan pembahasan topologi, IP, dan troubleshooting.',
            ],
            [
                'code' => 'BK-003',
                'category_id' => $categories['novel']->id,
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'year' => 2022,
                'cover_image' => 'images/book-covers/cover-3.svg',
                'rack_location' => 'C4',
                'stock_total' => 6,
                'stock_available' => 6,
                'description' => 'Novel inspiratif tentang mimpi, pendidikan, dan persahabatan.',
            ],
            [
                'code' => 'BK-004',
                'category_id' => $categories['database']->id,
                'title' => 'Belajar SQL untuk Pemula',
                'author' => 'Dina Maharani',
                'publisher' => 'DataWorks',
                'year' => 2024,
                'cover_image' => 'images/book-covers/cover-1.svg',
                'rack_location' => 'A3',
                'stock_total' => 5,
                'stock_available' => 5,
                'description' => 'Pengenalan query SQL, relasi tabel, dan studi kasus pengelolaan data sekolah.',
            ],
            [
                'code' => 'BK-005',
                'category_id' => $categories['pemrograman']->id,
                'title' => 'JavaScript Interaktif',
                'author' => 'Bagas Wicaksono',
                'publisher' => 'Sinar Digital',
                'year' => 2025,
                'cover_image' => 'images/book-covers/cover-1.svg',
                'rack_location' => 'A2',
                'stock_total' => 7,
                'stock_available' => 7,
                'description' => 'Belajar JavaScript modern, DOM, fetch API, dan membangun antarmuka interaktif.',
            ],
            [
                'code' => 'BK-006',
                'category_id' => $categories['uiux']->id,
                'title' => 'Dasar UI dan UX',
                'author' => 'Salsa Rahma',
                'publisher' => 'Kreasi Visual',
                'year' => 2023,
                'cover_image' => 'images/book-covers/cover-6.svg',
                'rack_location' => 'D1',
                'stock_total' => 3,
                'stock_available' => 3,
                'description' => 'Prinsip desain antarmuka, wireframe, usability, dan riset pengguna.',
            ],
            [
                'code' => 'BK-007',
                'category_id' => $categories['sains']->id,
                'title' => 'Eksplorasi Semesta',
                'author' => 'Nadia Prameswari',
                'publisher' => 'Orbit Media',
                'year' => 2021,
                'cover_image' => 'images/book-covers/cover-4.svg',
                'rack_location' => 'E2',
                'stock_total' => 4,
                'stock_available' => 4,
                'description' => 'Sains populer tentang planet, bintang, dan perjalanan manusia memahami alam semesta.',
            ],
            [
                'code' => 'BK-008',
                'category_id' => $categories['sejarah']->id,
                'title' => 'Sejarah Nusantara Ringkas',
                'author' => 'Fajar Yudhistira',
                'publisher' => 'Pustaka Bangsa',
                'year' => 2020,
                'cover_image' => 'images/book-covers/cover-5.svg',
                'rack_location' => 'F1',
                'stock_total' => 5,
                'stock_available' => 5,
                'description' => 'Ringkasan sejarah Indonesia dari masa kerajaan hingga era modern.',
            ],
            [
                'code' => 'BK-010',
                'category_id' => $categories['novel']->id,
                'title' => 'Sore yang Panjang',
                'author' => 'Mira Azzahra',
                'publisher' => 'Langit Senja',
                'year' => 2022,
                'cover_image' => 'images/book-covers/cover-3.svg',
                'rack_location' => 'C2',
                'stock_total' => 6,
                'stock_available' => 6,
                'description' => 'Novel remaja yang hangat dengan cerita keluarga, mimpi, dan keberanian memulai lagi.',
            ],
            [
                'code' => 'BK-011',
                'category_id' => $categories['database']->id,
                'title' => 'Perancangan Basis Data',
                'author' => 'Yusuf Hidayat',
                'publisher' => 'Data Scholar',
                'year' => 2023,
                'cover_image' => 'images/book-covers/cover-5.svg',
                'rack_location' => 'A4',
                'stock_total' => 3,
                'stock_available' => 3,
                'description' => 'Materi ERD, normalisasi, dan implementasi basis data untuk proyek sekolah.',
            ],
            [
                'code' => 'BK-012',
                'category_id' => $categories['jaringan']->id,
                'title' => 'Routing dan Switching Praktis',
                'author' => 'Gilang Aditya',
                'publisher' => 'Net Academy',
                'year' => 2024,
                'cover_image' => 'images/book-covers/cover-2.svg',
                'rack_location' => 'B4',
                'stock_total' => 4,
                'stock_available' => 4,
                'description' => 'Pembahasan konfigurasi perangkat jaringan dan simulasi kasus di laboratorium.',
            ],
            [
                'code' => 'BK-013',
                'category_id' => $categories['pemrograman']->id,
                'title' => 'Python untuk Proyek Sekolah',
                'author' => 'Naufal Ardiansyah',
                'publisher' => 'Koding Pintar',
                'year' => 2024,
                'cover_image' => 'images/book-covers/cover-1.svg',
                'rack_location' => 'A5',
                'stock_total' => 5,
                'stock_available' => 5,
                'description' => 'Panduan belajar Python dengan latihan proyek sederhana untuk tugas sekolah dan portofolio awal.',
            ],
            [
                'code' => 'BK-014',
                'category_id' => $categories['sains']->id,
                'title' => 'Sains Harian yang Menarik',
                'author' => 'Citra Maheswari',
                'publisher' => 'Cakrawala Ilmu',
                'year' => 2023,
                'cover_image' => 'images/book-covers/cover-4.svg',
                'rack_location' => 'E4',
                'stock_total' => 4,
                'stock_available' => 4,
                'description' => 'Kumpulan penjelasan sains ringan dari fenomena sehari-hari yang dekat dengan kehidupan pelajar.',
            ],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(['code' => $book['code']], $book);
        }

        $loanBook = Book::where('code', 'BK-001')->first();

        if ($loanBook) {
            Loan::updateOrCreate(
                ['loan_code' => 'TRX-0001'],
                [
                    'user_id' => $memberA->id,
                    'book_id' => $loanBook->id,
                    'processed_by' => $admin->id,
                    'borrowed_at' => now()->subDays(2),
                    'due_at' => now()->addDays(5),
                    'returned_at' => null,
                    'status' => 'borrowed',
                    'quantity' => 1,
                    'note' => 'Peminjaman awal untuk data contoh.',
                    'fine_amount' => 0,
                ]
            );
        }

        $returnedBook = Book::where('code', 'BK-003')->first();

        if ($returnedBook) {
            Loan::updateOrCreate(
                ['loan_code' => 'TRX-0002'],
                [
                    'user_id' => $memberB->id,
                    'book_id' => $returnedBook->id,
                    'processed_by' => $admin->id,
                    'borrowed_at' => now()->subDays(12),
                    'due_at' => now()->subDays(5),
                    'returned_at' => now()->subDays(3),
                    'status' => 'returned',
                    'quantity' => 1,
                    'note' => 'Contoh transaksi yang sudah selesai.',
                    'return_note' => 'Buku kembali dalam kondisi baik.',
                    'fine_amount' => 2000,
                ]
            );
        }
    }
}
