@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0;">Daftar Buku</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">Tambah Buku</a>
    </div>

    <div class="glass fade-in">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td><span class="badge"
                                    style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color);">{{ $book->code }}</span>
                            </td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>
                                @if($book->stock > 0)
                                    <span class="badge badge-success">{{ $book->stock }}</span>
                                @else
                                    <span class="badge badge-warning">Habis</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-primary"
                                    style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada buku
                                yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection