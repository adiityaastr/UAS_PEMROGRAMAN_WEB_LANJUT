@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Pembayaran Denda</h1>
        <a href="{{ route('fine-payments.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="glass fade-in" style="max-width: 700px; margin: 0 auto;">
        <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-radius: 0.5rem; border-left: 3px solid var(--primary-color);">
            <h3 style="margin-top: 0; margin-bottom: 0.5rem;">Informasi Peminjaman</h3>
            <p style="margin: 0.25rem 0;"><strong>Peminjam:</strong> {{ $loan->user->name }}</p>
            <p style="margin: 0.25rem 0;"><strong>Buku:</strong> {{ $loan->book->title }}</p>
            <p style="margin: 0.25rem 0;"><strong>Tanggal Pinjam:</strong> {{ \Carbon\Carbon::parse($loan->loan_date)->translatedFormat('d F Y') }}</p>
            <p style="margin: 0.25rem 0;"><strong>Tanggal Kembali:</strong> {{ \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d F Y') }}</p>
            @if($loan->isOverdue())
                <p style="margin: 0.25rem 0; color: var(--danger-color);"><strong>Terlambat:</strong> {{ $loan->getDaysLate() }} hari</p>
            @endif
        </div>

        <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 0.5rem; border-left: 3px solid var(--danger-color);">
            <h3 style="margin-top: 0; margin-bottom: 0.5rem;">Informasi Denda</h3>
            @php
                $totalFine = $loan->fine ?? $loan->calculateFine();
                $paidFine = $loan->getTotalPaidFines();
            @endphp
            <p style="margin: 0.25rem 0;"><strong>Total Denda:</strong> <span style="color: var(--danger-color); font-weight: bold; font-size: 1.2rem;">Rp {{ number_format($totalFine, 0, ',', '.') }}</span></p>
            @if($paidFine > 0)
                <p style="margin: 0.25rem 0;"><strong>Sudah Dibayar:</strong> <span style="color: var(--success-color); font-weight: bold;">Rp {{ number_format($paidFine, 0, ',', '.') }}</span></p>
            @endif
            <p style="margin: 0.25rem 0;"><strong>Sisa Denda:</strong> <span style="color: var(--warning-color); font-weight: bold; font-size: 1.1rem;">Rp {{ number_format($remainingFine, 0, ',', '.') }}</span></p>
        </div>

        <form action="{{ route('fine-payments.store', $loan) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Jumlah Pembayaran (Rp) <span style="color: var(--danger-color);">*</span></label>
                <input type="number" name="amount" class="form-control" required 
                    min="1" max="{{ $remainingFine }}" 
                    value="{{ old('amount', $remainingFine) }}" 
                    placeholder="Maksimal: Rp {{ number_format($remainingFine, 0, ',', '.') }}">
                @error('amount')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Pembayaran <span style="color: var(--danger-color);">*</span></label>
                <input type="date" name="payment_date" class="form-control" required 
                    value="{{ old('payment_date', date('Y-m-d')) }}" 
                    max="{{ date('Y-m-d') }}">
                @error('payment_date')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Metode Pembayaran <span style="color: var(--danger-color);">*</span></label>
                <select name="payment_method" class="form-control" required>
                    <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
                @error('payment_method')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" 
                    placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                @error('notes')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('fine-payments.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
@endsection
