<section class="panel form-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Form Transaksi</p>
            <h2>Tambah Transaksi Peminjaman</h2>
        </div>
        <a href="{{ route('loans.index') }}" class="text-link">Kembali</a>
    </div>

    <form action="{{ $action }}" method="POST" class="form-grid two-columns">
        @csrf
        <label>
            <span>Anggota</span>
            <select name="user_id" required>
                <option value="">Pilih anggota</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" @selected(old('user_id') == $member->id)>{{ $member->name }} - {{ $member->class_name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Buku</span>
            <select name="book_id" required>
                <option value="">Pilih buku</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)>{{ $book->title }} (stok {{ $book->stock_available }})</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Tanggal Pinjam</span>
            <input type="date" name="borrowed_at" value="{{ old('borrowed_at', now()->toDateString()) }}" required>
        </label>
        <label>
            <span>Tanggal Jatuh Tempo</span>
            <input type="date" name="due_at" value="{{ old('due_at', now()->addDays(7)->toDateString()) }}" required>
        </label>
        <label class="full">
            <span>Catatan</span>
            <textarea name="note" rows="4">{{ old('note') }}</textarea>
        </label>
        <button type="submit" class="button full">Simpan Transaksi</button>
    </form>
</section>
