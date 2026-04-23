CREATE DATABASE IF NOT EXISTS perpustakaan_sekolah;
USE perpustakaan_sekolah;

DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NULL UNIQUE,
    student_id VARCHAR(50) NULL UNIQUE,
    class_name VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    role ENUM('admin','member') NOT NULL DEFAULT 'member',
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publisher VARCHAR(255) NULL,
    year YEAR NULL,
    rack_location VARCHAR(100) NULL,
    stock_total INT UNSIGNED NOT NULL DEFAULT 0,
    stock_available INT UNSIGNED NOT NULL DEFAULT 0,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_books_categories FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE loans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loan_code VARCHAR(50) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    book_id BIGINT UNSIGNED NOT NULL,
    processed_by BIGINT UNSIGNED NULL,
    borrowed_at DATE NOT NULL,
    due_at DATE NOT NULL,
    returned_at DATE NULL,
    quantity TINYINT UNSIGNED NOT NULL DEFAULT 1,
    status ENUM('borrowed','returned','late') NOT NULL DEFAULT 'borrowed',
    note TEXT NULL,
    return_note TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_loans_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_loans_books FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    CONSTRAINT fk_loans_processors FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (id, name, username, email, student_id, class_name, phone, address, role, password, created_at, updated_at) VALUES
(1, 'Administrator Perpustakaan', 'admin', 'admin@perpus.test', NULL, NULL, '081200000001', 'Ruang Perpustakaan', 'admin', '$2y$10$JSTrHRTIlBidvUsGYklx3OTtK3qTJcAylW1/LFvmqgjIDShSKLuVO', NOW(), NOW()),
(2, 'Alya Putri', 'siswa1', 'alya@perpus.test', 'SIS-001', 'XI RPL 1', '081200000002', 'Jl. Melati 1', 'member', '$2y$10$JSTrHRTIlBidvUsGYklx3OTtK3qTJcAylW1/LFvmqgjIDShSKLuVO', NOW(), NOW()),
(3, 'Bima Pratama', 'siswa2', 'bima@perpus.test', 'SIS-002', 'XII TKJ 2', '081200000003', 'Jl. Anggrek 2', 'member', '$2y$10$JSTrHRTIlBidvUsGYklx3OTtK3qTJcAylW1/LFvmqgjIDShSKLuVO', NOW(), NOW());

INSERT INTO categories (id, name, slug, description, created_at, updated_at) VALUES
(1, 'Pemrograman', 'pemrograman', 'Buku coding dan pengembangan perangkat lunak.', NOW(), NOW()),
(2, 'Jaringan', 'jaringan', 'Buku dasar dan lanjutan jaringan komputer.', NOW(), NOW()),
(3, 'Novel', 'novel', 'Buku bacaan umum dan fiksi.', NOW(), NOW());

INSERT INTO books (id, category_id, code, title, author, publisher, year, rack_location, stock_total, stock_available, description, created_at, updated_at) VALUES
(1, 1, 'BK-001', 'Pemrograman Web Laravel', 'R. Nugroho', 'Informatika Nusantara', 2024, 'A1', 5, 4, 'Panduan membuat aplikasi web dengan Laravel.', NOW(), NOW()),
(2, 2, 'BK-002', 'Dasar Jaringan Komputer', 'T. Saputra', 'Tekno Press', 2023, 'B2', 3, 3, 'Materi jaringan untuk siswa SMK.', NOW(), NOW()),
(3, 3, 'BK-003', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2022, 'C4', 4, 4, 'Novel inspiratif untuk koleksi perpustakaan.', NOW(), NOW());

INSERT INTO loans (id, loan_code, user_id, book_id, processed_by, borrowed_at, due_at, returned_at, quantity, status, note, return_note, created_at, updated_at) VALUES
(1, 'TRX-0001', 2, 1, 1, CURDATE() - INTERVAL 2 DAY, CURDATE() + INTERVAL 5 DAY, NULL, 1, 'borrowed', 'Peminjaman awal untuk data contoh.', NULL, NOW(), NOW());
