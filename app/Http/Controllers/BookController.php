<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function generateKodeBuku()
    {
        // Format: B001, B002, B003, dst
        // Cari nomor terbesar dari semua buku dengan format BXXX
        $allBooks = Book::where('code', 'like', 'B%')->get();
        $maxNumber = 0;
        
        foreach ($allBooks as $book) {
            if (preg_match('/^B(\d+)$/', $book->code, $matches)) {
                $maxNumber = max($maxNumber, (int) $matches[1]);
            }
        }
        
        $newNumber = $maxNumber + 1;
        return 'B' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $books = Book::all();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $previewCode = $this->generateKodeBuku();
        return view('books.create', compact('previewCode'));
    }

    public function getPreviewCode()
    {
        $previewCode = $this->generateKodeBuku();
        return response()->json(['code' => $previewCode]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'edition' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:20',
            'stock' => 'required|integer|min:2',
        ], [
            'stock.min' => 'Stok minimal adalah 2 buku.',
            'year.min' => 'Tahun terbit tidak valid.',
            'year.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
        ]);

        // Generate kode buku otomatis
        $code = $this->generateKodeBuku();
        
        // Pastikan kode unik (jika ada duplikat, generate lagi)
        while (Book::where('code', $code)->exists()) {
            $code = $this->generateKodeBuku();
        }

        Book::create([
            'code' => $code,
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'edition' => $request->edition,
            'isbn' => $request->isbn,
            'stock' => $request->stock,
        ]);

        return redirect()->route('books.index')->with('success', "Buku berhasil ditambahkan dengan kode: {$code}");
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'edition' => 'nullable|string|max:50',
            'isbn' => 'nullable|string|max:20',
        ], [
            'year.min' => 'Tahun terbit tidak valid.',
            'year.max' => 'Tahun terbit tidak boleh lebih dari tahun sekarang.',
        ]);

        // Update hanya field yang diizinkan (tidak termasuk code dan stock)
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'year' => $request->year,
            'edition' => $request->edition,
            'isbn' => $request->isbn,
        ]);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function updateStock(Request $request, Book $book)
    {
        $request->validate([
            'stock_change' => 'required|integer',
        ]);

        $stockChange = (int) $request->stock_change;
        $newStock = $book->stock + $stockChange;

        if ($newStock < 0) {
            return back()->withErrors(['stock_change' => 'Stok tidak boleh negatif.'])->withInput();
        }

        $book->update(['stock' => $newStock]);

        $message = $stockChange > 0 
            ? "Stok buku '{$book->title}' berhasil ditambah {$stockChange}. Stok sekarang: {$newStock}."
            : "Stok buku '{$book->title}' berhasil dikurangi " . abs($stockChange) . ". Stok sekarang: {$newStock}.";

        return redirect()->route('books.index')->with('success', $message);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
