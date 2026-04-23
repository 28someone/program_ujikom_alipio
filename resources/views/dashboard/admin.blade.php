@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <section class="dashboard-shell">
        <div class="dashboard-banner">
            <div>
                <p class="eyebrow">Dashboard Admin</p>
                <h2>Kelola perpustakaan sekolah dari satu tempat</h2>
                <p class="muted">Pantau koleksi buku, anggota, dan transaksi dengan tampilan yang lebih rapi dan profesional.</p>
            </div>
            <div class="dashboard-quick">
                <div class="quick-item">
                    <span>Peran</span>
                    <strong>Administrator</strong>
                </div>
                <div class="quick-item">
                    <span>Status Sistem</span>
                    <strong>Aktif</strong>
                </div>
            </div>
        </div>

        <section class="dashboard-actions">
            <div class="section-intro">
                <p class="eyebrow">Aksi Cepat</p>
                <h3>Mulai pengelolaan data</h3>
            </div>
            <div class="action-row">
                <a href="{{ route('books.create') }}" class="button">Tambah Buku</a>
                <a href="{{ route('members.create') }}" class="button button-outline-inline">Tambah Anggota</a>
            </div>
        </section>

        <section class="stats-grid dashboard-stats">
            <article class="stat-card stat-card-clean">
                <span>Total Buku</span>
                <strong>{{ $bookCount }}</strong>
                <p>Koleksi buku yang sudah terdaftar di sistem perpustakaan.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Total Anggota</span>
                <strong>{{ $memberCount }}</strong>
                <p>Jumlah user aktif yang terdaftar sebagai anggota.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Menunggu Persetujuan</span>
                <strong>{{ $pendingLoanCount }}</strong>
                <p>Permintaan pinjam yang masih menunggu persetujuan admin.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Peminjaman Rejected</span>
                <strong>{{ $rejectedLoanCount }}</strong>
                <p>Permintaan peminjaman yang berstatus rejected oleh admin.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Transaksi Aktif</span>
                <strong>{{ $activeLoanCount }}</strong>
                <p>Peminjaman yang masih berlangsung atau belum selesai.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Sudah Kembali</span>
                <strong>{{ $returnedLoanCount }}</strong>
                <p>Total transaksi yang sudah berhasil dikembalikan.</p>
            </article>
            <article class="stat-card stat-card-clean">
                <span>Total Denda</span>
                <strong>Rp {{ number_format($collectedFineTotal, 0, ',', '.') }}</strong>
                <p>Akumulasi denda dari transaksi yang terlambat dikembalikan.</p>
            </article>
        </section>

        <section class="panel dashboard-panel">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Transaksi Terbaru</p>
                    <h3>Aktivitas peminjaman terkini</h3>
                </div>
                <a href="{{ route('loans.index') }}" class="text-link">Lihat semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLoans as $loan)
                            <tr>
                                <td><strong>{{ $loan->loan_code }}</strong></td>
                                <td>{{ $loan->user->name }}</td>
                                <td>
                                    <strong>{{ $loan->book->title }}</strong>
                                    <p class="table-note">Diproses untuk anggota perpustakaan</p>
                                </td>
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

        <section class="panel dashboard-panel">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Log Aktivitas</p>
                    <h3>Aktivitas pengguna terbaru</h3>
                </div>
                <a href="{{ route('activity-logs.index') }}" class="text-link">Lihat semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivityLogs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                                <td>{{ $log->user?->name ?? 'Pengguna terhapus' }}</td>
                                <td>{{ strtoupper($log->user?->role ?? '-') }}</td>
                                <td>{{ strtoupper(str_replace('.', ' ', $log->action)) }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">Belum ada log aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
