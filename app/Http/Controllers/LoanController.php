<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'book']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search berdasarkan nama peminjam, judul buku, atau kode unik
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('kode_unik', 'like', "%{$search}%");
                })->orWhereHas('book', function($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%")
                              ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $loans = $query->latest()->get();
        
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $books = Book::where('stock', '>', 0)->get();
        $users = User::where('role', '!=', 'admin')->get();
        return view('loans.create', compact('books', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_ids' => 'required|array|min:1|max:4',
            'book_ids.*' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:loan_date',
        ], [
            'book_ids.required' => 'Pilih minimal 1 buku',
            'book_ids.max' => 'Maksimal 4 buku per transaksi',
        ]);

        // Cek duplikasi buku
        $bookIds = array_unique($request->book_ids);
        if (count($bookIds) !== count($request->book_ids)) {
            return back()->withErrors(['book_ids' => 'Tidak boleh memilih buku yang sama lebih dari sekali.'])->withInput();
        }

        // Cek jumlah peminjaman aktif
        $activeLoans = Loan::where('user_id', $request->user_id)
            ->where('status', 'borrowed')
            ->count();

        $totalAfterLoan = $activeLoans + count($bookIds);
        if ($totalAfterLoan > 4) {
            $remaining = 4 - $activeLoans;
            if ($remaining <= 0) {
                return back()->withErrors(['user_id' => 'Pengguna telah mencapai batas maksimal peminjaman (4).'])->withInput();
            }
            return back()->withErrors(['book_ids' => "Pengguna hanya dapat meminjam maksimal {$remaining} buku lagi (Total aktif: {$activeLoans}/4)."])->withInput();
        }

        // Cek stok semua buku
        $books = Book::whereIn('id', $bookIds)->get();
        $errors = [];
        foreach ($books as $book) {
            if ($book->stock < 1) {
                $errors[] = "Stok buku '{$book->title}' habis.";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors(['book_ids' => implode(' ', $errors)])->withInput();
        }

        // Create Loans untuk setiap buku
        $createdLoans = [];
        foreach ($bookIds as $bookId) {
            $loan = Loan::create([
                'user_id' => $request->user_id,
                'book_id' => $bookId,
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'status' => 'borrowed',
            ]);

            // Decrease Stock
            Book::find($bookId)->decrement('stock');
            $createdLoans[] = $loan;
        }

        $bookCount = count($createdLoans);
        $message = $bookCount > 1 
            ? "Peminjaman {$bookCount} buku berhasil dibuat." 
            : "Peminjaman berhasil dibuat.";

        return redirect()->route('loans.index')->with('success', $message);
    }

    public function returnBook(Loan $loan)
    {
        if ($loan->status == 'returned') {
            return back()->with('error', 'Buku sudah dikembalikan.');
        }

        $loan->actual_return_date = now();
        $loan->status = 'returned';

        // Calculate Fine
        // Denda perhari 2.000
        $dueDate = Carbon::parse($loan->return_date);
        $returnDate = Carbon::parse($loan->actual_return_date);

        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $loan->fine = $daysLate * 2000;
        }

        $loan->save();

        // Increase Stock
        $loan->book->increment('stock');

        return redirect()->route('loans.index')->with('success', 'Buku berhasil dikembalikan. Denda: Rp ' . number_format($loan->fine, 0, ',', '.'));
    }

    public function report()
    {
        // Daily loan report
        $dailyLoans = Loan::selectRaw('DATE(loan_date) as date, count(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('loans.report', compact('dailyLoans'));
    }

    public function getUserLoans($userId)
    {
        $activeLoans = Loan::where('user_id', $userId)
            ->where('status', 'borrowed')
            ->count();

        return response()->json([
            'active_loans' => $activeLoans,
            'remaining' => max(0, 4 - $activeLoans)
        ]);
    }
}
