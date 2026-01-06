@extends('layouts.app')

@section('content')
    <h1>Buat Peminjaman Baru</h1>

    <div class="glass fade-in" style="max-width: 800px;">
        <form action="{{ route('loans.store') }}" method="POST" id="loanForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Anggota</label>
                <select name="user_id" id="user_id" class="form-control" required>
                    <option value="">Pilih Anggota</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}
                            ({{ ucfirst($user->role) }})</option>
                    @endforeach
                </select>
                <small id="loan-status" style="color: var(--text-muted); display: block; margin-top: 0.5rem;"></small>
            </div>

            <div class="form-group">
                <label class="form-label">Buku yang Dipinjam</label>
                <div id="books-container">
                    <div class="book-selection" style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: end;">
                        <select name="book_ids[]" class="form-control book-select" required>
                            <option value="">Pilih Buku</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" data-stock="{{ $book->stock }}">{{ $book->title }}
                                    (Stok: {{ $book->stock }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-danger btn-sm remove-book" style="display: none;">Hapus</button>
                    </div>
                </div>
                <button type="button" id="add-book" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;">+ Tambah Buku</button>
                <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">Maksimal 4 buku per transaksi</small>
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
            <div class="form-actions">
                <a href="{{ route('loans.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const booksContainer = document.getElementById('books-container');
            const addBookBtn = document.getElementById('add-book');
            const userSelect = document.getElementById('user_id');
            const loanStatus = document.getElementById('loan-status');
            let bookCount = 1;

            // Simpan template select pertama untuk clone
            const firstSelect = document.querySelector('.book-select');
            const selectTemplate = firstSelect.cloneNode(true);

            // Update loan status saat user dipilih
            userSelect.addEventListener('change', function() {
                updateLoanStatus();
            });

            function updateLoanStatus() {
                const userId = userSelect.value;
                if (!userId) {
                    loanStatus.textContent = '';
                    return;
                }

                fetch(`/api/user-loans/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        const activeLoans = data.active_loans || 0;
                        const remaining = Math.max(0, 4 - activeLoans);
                        loanStatus.textContent = `Peminjaman aktif: ${activeLoans}/4. Sisa kuota: ${remaining} buku.`;
                        if (activeLoans >= 4) {
                            loanStatus.style.color = 'var(--danger-color)';
                        } else {
                            loanStatus.style.color = 'var(--text-muted)';
                        }
                    })
                    .catch(() => {
                        loanStatus.textContent = '';
                    });
            }

            // Update dropdown untuk menghilangkan buku yang sudah dipilih
            function updateBookOptions() {
                const selects = document.querySelectorAll('.book-select');
                const selectedIds = Array.from(selects)
                    .map(select => select.value)
                    .filter(id => id !== '');

                selects.forEach(select => {
                    const currentValue = select.value;
                    const options = select.querySelectorAll('option[value]');
                    
                    options.forEach(option => {
                        const optionId = option.value;
                        if (optionId === '' || optionId === currentValue) {
                            option.style.display = '';
                        } else if (selectedIds.includes(optionId)) {
                            option.style.display = 'none';
                        } else {
                            option.style.display = '';
                        }
                    });
                });
            }

            // Tambah buku
            addBookBtn.addEventListener('click', function() {
                if (bookCount >= 4) {
                    alert('Maksimal 4 buku per transaksi');
                    return;
                }

                const newBookDiv = document.createElement('div');
                newBookDiv.className = 'book-selection';
                newBookDiv.style.cssText = 'display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: end;';
                
                const select = selectTemplate.cloneNode(true);
                select.value = '';
                select.name = 'book_ids[]';
                select.addEventListener('change', updateBookOptions);
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm remove-book';
                removeBtn.textContent = 'Hapus';
                removeBtn.addEventListener('click', function() {
                    newBookDiv.remove();
                    bookCount--;
                    updateRemoveButtons();
                    updateBookOptions();
                });

                newBookDiv.appendChild(select);
                newBookDiv.appendChild(removeBtn);
                booksContainer.appendChild(newBookDiv);
                bookCount++;
                updateRemoveButtons();
                updateBookOptions();
            });

            // Event listener untuk select pertama
            firstSelect.addEventListener('change', updateBookOptions);

            // Hapus buku
            booksContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-book')) {
                    if (bookCount <= 1) {
                        alert('Minimal harus memilih 1 buku');
                        return;
                    }
                    e.target.closest('.book-selection').remove();
                    bookCount--;
                    updateRemoveButtons();
                    updateBookOptions();
                }
            });

            function updateRemoveButtons() {
                const removeButtons = document.querySelectorAll('.remove-book');
                removeButtons.forEach(btn => {
                    btn.style.display = bookCount > 1 ? 'inline-block' : 'none';
                });
                
                if (bookCount >= 4) {
                    addBookBtn.style.display = 'none';
                } else {
                    addBookBtn.style.display = 'inline-block';
                }
            }

            // Validasi sebelum submit
            document.getElementById('loanForm').addEventListener('submit', function(e) {
                const selectedBooks = document.querySelectorAll('.book-select');
                const selectedCount = Array.from(selectedBooks).filter(select => select.value !== '').length;
                
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 buku');
                    return false;
                }

                // Cek duplikasi buku
                const bookIds = Array.from(selectedBooks).map(select => select.value).filter(id => id !== '');
                const uniqueIds = [...new Set(bookIds)];
                if (bookIds.length !== uniqueIds.length) {
                    e.preventDefault();
                    alert('Tidak boleh memilih buku yang sama lebih dari sekali');
                    return false;
                }
            });

            updateRemoveButtons();
            updateLoanStatus();
        });
    </script>
@endsection