@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')
    <section class="dashboard-shell">
        <div class="dashboard-banner member-banner-structured">
            <div>
                <p class="eyebrow">Dashboard User</p>
                <h2>Temukan buku dan kelola peminjaman dengan lebih nyaman</h2>
                <p class="muted">Lihat buku yang tersedia, pantau status pinjaman, dan akses layanan perpustakaan dari satu halaman.</p>
            </div>
            <div class="dashboard-quick">
                <div class="quick-item">
                    <span>Peran</span>
                    <strong>Anggota</strong>
                </div>
                <div class="quick-item">
                    <span>Akses</span>
                    <strong>Aktif</strong>
                </div>
            </div>
        </div>

        <section class="dashboard-actions">
            <div class="section-intro">
                <p class="eyebrow">Layanan Utama</p>
                <h3>Akses menu penting dengan cepat</h3>
            </div>
            <div class="action-row">
                <a href="{{ route('loans.borrow-form') }}" class="button">Pinjam Buku</a>
                <a href="{{ route('loans.index') }}" class="button button-outline-inline">Riwayat Saya</a>
            </div>
        </section>

        <section class="stats-grid dashboard-stats member-stats">
            <article class="stat-card stat-card-clean">
                <span>Buku Tersedia</span>
                <strong>{{ $availableBooks }}</strong>
                <p>Jumlah koleksi yang saat ini siap untuk dipinjam.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Menunggu Persetujuan</span>
                <strong>{{ $pendingLoanCount }}</strong>
                <p>Permintaan peminjaman yang masih menunggu persetujuan admin.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Peminjaman Ditolak</span>
                <strong>{{ $rejectedLoanCount }}</strong>
                <p>Permintaan peminjaman Anda yang ditolak oleh admin.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Pengembalian Ditolak</span>
                <strong>{{ $rejectedReturnCount }}</strong>
                <p>Pengajuan pengembalian Anda yang ditolak admin dan menunggu diajukan ulang.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Peminjaman Aktif</span>
                <strong>{{ $activeLoanCount }}</strong>
                <p>Buku yang sedang Anda pinjam saat ini.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Sudah Dikembalikan</span>
                <strong>{{ $returnedLoanCount }}</strong>
                <p>Riwayat transaksi yang telah selesai diproses.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Total Denda</span>
                <strong>Rp {{ number_format($fineTotal, 0, ',', '.') }}</strong>
                <p>Akumulasi denda dari peminjaman yang terlambat dikembalikan.</p>
            </article>
        </section>

        <section class="panel dashboard-panel">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Riwayat Singkat</p>
                    <h3>Peminjaman terbaru Anda</h3>
                </div>
                <a href="{{ route('loans.index') }}" class="text-link">Lihat semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Buku</th>
                            <th>Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memberLoans as $loan)
                            <tr>
                                <td><strong>{{ $loan->loan_code }}</strong></td>
                                <td>
                                    <strong>{{ $loan->book->title }}</strong>
                                    <p class="table-note">Transaksi pribadi Anda</p>
                                </td>
                                <td>{{ $loan->borrowed_at?->format('d M Y') }}</td>
                                <td>{{ $loan->due_at?->format('d M Y') }}</td>
                                <td><span class="badge {{ $loan->status }}">{{ strtoupper($loan->status) }}</span></td>
                                <td>Rp {{ number_format($loan->displayFineAmount(), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
