@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>

    <div class="grid">
        <div class="glass stat-card fade-in" style="animation-delay: 0.1s;">
            <div class="stat-label">Total Buku</div>
            <div class="stat-value">{{ $totalBooks }}</div>
        </div>
        <div class="glass stat-card fade-in" style="animation-delay: 0.2s;">
            <div class="stat-label">Total Peminjaman</div>
            <div class="stat-value">{{ $totalLoans }}</div>
        </div>
    </div>

    <div class="grid" style="margin-top: 2rem;">
        <div class="glass fade-in" style="animation-delay: 0.3s;">
            <h3 style="margin-top: 0;">Buku Terpopuler</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularBooks as $book)
                            <tr>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->loans_count }} kali</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-muted);">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass fade-in" style="animation-delay: 0.4s;">
            <h3 style="margin-top: 0;">Peminjaman Terbaru</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLoans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->book->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection