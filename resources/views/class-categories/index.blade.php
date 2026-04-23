@extends('layouts.app')

@section('title', 'Kategori Kelas')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Kategori Kelas</p>
                <h2>Kelola kategori kelas anggota</h2>
            </div>
            <div class="action-row">
                <a href="{{ route('members.index') }}" class="button button-outline-inline">Kembali</a>
                <a href="{{ route('class-categories.create') }}" class="button">Tambah Kategori</a>
            </div>
        </div>

        <form method="GET" class="filter-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori kelas">
            <button type="submit" class="button">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Jumlah Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classCategories as $classCategory)
                        <tr>
                            <td>{{ $classCategory->name }}</td>
                            <td>{{ $classCategory->users_count }}</td>
                            <td class="actions">
                                <a href="{{ route('class-categories.edit', $classCategory) }}" class="text-link">Edit</a>
                                <form action="{{ route('class-categories.destroy', $classCategory) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-link danger-link" onclick="return confirm('Hapus kategori kelas ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum ada kategori kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $classCategories->links() }}</div>
    </section>
@endsection
