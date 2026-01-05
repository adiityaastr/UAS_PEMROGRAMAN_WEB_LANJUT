@extends('layouts.app')

@section('content')
    <h1 style="margin-bottom: 2rem;">Buat Peminjaman Baru</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('loans.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Anggota</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Pilih Anggota</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}
                            ({{ ucfirst($user->role) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Buku</label>
                <select name="book_id" class="form-control" required>
                    <option value="">Pilih Buku</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>{{ $book->title }}
                            (Stok: {{ $book->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Pinjam</label>
                <input type="date" name="loan_date" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Kembali (Jadwal)</label>
                <input type="date" name="return_date" class="form-control" required
                    value="{{ date('Y-m-d', strtotime('+7 days')) }}">
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                <a href="{{ route('loans.index') }}" class="btn" style="background: rgba(148, 163, 184, 0.1);">Batal</a>
            </div>
        </form>
    </div>
@endsection