@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0;">Daftar Peminjaman</h1>
        @can('isPetugas')
            <a href="{{ route('loans.create') }}" class="btn btn-primary">Pinjam Baru</a>
        @endcan
    </div>

    <div class="glass fade-in">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali (Jadwal)</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->book->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($loan->status == 'borrowed')
                                    <span class="badge badge-warning">Dipinjam</span>
                                @else
                                    <span class="badge badge-success">Dikembalikan</span>
                                @endif
                            </td>
                            <td>
                                @if($loan->status == 'borrowed')
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($loan->return_date);
                                        $now = now();
                                        $fine = 0;
                                        if ($now->gt($dueDate)) {
                                            $daysLate = $now->diffInDays($dueDate);
                                            $fine = $daysLate * 2000;
                                        }
                                    @endphp
                                    @if($fine > 0)
                                        <span style="color: var(--danger-color); font-weight: bold;">Est. Rp {{ number_format($fine, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                @else
                                    @if($loan->fine > 0)
                                        <span style="color: var(--danger-color); font-weight: bold;">Rp {{ number_format($loan->fine, 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($loan->status == 'borrowed')
                                    @can('isPetugas')
                                        <form action="{{ route('loans.return', $loan) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success"
                                                style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Kembalikan</button>
                                        </form>
                                    @endcan
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada data
                                peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection