@extends('layouts.app')

@section('content')
    <h1>Edit Buku</h1>

    <div class="glass fade-in" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('books.update', $book) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" class="form-control" value="{{ $book->code }}" disabled
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-family: monospace; letter-spacing: 0.05em; font-weight: 600; font-size: 1.1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Stok</label>
                    <input type="number" class="form-control" value="{{ $book->stock }}" disabled
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-weight: 600;">
                    <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">Gunakan tombol "Update Stok" di halaman daftar buku untuk mengubah stok</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Judul Buku <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $book->title) }}">
                @error('title')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Penulis <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="author" class="form-control" required value="{{ old('author', $book->author) }}">
                @error('author')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}"
                        placeholder="Nama penerbit">
                    @error('publisher')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', $book->year) }}"
                        placeholder="Contoh: 2024" min="1000" max="{{ date('Y') + 1 }}">
                    @error('year')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cetakan/Edisi</label>
                    <input type="text" name="edition" class="form-control" value="{{ old('edition', $book->edition) }}"
                        placeholder="Contoh: Cetakan ke-1, Edisi 2">
                    @error('edition')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}"
                        placeholder="Contoh: 978-602-8519-93-9">
                    @error('isbn')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Buku</button>
            </div>
        </form>
    </div>
@endsection