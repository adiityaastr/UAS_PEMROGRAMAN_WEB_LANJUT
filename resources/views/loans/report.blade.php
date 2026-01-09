@extends('layouts.app')

@section('content')
    <style>
        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }
            
            /* Hide sidebar and navigation elements */
            .sidebar,
            .no-print,
            .btn,
            .page-header,
            .form-group,
            form,
            .app-container > aside {
                display: none !important;
            }
            
            /* Reset body and main content for print */
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            
            .app-container {
                display: block !important;
            }
            
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                overflow: visible !important;
            }
            
            .print-section {
                position: relative !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .glass {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                background: white !important;
                page-break-inside: avoid;
                margin-bottom: 1rem;
                padding: 1rem;
            }
            
            .table {
                border-collapse: collapse;
                width: 100%;
                font-size: 10px;
                margin: 0;
            }
            
            .table th, .table td {
                border: 1px solid #ddd;
                padding: 5px;
                text-align: left;
            }
            
            .table th {
                background-color: #f2f2f2 !important;
                color: #000 !important;
                font-weight: bold;
            }
            
            .stat-card {
                border: 1px solid #ddd !important;
                background: white !important;
                page-break-inside: avoid;
                padding: 0.75rem;
            }
            
            h1, h2, h3 {
                color: #000 !important;
                page-break-after: avoid;
                margin: 0.5rem 0;
            }
            
            h1 {
                font-size: 18px;
            }
            
            h2 {
                font-size: 14px;
            }
            
            h3 {
                font-size: 12px;
            }
            
            .grid {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 0.5rem !important;
                margin-bottom: 1rem;
            }
            
            .stat-value {
                color: #000 !important;
                font-size: 16px;
            }
            
            .stat-label {
                color: #666 !important;
                font-size: 11px;
            }
            
            /* Ensure all text is black for print */
            p, span, div, td, th {
                color: #000 !important;
            }
            
            /* Page break handling */
            .glass {
                page-break-inside: avoid;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
    </style>

    <div class="print-section">
        <div class="no-print" style="margin-bottom: 1.5rem;">
            <div class="page-header">
                <h1>Laporan Peminjaman</h1>
                <button onclick="window.print()" class="btn btn-primary">
                    Cetak Laporan
                </button>
            </div>
        </div>

        <!-- Report Header -->
        <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--glass-border);">
            <h1 style="margin-bottom: 0.5rem;">LAPORAN PEMINJAMAN BUKU</h1>
            <h2 style="margin: 0; font-size: 1.2rem; color: var(--text-muted); font-weight: normal;">
                Perpustakaan Kampus
            </h2>
            <p style="margin: 0.5rem 0 0 0; color: var(--text-muted);">
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
            </p>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">
                Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }}
            </p>
        </div>

        <div class="no-print" style="margin-bottom: 1.5rem;">
            <!-- Filter Section -->
            <div class="glass fade-in" style="margin-bottom: 1.5rem;">
                <h3 style="margin-top: 0;">Filter Periode</h3>
                <form method="GET" action="{{ route('loans.report') }}" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Pilih Bulan</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}" required>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: end;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('loans.report') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid" style="margin-bottom: 2rem;">
            <div class="glass stat-card fade-in">
                <div class="stat-label">Total Peminjaman</div>
                <div class="stat-value">{{ $totalLoans }}</div>
            </div>
            <div class="glass stat-card fade-in">
                <div class="stat-label">Masih Dipinjam</div>
                <div class="stat-value" style="color: var(--warning-color);">{{ $borrowedCount }}</div>
            </div>
            <div class="glass stat-card fade-in">
                <div class="stat-label">Sudah Dikembalikan</div>
                <div class="stat-value" style="color: var(--success-color);">{{ $returnedCount }}</div>
            </div>
            <div class="glass stat-card fade-in">
                <div class="stat-label">Terlambat</div>
                <div class="stat-value" style="color: var(--danger-color);">{{ $overdueCount }}</div>
            </div>
            <div class="glass stat-card fade-in">
                <div class="stat-label">Total Denda</div>
                <div class="stat-value" style="color: var(--danger-color);">Rp {{ number_format($totalFines, 0, ',', '.') }}</div>
            </div>
            <div class="glass stat-card fade-in">
                <div class="stat-label">Denda Belum Dibayar</div>
                <div class="stat-value" style="color: var(--danger-color);">Rp {{ number_format($pendingFines, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Daily Statistics -->
        <div class="glass fade-in" style="margin-bottom: 2rem;">
            <h3 style="margin-top: 0;">Statistik Harian</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total Peminjaman</th>
                            <th>Masih Dipinjam</th>
                            <th>Sudah Dikembalikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyStats as $stat)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($stat['date'])->translatedFormat('l, d F Y') }}</td>
                                <td><strong>{{ $stat['total'] }}</strong></td>
                                <td>{{ $stat['borrowed'] }}</td>
                                <td>{{ $stat['returned'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Most Popular Books -->
        @if($popularBooks->count() > 0)
        <div class="glass fade-in" style="margin-bottom: 2rem;">
            <h3 style="margin-top: 0;">Buku Terpopuler (Top 5)</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Jumlah Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($popularBooks as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $item['book']->title }}</strong></td>
                                <td>{{ $item['book']->author }}</td>
                                <td><strong>{{ $item['count'] }} kali</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Detailed Loan List -->
        <div class="glass fade-in">
            <h3 style="margin-top: 0;">Detail Peminjaman</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Peminjam</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $index => $loan)
                            @php
                                $isLate = $loan->isOverdue();
                                $daysLate = (int) $loan->getDaysLate();
                                $fine = $loan->calculateFine();
                            @endphp
                            <tr style="{{ $isLate ? 'background: rgba(239, 68, 68, 0.05);' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}</td>
                                <td><strong>{{ $loan->user->name }}</strong></td>
                                <td>
                                    @if($loan->user->kode_unik)
                                        <span style="font-family: monospace;">{{ $loan->user->kode_unik }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $loan->user->program_studi ?? '-' }}</td>
                                <td>{{ $loan->book->title }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}
                                    @if($isLate)
                                        <br><small style="color: var(--danger-color); font-weight: 600;">Terlambat {{ $daysLate }} hari</small>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->status == 'borrowed')
                                        <span style="color: var(--warning-color); font-weight: 600;">Dipinjam</span>
                                    @else
                                        <span style="color: var(--success-color); font-weight: 600;">Dikembalikan</span>
                                        @if($loan->actual_return_date)
                                            <br><small style="color: var(--text-muted);">
                                                {{ \Carbon\Carbon::parse($loan->actual_return_date)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($fine > 0)
                                        <span style="color: var(--danger-color); font-weight: bold;">
                                            Rp {{ number_format($fine, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Tidak ada data peminjaman untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--glass-border); text-align: center; color: var(--text-muted);">
            <p style="margin: 0;">Laporan ini dibuat secara otomatis oleh Sistem Perpustakaan Kampus</p>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                Halaman 1 | Total: {{ $totalLoans }} peminjaman
            </p>
        </div>
    </div>
@endsection
