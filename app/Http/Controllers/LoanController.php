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
        
        // Update fine for overdue loans
        foreach ($loans as $loan) {
            if ($loan->status === 'borrowed' && $loan->isOverdue()) {
                $calculatedFine = $loan->calculateFine();
                // Update fine in database if different
                if ($loan->fine != $calculatedFine) {
                    $loan->fine = $calculatedFine;
                    $loan->save();
                }
            }
        }
        
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

        // Calculate Fine - use the fine that's already calculated or calculate if not set
        if ($loan->fine == 0) {
            $dueDate = Carbon::parse($loan->return_date)->startOfDay();
            $returnDate = Carbon::parse($loan->actual_return_date)->startOfDay();

            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate, false);
                if ($daysLate > 0) {
                    $loan->fine = $daysLate * 2000;
                }
            }
        }

        $loan->save();

        // Increase Stock
        $loan->book->increment('stock');

        return redirect()->route('loans.index')->with('success', 'Buku berhasil dikembalikan. Denda: Rp ' . number_format($loan->fine, 0, ',', '.'));
    }

    public function report(Request $request)
    {
        // Get filter parameters
        $month = $request->get('month', date('Y-m'));
        $year = $request->get('year', date('Y'));
        
        // Parse month and year
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        // Get all loans in the period
        $loans = Loan::with(['user', 'book'])
            ->whereBetween('loan_date', [$startDate, $endDate])
            ->orderBy('loan_date', 'desc')
            ->get();
        
        // Update fines for overdue loans
        foreach ($loans as $loan) {
            if ($loan->status === 'borrowed' && $loan->isOverdue()) {
                $calculatedFine = $loan->calculateFine();
                if ($loan->fine != $calculatedFine) {
                    $loan->fine = $calculatedFine;
                    $loan->save();
                }
            }
        }
        
        // Statistics
        $totalLoans = $loans->count();
        $borrowedCount = $loans->where('status', 'borrowed')->count();
        $returnedCount = $loans->where('status', 'returned')->count();
        $overdueCount = $loans->filter(function($loan) {
            return $loan->status === 'borrowed' && $loan->isOverdue();
        })->count();
        
        // Calculate total fines
        $totalFines = $loans->sum(function($loan) {
            return $loan->calculateFine();
        });
        
        $paidFines = $loans->where('status', 'returned')->sum('fine');
        $pendingFines = $totalFines - $paidFines;
        
        // Daily statistics
        $dailyStats = $loans->groupBy(function($loan) {
            return Carbon::parse($loan->loan_date)->format('Y-m-d');
        })->map(function($dayLoans) {
            return [
                'date' => Carbon::parse($dayLoans->first()->loan_date)->format('Y-m-d'),
                'total' => $dayLoans->count(),
                'borrowed' => $dayLoans->where('status', 'borrowed')->count(),
                'returned' => $dayLoans->where('status', 'returned')->count(),
            ];
        })->sortByDesc('date')->values();
        
        // Most borrowed books
        $popularBooks = $loans->groupBy('book_id')->map(function($bookLoans) {
            return [
                'book' => $bookLoans->first()->book,
                'count' => $bookLoans->count(),
            ];
        })->sortByDesc('count')->take(5)->values();
        
        // Get available months/years for filter
        $availableMonths = Loan::selectRaw('DATE_FORMAT(loan_date, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');
        
        return view('loans.report', compact(
            'loans',
            'totalLoans',
            'borrowedCount',
            'returnedCount',
            'overdueCount',
            'totalFines',
            'paidFines',
            'pendingFines',
            'dailyStats',
            'popularBooks',
            'month',
            'year',
            'startDate',
            'endDate',
            'availableMonths'
        ));
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

    public function renew(Loan $loan, Request $request)
    {
        // Validasi apakah bisa di-renew
        if (!$loan->canBeRenewed()) {
            if ($loan->status === 'returned') {
                return back()->with('error', 'Buku sudah dikembalikan, tidak dapat diperpanjang.');
            }
            if ($loan->renewal_count >= 1) {
                return back()->with('error', 'Peminjaman ini sudah pernah diperpanjang. Maksimal 1 kali perpanjangan per peminjaman.');
            }
            if ($loan->isOverdue() && $loan->getDaysLate() > 7) {
                return back()->with('error', 'Peminjaman yang terlambat lebih dari 7 hari tidak dapat diperpanjang.');
            }
            return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang.');
        }

        // Validasi durasi perpanjangan
        $request->validate([
            'duration' => 'required|integer|min:1|max:7',
        ], [
            'duration.required' => 'Durasi perpanjangan harus diisi.',
            'duration.integer' => 'Durasi perpanjangan harus berupa angka.',
            'duration.min' => 'Durasi perpanjangan minimal 1 hari.',
            'duration.max' => 'Durasi perpanjangan maksimal 7 hari.',
        ]);

        $duration = (int) $request->duration;

        // Perpanjang sesuai durasi yang dipilih dari tanggal kembali saat ini
        $newReturnDate = Carbon::parse($loan->return_date)->addDays($duration);
        
        $loan->return_date = $newReturnDate;
        $loan->renewal_count = $loan->renewal_count + 1;
        $loan->renewed_at = now();
        $loan->save();

        return redirect()->route('loans.index')->with('success', 'Peminjaman berhasil diperpanjang ' . $duration . ' hari hingga ' . $newReturnDate->translatedFormat('d F Y') . '.');
    }
}
