<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLoans = Loan::count();
        $totalBooks = Book::count();

        // Most borrowed books
        $popularBooks = Book::withCount('loans')
            ->orderBy('loans_count', 'desc')
            ->take(5)
            ->get();

        // Recent loans
        $recentLoans = Loan::with(['user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('totalLoans', 'totalBooks', 'popularBooks', 'recentLoans'));
    }
}
