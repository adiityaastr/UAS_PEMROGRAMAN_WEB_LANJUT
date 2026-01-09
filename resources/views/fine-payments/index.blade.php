@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Pembayaran Denda</h1>
        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Section: Peminjaman dengan Denda Belum Dibayar -->
    <div class="glass fade-in" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: var(--text-color);">Peminjaman dengan Denda Belum Dibayar</h3>
            @if($loansWithFines->count() > 0)
                <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); font-weight: 600;">
                    {{ $loansWithFines->count() }} peminjaman
                </span>
            @endif
        </div>

        @if($loansWithFines->count() > 0)

        <!-- Search for loans -->
        <form method="GET" action="{{ route('fine-payments.index') }}" style="margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cari Peminjaman</label>
                    <input type="text" name="search_loan" class="form-control" 
                        value="{{ request('search_loan') }}" 
                        placeholder="Cari nama peminjam, NIM, atau judul buku...">
                    @if(request('search') || request('status'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search_loan'))
                        <a href="{{ route('fine-payments.index', ['search' => request('search'), 'status' => request('status')]) }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tanggal Kembali</th>
                        <th>Total Denda</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loansWithFines as $loan)
                        @php
                            $totalFine = $loan->fine ?? $loan->calculateFine();
                            $paidFine = $loan->getTotalPaidFines();
                            $remainingFine = $loan->getRemainingFine();
                            $isOverdue = $loan->isOverdue();
                            $daysLate = $loan->getDaysLate();
                        @endphp
                        <tr style="{{ $isOverdue ? 'background: rgba(239, 68, 68, 0.05);' : '' }}">
                            <td>
                                <strong>{{ $loan->user->name }}</strong>
                                @if($loan->user->kode_unik)
                                    <br><small style="color: var(--text-muted); font-family: monospace;">{{ $loan->user->kode_unik }}</small>
                                @endif
                            </td>
                            <td>{{ $loan->book->title }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}
                                @if($isOverdue)
                                    <br><small style="color: var(--danger-color); font-weight: 600;">Terlambat {{ $daysLate }} hari</small>
                                @endif
                            </td>
                            <td style="color: var(--danger-color); font-weight: bold;">
                                Rp {{ number_format($totalFine, 0, ',', '.') }}
                            </td>
                            <td style="color: var(--success-color);">
                                Rp {{ number_format($paidFine, 0, ',', '.') }}
                            </td>
                            <td style="color: var(--warning-color); font-weight: bold; font-size: 1.1rem;">
                                Rp {{ number_format($remainingFine, 0, ',', '.') }}
                            </td>
                            <td>
                                <a href="{{ route('fine-payments.create', $loan) }}" class="btn btn-primary btn-sm">
                                    Bayar Denda
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                <p style="margin: 0; font-size: 1.1rem;">✅ Tidak ada peminjaman dengan denda yang belum dibayar.</p>
            </div>
        @endif
    </div>

    <!-- Section: Riwayat Pembayaran -->
    <div class="glass fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: var(--text-color);">Riwayat Pembayaran Denda</h3>
        </div>

        <!-- Search Section -->
        <form method="GET" action="{{ route('fine-payments.index') }}" id="searchForm" style="margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" 
                        value="{{ request('search') }}" 
                        placeholder="Nama peminjam, NIM, atau judul buku...">
                    @if(request('search_loan'))
                        <input type="hidden" name="search_loan" value="{{ request('search_loan') }}">
                    @endif
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('fine-payments.index', ['search_loan' => request('search_loan')]) }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Peminjam</th>
                        <th>NIM</th>
                        <th>Buku</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Dibayar Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $payment->loan->user->name }}</strong></td>
                            <td>
                                @if($payment->loan->user->kode_unik)
                                    <span style="font-family: monospace; font-weight: 600;">{{ $payment->loan->user->kode_unik }}</span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>{{ $payment->loan->book->title }}</td>
                            <td style="color: var(--success-color); font-weight: bold;">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($payment->payment_method == 'cash')
                                    <span class="badge">Tunai</span>
                                @elseif($payment->payment_method == 'qris')
                                    <span class="badge">QRIS</span>
                                @else
                                    <span class="badge">{{ ucfirst($payment->payment_method) }}</span>
                                @endif
                            </td>
                            <td>{{ $payment->paidBy->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Tidak ada data pembayaran denda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
@endsection
