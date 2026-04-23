<section class="panel form-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Edit Transaksi</p>
            <h2>{{ $loan->loan_code }}</h2>
        </div>
        <a href="{{ route('loans.index') }}" class="text-link">Kembali</a>
    </div>

    <form action="{{ $action }}" method="POST" class="form-grid two-columns">
        @csrf
        @method($method)
        <label>
            <span>Anggota</span>
            <input type="text" value="{{ $loan->user->name }}" disabled>
        </label>
        <label>
            <span>Buku</span>
            <input type="text" value="{{ $loan->book->title }}" disabled>
        </label>
        <label>
            <span>Tanggal Pinjam</span>
            <input type="date" name="borrowed_at" value="{{ old('borrowed_at', $loan->borrowed_at?->toDateString()) }}" required>
        </label>
        <label>
            <span>Tanggal Jatuh Tempo</span>
            <input type="date" name="due_at" value="{{ old('due_at', $loan->due_at?->toDateString()) }}" required>
        </label>
        <label>
            <span>Status</span>
            <select name="status" required>
                @foreach(['pending', 'rejected', 'borrowed', 'late', 'returned'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $loan->status) === $status)>{{ strtoupper($status) }}</option>
                @endforeach
            </select>
        </label>
        <label class="full">
            <span>Catatan</span>
            <textarea name="note" rows="4">{{ old('note', $loan->note) }}</textarea>
        </label>
        <button type="submit" class="button full">Simpan Perubahan</button>
    </form>
</section>
