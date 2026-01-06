@extends('layouts.app')

@section('content')
    <h1>Tambah Buku Baru</h1>

    <div class="glass fade-in" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('books.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" id="preview-code" class="form-control" value="{{ $previewCode ?? 'B001' }}" disabled
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-family: monospace; letter-spacing: 0.05em; font-weight: 600; font-size: 1.1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Stok Awal <span style="color: var(--danger-color);">*</span></label>
                    <input type="number" name="stock" class="form-control" required min="2" 
                        value="{{ old('stock') }}" placeholder="Minimal 2" autofocus>
                    @error('stock')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Judul Buku <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="title" class="form-control" required value="{{ old('title') }}"
                    placeholder="Masukkan judul buku lengkap">
                @error('title')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Penulis <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="author" class="form-control" required value="{{ old('author') }}"
                    placeholder="Nama penulis lengkap">
                @error('author')
                    <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}"
                        placeholder="Nama penerbit">
                    @error('publisher')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year') }}"
                        placeholder="Contoh: 2024" min="1000" max="{{ date('Y') + 1 }}">
                    @error('year')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Cetakan/Edisi</label>
                    <input type="text" name="edition" class="form-control" value="{{ old('edition') }}"
                        placeholder="Contoh: Cetakan ke-1, Edisi 2">
                    @error('edition')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}"
                        placeholder="Contoh: 978-602-8519-93-9">
                    @error('isbn')
                        <small style="color: var(--danger-color); display: block; margin-top: 0.25rem;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Buku</button>
            </div>
        </form>
    </div>
@endsection