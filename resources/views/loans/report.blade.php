@extends('layouts.app')

@section('content')
    <h1>Laporan Peminjaman Harian</h1>

    <div class="glass fade-in">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Total Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyLoans as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report->date)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ $report->total }} buku</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada data
                                laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection