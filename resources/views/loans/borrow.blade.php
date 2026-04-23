@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Peminjaman Buku</p>
                <h2>Pilih buku yang ingin dipinjam</h2>
            </div>
        </div>

        <div class="card-grid">
            @forelse($books as $book)
                <article class="book-card">
                    <div class="book-card-media">
                        <img
                            src="{{ asset($book->cover_image ?: 'images/book-covers/default-cover.svg') }}"
                            alt="Cover {{ $book->title }}"
                            class="book-card-cover"
                        >
                    </div>
                    <div class="book-card-body">
                        <span class="book-tag">{{ $book->category?->name ?? 'Umum' }}</span>
                        <h3>{{ $book->title }}</h3>
                        <p class="book-author">{{ $book->author }}</p>
                        <ul class="book-meta">
                            <li><span>Kode</span><strong>{{ $book->code }}</strong></li>
                            <li><span>Stok</span><strong>{{ $book->stock_available }}</strong></li>
                            <li><span>Rak</span><strong>{{ $book->rack_location ?: '-' }}</strong></li>
                        </ul>
                    </div>
                    <form action="{{ route('loans.borrow-store') }}" method="POST" class="compact-form book-card-action">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="borrowed_at" value="{{ now()->toDateString() }}">
                        <input type="hidden" name="due_at" value="{{ now()->addDays(7)->toDateString() }}">
                        <button type="submit" class="button full-width">Pinjam Sekarang</button>
                    </form>
                </article>
            @empty
                <p class="empty">Tidak ada buku yang tersedia untuk dipinjam.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $books->links() }}</div>
    </section>
@endsection
