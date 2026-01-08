@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Daftar Peminjaman</h1>
        <div style="display: flex; gap: 0.5rem;">
            @can('isPetugasOrAdmin')
                <a href="{{ route('loans.create') }}" class="btn btn-primary">Pinjam Baru</a>
                <a href="{{ route('fine-payments.index') }}" class="btn btn-secondary">Pembayaran Denda</a>
            @endcan
        </div>
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
                        <th>Perpanjangan</th>
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
                                    $paidFine = $loan->getTotalPaidFines();
                                    $remainingFine = $loan->getRemainingFine();
                                @endphp
                                @if($fine > 0)
                                    <div>
                                        <span style="color: var(--danger-color); font-weight: bold;">
                                            Rp {{ number_format($fine, 0, ',', '.') }}
                                        </span>
                                        @if($paidFine > 0)
                                            <br><small style="font-size: 0.75rem; color: var(--success-color);">
                                                Dibayar: Rp {{ number_format($paidFine, 0, ',', '.') }}
                                            </small>
                                            @if($remainingFine > 0)
                                                <br><small style="font-size: 0.75rem; color: var(--warning-color);">
                                                    Sisa: Rp {{ number_format($remainingFine, 0, ',', '.') }}
                                                </small>
                                            @endif
                                        @endif
                                        @if($loan->status == 'borrowed' && $paidFine == 0)
                                            <br><small style="font-size: 0.75rem; opacity: 0.8;">(+ Rp 2.000/hari)</small>
                                        @endif
                                    </div>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($loan->renewal_count > 0)
                                    <span style="color: var(--success-color); font-weight: 600;">
                                        Diperpanjang {{ $loan->renewal_count }}x
                                    </span>
                                    @if($loan->renewed_at)
                                        <br><small style="font-size: 0.75rem; color: var(--text-muted);">
                                            {{ \Carbon\Carbon::parse($loan->renewed_at)->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    @if($loan->status == 'borrowed')
                                        @can('isPetugasOrAdmin')
                                            <form action="{{ route('loans.return', $loan) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                                            </form>
                                            
                                            @php
                                                $canRenew = $loan->canBeRenewed();
                                            @endphp
                                            
                                            @if($canRenew)
                                                <button type="button" class="btn btn-warning btn-sm" 
                                                    onclick="showRenewModal({{ $loan->id }}, '{{ $loan->book->title }}', '{{ $loan->user->name }}', '{{ \Carbon\Carbon::parse($loan->return_date)->format('Y-m-d') }}')">
                                                    Perpanjang
                                                </button>
                                            @endif
                                        @endcan
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.875rem;">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
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

    <!-- Modal untuk perpanjangan peminjaman -->
    <div id="renewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass" style="max-width: 500px; width: 90%; padding: 2rem;">
            <h3 style="margin-top: 0;">Perpanjang Peminjaman</h3>
            <p><strong>Peminjam:</strong> <span id="renew-user-name"></span></p>
            <p><strong>Buku:</strong> <span id="renew-book-title"></span></p>
            <p><strong>Tanggal Kembali Saat Ini:</strong> <span id="renew-current-date"></span></p>
            
            <form id="renewForm" method="POST" style="margin-top: 1.5rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Durasi Perpanjangan (Hari) <span style="color: var(--danger-color);">*</span></label>
                    <input type="number" name="duration" class="form-control" required 
                        min="1" max="7" value="7" id="renew-duration">
                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                        Maksimal 7 hari. Peminjaman akan diperpanjang dari tanggal kembali saat ini.
                    </small>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Tanggal Kembali Baru:</label>
                    <input type="text" class="form-control" id="renew-new-date" readonly 
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-weight: bold;">
                </div>
                <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeRenewModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Perpanjang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentReturnDate = '';

        function showRenewModal(loanId, bookTitle, userName, returnDate) {
            currentReturnDate = returnDate;
            document.getElementById('renew-user-name').textContent = userName;
            document.getElementById('renew-book-title').textContent = bookTitle;
            
            // Format tanggal: YYYY-MM-DD to readable format
            const dateParts = returnDate.split('-');
            const returnDateObj = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[returnDateObj.getDay()];
            const day = returnDateObj.getDate();
            const month = months[returnDateObj.getMonth()];
            const year = returnDateObj.getFullYear();
            const formattedDate = dayName + ', ' + day + ' ' + month + ' ' + year;
            
            document.getElementById('renew-current-date').textContent = formattedDate;
            
            document.getElementById('renewForm').action = '/loans/' + loanId + '/renew';
            document.getElementById('renewModal').style.display = 'flex';
            
            // Update tanggal baru saat durasi berubah
            updateNewReturnDate();
        }

        function closeRenewModal() {
            document.getElementById('renewModal').style.display = 'none';
        }

        function updateNewReturnDate() {
            const duration = parseInt(document.getElementById('renew-duration').value) || 0;
            if (duration > 0 && currentReturnDate) {
                const dateParts = currentReturnDate.split('-');
                const returnDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                returnDate.setDate(returnDate.getDate() + duration);
                
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                const dayName = days[returnDate.getDay()];
                const day = returnDate.getDate();
                const month = months[returnDate.getMonth()];
                const year = returnDate.getFullYear();
                const formattedDate = dayName + ', ' + day + ' ' + month + ' ' + year;
                
                document.getElementById('renew-new-date').value = formattedDate;
            } else {
                document.getElementById('renew-new-date').value = '';
            }
        }

        // Update tanggal baru saat input durasi berubah
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('renewModal');
            if (modal) {
                const durationInput = document.getElementById('renew-duration');
                if (durationInput) {
                    durationInput.addEventListener('input', updateNewReturnDate);
                    durationInput.addEventListener('change', updateNewReturnDate);
                }
                
                // Close modal when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRenewModal();
                    }
                });
            }
        });
    </script>
@endsection