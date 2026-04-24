@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Data Transaksi</p>
                <h2>{{ $isAdmin ? 'CRUD Transaksi Peminjaman' : 'Riwayat Peminjaman Saya' }}</h2>
            </div>
            @if($isAdmin)
                <a href="{{ route('loans.create') }}" class="button">Tambah Transaksi</a>
            @endif
        </div>

        <form method="GET" class="filter-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi, anggota, atau judul buku">
            <select name="status">
                <option value="">Semua status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                <option value="borrowed" @selected(request('status') === 'borrowed')>Borrowed</option>
                <option value="late" @selected(request('status') === 'late')>Late</option>
                <option value="return_pending" @selected(request('status') === 'return_pending')>Return Pending</option>
                <option value="return_rejected" @selected(request('status') === 'return_rejected')>Return Rejected</option>
                <option value="returned" @selected(request('status') === 'returned')>Returned</option>
            </select>
            <button type="submit" class="button">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        @if($isAdmin)
                            <th>Anggota</th>
                        @endif
                        <th>Buku</th>
                        <th>Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->loan_code }}</td>
                            @if($isAdmin)
                                <td>{{ $loan->user->name }}</td>
                            @endif
                            <td>{{ $loan->book->title }}</td>
                            <td>{{ $loan->borrowed_at?->format('d M Y') }}</td>
                            <td>{{ $loan->due_at?->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $loan->status }}">
                                    {{
                                        [
                                            'pending' => 'PENDING',
                                            'rejected' => 'REJECTED',
                                            'borrowed' => 'BORROWED',
                                            'late' => 'LATE',
                                            'return_pending' => 'RETURN PENDING',
                                            'return_rejected' => 'RETURN REJECTED',
                                            'returned' => 'RETURNED',
                                        ][$loan->status] ?? strtoupper($loan->status)
                                    }}
                                </span>
                            </td>
                            <td>
                                <strong>Rp {{ number_format($loan->displayFineAmount(), 0, ',', '.') }}</strong>
                                @if(! in_array($loan->status, ['pending', 'rejected', 'returned'], true) && $loan->displayFineAmount() > 0)
                                    <p class="table-note">Denda berjalan</p>
                                @endif
                            </td>
                            <td class="actions">
                                @if($isAdmin && $loan->status === 'pending')
                                    <form action="{{ route('loans.approve', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Setujui</button>
                                    </form>
                                    <form action="{{ route('loans.reject', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link danger-link">Tolak</button>
                                    </form>
                                @endif
                                @if($isAdmin && $loan->status === 'return_pending')
                                    <form action="{{ route('loans.return.approve', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Setujui Pengembalian</button>
                                    </form>
                                    <form action="{{ route('loans.return.reject', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link danger-link">Tolak Pengembalian</button>
                                    </form>
                                @endif
                                @if(! $isAdmin && in_array($loan->status, ['borrowed', 'late', 'return_rejected'], true))
                                    <form action="{{ route('loans.return', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Ajukan Pengembalian</button>
                                    </form>
                                @endif
                                @if(! $isAdmin && $loan->status === 'pending')
                                    <span class="table-note">Menunggu persetujuan admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'rejected')
                                    <span class="table-note">Permintaan berstatus rejected oleh admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'return_pending')
                                    <span class="table-note">Permintaan pengembalian menunggu persetujuan admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'return_rejected')
                                    <span class="table-note">Pengembalian ditolak admin. Silakan ajukan ulang setelah konfirmasi.</span>
                                @endif
                                @if($isAdmin)
                                    <a href="{{ route('loans.edit', $loan) }}" class="text-link">Edit</a>
                                    <form action="{{ route('loans.destroy', $loan) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-link danger-link" onclick="return confirm('Hapus transaksi ini?')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isAdmin ? 8 : 7 }}" class="empty">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $loans->links() }}</div>
    </section>
@endsection
