@extends('layouts.app')

@section('content')
    <h1 style="margin-bottom: 2rem;">Edit Buku</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('books.update', $book) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kode Buku</label>
                <input type="text" name="code" class="form-control" required value="{{ old('code', $book->code) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $book->title) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Penulis</label>
                <input type="text" name="author" class="form-control" required value="{{ old('author', $book->author) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Stok</label>
                <input type="number" name="stock" class="form-control" required min="0"
                    value="{{ old('stock', $book->stock) }}">
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Perbarui Buku</button>
                <a href="{{ route('books.index') }}" class="btn" style="background: rgba(148, 163, 184, 0.1);">Batal</a>
            </div>
        </form>
    </div>
@endsection