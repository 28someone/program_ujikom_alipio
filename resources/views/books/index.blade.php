@extends('layouts.app')

@section('title', 'Data Buku')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Pencarian Buku</p>
                <h2>{{ $isAdmin ? 'Kelola Data Buku' : 'Koleksi Buku' }}</h2>
            </div>
            @if($isAdmin)
                <a href="{{ route('books.create') }}" class="button">Tambah Buku</a>
            @endif
        </div>

        <form method="GET" class="filter-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, kode, penulis, penerbit">
            <select name="category">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="button">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Stok</th>
                        @if($isAdmin)
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->code }}</td>
                            <td>
                                <div class="book-table-entry">
                                    <img
                                        src="{{ asset($book->cover_image ?: 'images/book-covers/default-cover.svg') }}"
                                        alt="Cover {{ $book->title }}"
                                        class="book-thumb"
                                    >
                                    <div>
                                        <strong>{{ $book->title }}</strong>
                                        <p class="table-note">{{ $book->publisher }} {{ $book->year ? '- '.$book->year : '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $book->category?->name ?? '-' }}</td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->stock_available }}/{{ $book->stock_total }}</td>
                            @if($isAdmin)
                                <td class="actions">
                                    <a href="{{ route('books.edit', $book) }}" class="text-link">Edit</a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-link danger-link" onclick="return confirm('Hapus data buku ini?')">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isAdmin ? 6 : 5 }}" class="empty">Data buku belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $books->links() }}</div>
    </section>
@endsection
