<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['user', 'book'])->latest()->get();
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
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:loan_date',
        ]);

        // Bonus: Max loan limit > 4 blocked
        $activeLoans = Loan::where('user_id', $request->user_id)
            ->where('status', 'borrowed')
            ->count();

        if ($activeLoans >= 4) {
            return back()->withErrors(['user_id' => 'Pengguna telah mencapai batas maksimal peminjaman (4).']);
        }

        $book = Book::find($request->book_id);
        if ($book->stock < 1) {
            return back()->withErrors(['book_id' => 'Stok buku habis.']);
        }

        // Create Loan
        Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
            'loan_date' => $request->loan_date,
            'return_date' => $request->return_date,
            'status' => 'borrowed',
        ]);

        // Decrease Stock
        $book->decrement('stock');

        return redirect()->route('loans.index')->with('success', 'Peminjaman berhasil dibuat.');
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
}
