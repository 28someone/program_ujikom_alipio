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
                        <th>Pembayaran Denda</th>
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
                            <td>
                                @if($loan->status !== 'returned' || ! $loan->hasFine())
                                    <span class="table-note">Belum ada pembayaran</span>
                                @elseif($loan->isFinePaid())
                                    <span class="badge fine-paid">LUNAS</span>
                                    <p class="table-note">{{ $loan->fine_paid_at?->format('d M Y H:i') }}</p>
                                @else
                                    <span class="badge fine-unpaid">BELUM LUNAS</span>
                                @endif
                            </td>
                            <td class="actions">
                                @if($isAdmin && $loan->status === 'pending')
                                    <form action="{{ route('loans.approve', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Setujui</button>
                                    </form>
                                    <form action="{{ route('loans.reject', $loan) }}" method="POST" class="reject-loan-form">
                                        @csrf
                                        <input type="hidden" name="rejection_reason">
                                        <button type="submit" class="text-link danger-link">Tolak</button>
                                    </form>
                                @endif
                                @if($isAdmin && $loan->status === 'return_pending')
                                    <form action="{{ route('loans.return.approve', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Setujui Pengembalian</button>
                                    </form>
                                    <form action="{{ route('loans.return.reject', $loan) }}" method="POST" class="reject-return-form">
                                        @csrf
                                        <input type="hidden" name="return_rejection_reason">
                                        <button type="submit" class="text-link danger-link">Tolak Pengembalian</button>
                                    </form>
                                @endif
                                @if($isAdmin && $loan->status === 'returned' && $loan->hasFine() && ! $loan->isFinePaid())
                                    <form action="{{ route('loans.fine.pay', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Bayar Denda</button>
                                    </form>
                                @endif
                                @if(! $isAdmin && in_array($loan->status, ['borrowed', 'late', 'return_rejected'], true))
                                    <form action="{{ route('loans.return', $loan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-link">Ajukan Pengembalian</button>
                                    </form>
                                @endif
                                @if(! $isAdmin && $loan->status === 'returned' && $loan->hasFine() && ! $loan->isFinePaid())
                                    <span class="table-note">Menunggu pembayaran denda dikonfirmasi admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'returned' && $loan->hasFine() && $loan->isFinePaid())
                                    <span class="table-note">Denda sudah dibayar</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'pending')
                                    <span class="table-note">Menunggu persetujuan admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'rejected')
                                    <span class="table-note">Permintaan peminjaman ditolak admin.</span>
                                    @if($loan->rejection_reason)
                                        <p class="table-note">Alasan: {{ $loan->rejection_reason }}</p>
                                    @endif
                                @endif
                                @if(! $isAdmin && $loan->status === 'return_pending')
                                    <span class="table-note">Permintaan pengembalian menunggu persetujuan admin</span>
                                @endif
                                @if(! $isAdmin && $loan->status === 'return_rejected')
                                    <span class="table-note">Pengembalian ditolak admin. Silakan ajukan ulang setelah konfirmasi.</span>
                                    @if($loan->return_rejection_reason)
                                        <p class="table-note">Alasan: {{ $loan->return_rejection_reason }}</p>
                                    @endif
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
                        <tr><td colspan="{{ $isAdmin ? 9 : 8 }}" class="empty">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $loans->links() }}</div>
    </section>

    @if($isAdmin)
        <div class="reason-modal" id="reason-modal" hidden>
            <div class="reason-modal-backdrop" data-modal-close></div>
            <div class="reason-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="reason-modal-title">
                <div class="reason-modal-head">
                    <p class="eyebrow">Konfirmasi Admin</p>
                    <h3 id="reason-modal-title">Alasan Penolakan</h3>
                    <p class="muted" id="reason-modal-description">Tuliskan alasan yang akan ditampilkan kepada pengguna.</p>
                </div>
                <div class="reason-modal-body">
                    <label class="full">
                        <span>Alasan</span>
                        <textarea id="reason-modal-input" rows="5" placeholder="Contoh: Buku belum memenuhi syarat pemeriksaan admin."></textarea>
                    </label>
                    <p class="reason-modal-error" id="reason-modal-error" hidden>Alasan penolakan wajib diisi.</p>
                </div>
                <div class="reason-modal-actions">
                    <button type="button" class="button button-outline-inline" id="reason-modal-cancel">Batal</button>
                    <button type="button" class="button" id="reason-modal-submit">Simpan Alasan</button>
                </div>
            </div>
        </div>

        <script>
            (() => {
                const modal = document.getElementById('reason-modal');
                const modalTitle = document.getElementById('reason-modal-title');
                const modalDescription = document.getElementById('reason-modal-description');
                const modalInput = document.getElementById('reason-modal-input');
                const modalError = document.getElementById('reason-modal-error');
                const modalSubmit = document.getElementById('reason-modal-submit');
                const modalCancel = document.getElementById('reason-modal-cancel');
                let activeForm = null;
                let activeInput = null;

                const openModal = ({ form, inputName, title, description, placeholder, actionLabel }) => {
                    activeForm = form;
                    activeInput = form.querySelector(`input[name="${inputName}"]`);
                    modalTitle.textContent = title;
                    modalDescription.textContent = description;
                    modalInput.value = '';
                    modalInput.placeholder = placeholder;
                    modalSubmit.textContent = actionLabel;
                    modalError.hidden = true;
                    modal.hidden = false;
                    document.body.classList.add('modal-open');
                    window.setTimeout(() => modalInput.focus(), 30);
                };

                const closeModal = () => {
                    modal.hidden = true;
                    document.body.classList.remove('modal-open');
                    activeForm = null;
                    activeInput = null;
                    modalError.hidden = true;
                    modalInput.value = '';
                };

                document.querySelectorAll('.reject-loan-form').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();

                        openModal({
                            form,
                            inputName: 'rejection_reason',
                            title: 'Tolak Peminjaman',
                            description: 'Masukkan alasan yang akan dikirim ke akun pengguna agar mereka tahu kenapa permintaan peminjaman ditolak.',
                            placeholder: 'Contoh: Buku sedang dalam proses perbaikan dan belum bisa dipinjam.',
                            actionLabel: 'Tolak Peminjaman',
                        });
                    });
                });

                document.querySelectorAll('.reject-return-form').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();

                        openModal({
                            form,
                            inputName: 'return_rejection_reason',
                            title: 'Tolak Pengembalian',
                            description: 'Masukkan alasan penolakan pengembalian agar pengguna bisa melakukan perbaikan atau konfirmasi ulang.',
                            placeholder: 'Contoh: Buku belum diperiksa lengkap. Silakan datang ke petugas perpustakaan.',
                            actionLabel: 'Tolak Pengembalian',
                        });
                    });
                });

                modalSubmit.addEventListener('click', () => {
                    const reason = modalInput.value.trim();

                    if (!reason || !activeForm || !activeInput) {
                        modalError.hidden = false;
                        modalInput.focus();
                        return;
                    }

                    activeInput.value = reason;
                    const targetForm = activeForm;
                    closeModal();
                    targetForm.submit();
                });

                modalCancel.addEventListener('click', closeModal);

                modal.querySelectorAll('[data-modal-close]').forEach((element) => {
                    element.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.hidden) {
                        closeModal();
                    }
                });
            })();
        </script>
    @endif
@endsection
