<section class="panel form-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Form Buku</p>
            <h2>{{ $book->exists ? 'Edit Data Buku' : 'Tambah Data Buku' }}</h2>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-link">Kembali</a>
    </div>

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="form-grid two-columns">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif
        <label>
            <span>Kode Buku</span>
            <input type="text" name="code" value="{{ old('code', $book->code) }}" required>
        </label>
        <label>
            <span>Kategori</span>
            <select name="category_id">
                <option value="">Pilih kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Judul Buku</span>
            <input type="text" name="title" value="{{ old('title', $book->title) }}" required>
        </label>
        <label>
            <span>Penulis</span>
            <input type="text" name="author" value="{{ old('author', $book->author) }}" required>
        </label>
        <label>
            <span>Penerbit</span>
            <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}">
        </label>
        <label>
            <span>Tahun</span>
            <input type="number" name="year" value="{{ old('year', $book->year) }}">
        </label>
        <label>
            <span>Upload Cover</span>
            <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,.svg,image/*">
            <small class="field-note">Pilih gambar langsung dari folder komputer Anda. Format: JPG, PNG, WEBP, atau SVG.</small>
        </label>
        <label>
            <span>Lokasi Rak</span>
            <input type="text" name="rack_location" value="{{ old('rack_location', $book->rack_location) }}">
        </label>
        @if($book->cover_image)
            <label class="full">
                <span>Cover Saat Ini</span>
                <div class="cover-preview-box">
                    <img src="{{ asset($book->cover_image) }}" alt="Cover {{ $book->title }}" class="cover-preview-image">
                    <div>
                        <strong>{{ $book->title ?: 'Cover buku' }}</strong>
                        <p class="table-note">Upload file baru jika ingin mengganti cover yang sekarang.</p>
                    </div>
                </div>
            </label>
        @endif
        <label>
            <span>Stok Total</span>
            <input type="number" name="stock_total" value="{{ old('stock_total', $book->stock_total ?: 1) }}" min="1" required>
        </label>
        <label class="full">
            <span>Deskripsi</span>
            <textarea name="description" rows="4">{{ old('description', $book->description) }}</textarea>
        </label>
        <button type="submit" class="button full">{{ $book->exists ? 'Simpan Perubahan' : 'Tambah Buku' }}</button>
    </form>
</section>
