@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Daftar Peminjaman</h1>
        @can('isPetugas')
            <a href="{{ route('loans.create') }}" class="btn btn-primary">Pinjam Baru</a>
        @endcan
    </div>

    <!-- Search and Filter Section -->
    <div class="glass fade-in" style="margin-bottom: 1.5rem;">
        <!-- Quick Filter Buttons -->
        <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
            <a href="{{ route('loans.index', ['status' => 'borrowed']) }}" 
                class="btn {{ request('status') == 'borrowed' && !request('search') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                Perlu Dikembalikan
            </a>
            <a href="{{ route('loans.index', ['status' => 'returned']) }}" 
                class="btn {{ request('status') == 'returned' && !request('search') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                Sudah Dikembalikan
            </a>
            <a href="{{ route('loans.index') }}" 
                class="btn {{ !request('status') && !request('search') ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                Semua Data
            </a>
        </div>

        <form method="GET" action="{{ route('loans.index') }}" id="searchForm">
            <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cari Peminjam/Buku</label>
                    <input type="text" name="search" class="form-control" 
                        value="{{ request('search') }}" 
                        placeholder="Nama peminjam, NIM (9 digit), judul buku, atau kode buku..."
                        autofocus>
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" onchange="document.getElementById('searchForm').submit();">
                        <option value="">Semua Status</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="glass fade-in">
        @if(request('search') || request('status'))
            <div style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; border-left: 3px solid var(--primary-color);">
                <strong>Filter Aktif:</strong>
                @if(request('search'))
                    <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: var(--primary-color); margin-left: 0.5rem;">
                        Pencarian: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('status'))
                    <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: var(--primary-color); margin-left: 0.5rem;">
                        Status: {{ request('status') == 'borrowed' ? 'Dipinjam' : 'Dikembalikan' }}
                    </span>
                @endif
            </div>
        @endif

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Kode Unik</th>
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
                        @php
                            $searchTerm = request('search');
                            $highlightName = $searchTerm ? str_ireplace($searchTerm, "<mark style='background: rgba(245, 158, 11, 0.3); padding: 0.1rem 0.2rem; border-radius: 0.25rem;'>$searchTerm</mark>", e($loan->user->name)) : e($loan->user->name);
                            $highlightBook = $searchTerm ? str_ireplace($searchTerm, "<mark style='background: rgba(245, 158, 11, 0.3); padding: 0.1rem 0.2rem; border-radius: 0.25rem;'>$searchTerm</mark>", e($loan->book->title)) : e($loan->book->title);
                            $highlightCode = $searchTerm && $loan->user->kode_unik ? str_ireplace($searchTerm, "<mark style='background: rgba(245, 158, 11, 0.3); padding: 0.1rem 0.2rem; border-radius: 0.25rem;'>$searchTerm</mark>", e($loan->user->kode_unik)) : ($loan->user->kode_unik ? e($loan->user->kode_unik) : '');
                            
                            $isLate = $loan->isOverdue();
                            $daysLate = (int) $loan->getDaysLate(); // Pastikan integer
                        @endphp
                        <tr style="{{ $isLate ? 'background: rgba(239, 68, 68, 0.05);' : '' }}">
                            <td><strong>{!! $highlightName !!}</strong></td>
                            <td>
                                @if($loan->user->kode_unik)
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color); font-family: monospace; letter-spacing: 0.05em; font-weight: 600;">
                                        {!! $highlightCode ?: e($loan->user->kode_unik) !!}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>{!! $highlightBook !!}</td>
                            <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}
                                @if($isLate)
                                    <br><small style="color: var(--danger-color); font-weight: 600;">Terlambat {{ $daysLate }} hari</small>
                                @endif
                            </td>
                            <td>
                                @if($loan->status == 'borrowed')
                                    <span class="badge badge-warning">Dipinjam</span>
                                @else
                                    <span class="badge badge-success">Dikembalikan</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $fine = $loan->calculateFine();
                                @endphp
                                @if($fine > 0)
                                    <span style="color: var(--danger-color); font-weight: bold;">
                                        Rp {{ number_format($fine, 0, ',', '.') }}
                                        @if($loan->status == 'borrowed')
                                            <br><small style="font-size: 0.75rem; opacity: 0.8;">(+ Rp 2.000/hari)</small>
                                        @endif
                                    </span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($loan->status == 'borrowed')
                                    @can('isPetugas')
                                        <div class="table-actions">
                                            <form action="{{ route('loans.return', $loan) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                                            </form>
                                        </div>
                                    @endcan
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.875rem;">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                @if(request('search') || request('status'))
                                    Tidak ada data peminjaman yang sesuai dengan filter.
                                @else
                                    Belum ada data peminjaman.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection