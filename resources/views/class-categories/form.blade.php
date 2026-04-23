<section class="panel form-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Kategori Kelas</p>
            <h2>{{ $classCategory->exists ? 'Edit Kategori Kelas' : 'Tambah Kategori Kelas' }}</h2>
        </div>
        <a href="{{ route('class-categories.index') }}" class="text-link">Kembali</a>
    </div>

    <form action="{{ $action }}" method="POST" class="form-grid">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <label>
            <span>Nama Kategori Kelas</span>
            <input type="text" name="name" value="{{ old('name', $classCategory->name) }}" placeholder="Contoh: X PPLG 1" required>
        </label>

        <button type="submit" class="button full">{{ $classCategory->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
    </form>
</section>
