@extends('layouts.app')

@section('title', 'Log Aktivitas Pengguna')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Log Aktivitas</p>
                <h2>Riwayat seluruh aktivitas pengguna</h2>
            </div>
        </div>

        <form method="GET" class="filter-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, nama, username, atau role">
            <button type="submit" class="button">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td>{{ $log->user?->name ?? 'Pengguna terhapus' }}</td>
                            <td>{{ strtoupper($log->user?->role ?? '-') }}</td>
                            <td><span class="badge">{{ strtoupper(str_replace('.', ' ', $log->action)) }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>
                                @if(! empty($log->properties))
                                    @foreach($log->properties as $key => $value)
                                        <p class="table-note">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</p>
                                    @endforeach
                                @else
                                    <span class="table-note">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada log aktivitas pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $logs->links() }}</div>
    </section>
@endsection
