<section class="panel form-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Form Anggota</p>
            <h2>{{ $member->exists ? 'Edit Anggota' : 'Tambah Anggota' }}</h2>
        </div>
        <a href="{{ route('members.index') }}" class="text-link">Kembali</a>
    </div>

    <form action="{{ $action }}" method="POST" class="form-grid two-columns">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif
        <label>
            <span>Nama Lengkap</span>
            <input type="text" name="name" value="{{ old('name', $member->name) }}" required>
        </label>
        <label>
            <span>Username</span>
            <input type="text" name="username" value="{{ old('username', $member->username) }}" required>
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $member->email) }}">
        </label>
        <label>
            <span>NIS / ID Siswa</span>
            <input type="text" name="student_id" value="{{ old('student_id', $member->student_id) }}" required>
        </label>
        <label>
            <span>Kategori Kelas</span>
            <select name="class_category_id" required>
                <option value="">Pilih Kelas</option>
                @foreach($classCategories as $classCategory)
                    <option value="{{ $classCategory->id }}" @selected(old('class_category_id', $member->class_category_id) == $classCategory->id)>{{ $classCategory->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Nomor Telepon</span>
            <input type="text" name="phone" value="{{ old('phone', $member->phone) }}">
        </label>
        <label class="full">
            <span>Alamat</span>
            <textarea name="address" rows="3">{{ old('address', $member->address) }}</textarea>
        </label>
        <label>
            <span>Password {{ $member->exists ? '(kosongkan jika tidak diubah)' : '' }}</span>
            <input type="password" name="password" {{ $member->exists ? '' : 'required' }}>
        </label>
        <label>
            <span>Konfirmasi Password</span>
            <input type="password" name="password_confirmation" {{ $member->exists ? '' : 'required' }}>
        </label>
        <button type="submit" class="button full">{{ $member->exists ? 'Simpan Perubahan' : 'Tambah Anggota' }}</button>
    </form>
</section>
