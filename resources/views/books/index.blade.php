@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Daftar Buku</h1>
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
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td><span class="badge"
                                    style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color); font-family: monospace;">{{ $book->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $book->title }}</strong>
                                @if($book->edition)
                                    <br><small style="color: var(--text-muted);">Edisi: {{ $book->edition }}</small>
                                @endif
                                @if($book->isbn)
                                    <br><small style="color: var(--text-muted); font-family: monospace;">ISBN: {{ $book->isbn }}</small>
                                @endif
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>
                                {{ $book->publisher ?? '-' }}
                            </td>
                            <td>
                                {{ $book->year ?? '-' }}
                            </td>
                            <td>
                                @if($book->stock > 0)
                                    <span class="badge badge-success">{{ $book->stock }}</span>
                                @else
                                    <span class="badge badge-warning">Habis</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn btn-success btn-sm" 
                                        onclick="showUpdateStockModal({{ $book->id }}, '{{ $book->title }}', {{ $book->stock }})">
                                        Update Stok
                                    </button>
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada buku
                                yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Update Stok -->
    <div id="stockModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="glass" style="max-width: 500px; width: 90%; margin: auto;">
            <h3 style="margin-top: 0;">Update Stok Buku</h3>
            <form id="stockForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" id="modal-book-title" class="form-control" readonly
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Saat Ini</label>
                    <input type="number" id="modal-current-stock" class="form-control" readonly
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label class="form-label">Tambah/Kurangi Stok</label>
                    <input type="number" id="modal-stock-change" name="stock_change" class="form-control" required
                        placeholder="Contoh: +10 untuk menambah, -5 untuk mengurangi">
                    <small style="color: var(--text-muted);">Masukkan angka positif untuk menambah, negatif untuk mengurangi</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Baru (Preview)</label>
                    <input type="number" id="modal-new-stock" class="form-control" readonly
                        style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-weight: 600;">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeStockModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Stok</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateStockPreview() {
            const currentStock = parseInt(document.getElementById('modal-current-stock').value) || 0;
            const stockChange = parseInt(document.getElementById('modal-stock-change').value) || 0;
            const newStock = currentStock + stockChange;
            const newStockInput = document.getElementById('modal-new-stock');
            
            if (newStockInput) {
                newStockInput.value = newStock;
                
                // Validasi: stok baru tidak boleh negatif
                if (newStock < 0) {
                    newStockInput.style.color = 'var(--danger-color)';
                } else {
                    newStockInput.style.color = 'var(--text-color)';
                }
            }
        }

        function showUpdateStockModal(bookId, bookTitle, currentStock) {
            document.getElementById('stockForm').action = `/books/${bookId}/update-stock`;
            document.getElementById('modal-book-title').value = bookTitle;
            document.getElementById('modal-current-stock').value = currentStock;
            document.getElementById('modal-stock-change').value = '';
            document.getElementById('modal-new-stock').value = currentStock;
            document.getElementById('modal-new-stock').style.color = 'var(--text-color)';
            document.getElementById('stockModal').style.display = 'flex';
            
            // Focus pada input stock change
            setTimeout(() => {
                document.getElementById('modal-stock-change').focus();
            }, 100);
        }

        function closeStockModal() {
            document.getElementById('stockModal').style.display = 'none';
        }

        // Setup event listeners saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const stockChangeInput = document.getElementById('modal-stock-change');
            const stockForm = document.getElementById('stockForm');
            const stockModal = document.getElementById('stockModal');
            
            // Update preview saat input berubah
            if (stockChangeInput) {
                stockChangeInput.addEventListener('input', updateStockPreview);
            }

            // Tutup modal saat klik di luar
            if (stockModal) {
                stockModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeStockModal();
                    }
                });
            }

            // Validasi form sebelum submit
            if (stockForm) {
                stockForm.addEventListener('submit', function(e) {
                    const currentStock = parseInt(document.getElementById('modal-current-stock').value) || 0;
                    const stockChange = parseInt(document.getElementById('modal-stock-change').value) || 0;
                    const newStock = currentStock + stockChange;
                    
                    if (newStock < 0) {
                        e.preventDefault();
                        alert('Stok tidak boleh negatif!');
                        return false;
                    }
                });
            }
        });
    </script>

    <style>
        .modal {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection