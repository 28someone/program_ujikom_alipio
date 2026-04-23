@extends('layouts.app')

@section('title', 'Kelola Anggota')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Kelola Anggota</p>
                <h2>Data siswa dan pengguna perpustakaan</h2>
            </div>
            <div class="action-row">
                <a href="{{ route('class-categories.index') }}" class="button button-outline-inline">Kategori Kelas</a>
                <a href="{{ route('members.create') }}" class="button">Tambah Anggota</a>
            </div>
        </div>

        <form method="GET" class="filter-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, NIS, kelas">
            <button type="submit" class="button">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->username }}</td>
                            <td>{{ $member->student_id }}</td>
                            <td>{{ $member->classCategory?->name ?? $member->class_name ?? '-' }}</td>
                            <td class="actions">
                                <a href="{{ route('members.edit', $member) }}" class="text-link">Edit</a>
                                <form action="{{ route('members.destroy', $member) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-link danger-link" onclick="return confirm('Hapus data anggota ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Belum ada anggota.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $members->links() }}</div>
    </section>
@endsection
