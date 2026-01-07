<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLoans = Loan::count();
        $totalBooks = Book::count();
        
        // Update fines for overdue loans
        Loan::where('status', 'borrowed')
            ->whereDate('return_date', '<', now())
            ->get()
            ->each(function ($loan) {
                $calculatedFine = $loan->calculateFine();
                if ($loan->fine != $calculatedFine) {
                    $loan->fine = $calculatedFine;
                    $loan->save();
                }
            });
        
        // Calculate total pending fines
        $totalPendingFines = Loan::where('status', 'borrowed')
            ->whereDate('return_date', '<', now())
            ->get()
            ->sum(function ($loan) {
                return $loan->calculateFine();
            });
        
        $overdueCount = Loan::where('status', 'borrowed')
            ->whereDate('return_date', '<', now())
            ->count();

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

        // Chart 1: Peminjaman per bulan (6 bulan terakhir)
        $monthlyLoans = Loan::selectRaw('DATE_FORMAT(loan_date, "%Y-%m") as month, COUNT(*) as total')
            ->where('loan_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        $monthlyLabels = [];
        $monthlyData = [];
        foreach ($monthlyLoans as $loan) {
            $date = Carbon::createFromFormat('Y-m', $loan->month);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = $loan->total;
        }

        // Chart 2: Status peminjaman
        $statusLoans = Loan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();
        
        $statusLabels = [];
        $statusData = [];
        $statusMap = [
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan'
        ];
        foreach ($statusLoans as $status) {
            $statusLabels[] = $statusMap[$status->status] ?? ucfirst($status->status);
            $statusData[] = $status->total;
        }

        // Chart 3: Buku terpopuler (Top 5)
        $topBooks = Book::withCount('loans')
            ->orderBy('loans_count', 'desc')
            ->take(5)
            ->get();
        
        $bookLabels = $topBooks->pluck('title')->map(function($title) {
            return strlen($title) > 20 ? substr($title, 0, 20) . '...' : $title;
        })->toArray();
        $bookData = $topBooks->pluck('loans_count')->toArray();

        // Chart 4: Anggota per program studi (Top 8)
        $membersByProdi = User::where('role', 'member')
            ->whereNotNull('program_studi')
            ->selectRaw('program_studi, COUNT(*) as total')
            ->groupBy('program_studi')
            ->orderBy('total', 'desc')
            ->take(8)
            ->get();
        
        $prodiLabels = $membersByProdi->pluck('program_studi')->map(function($prodi) {
            return strlen($prodi) > 15 ? substr($prodi, 0, 15) . '...' : $prodi;
        })->toArray();
        $prodiData = $membersByProdi->pluck('total')->toArray();

        // Chart 5: Peminjaman per hari dalam seminggu
        $weeklyLoans = Loan::selectRaw('DAYNAME(loan_date) as day_name, DAYOFWEEK(loan_date) as day_num, COUNT(*) as total')
            ->where('loan_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('day_name', 'day_num')
            ->orderBy('day_num')
            ->get();
        
        $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $weeklyData = array_fill(0, 7, 0);
        
        foreach ($weeklyLoans as $loan) {
            $dayIndex = array_search($loan->day_name, $daysOrder);
            if ($dayIndex !== false) {
                $weeklyData[$dayIndex] = $loan->total;
            }
        }

        // Chart 6: Buku dengan stok terendah (Top 6)
        $lowStockBooks = Book::orderBy('stock', 'asc')
            ->take(6)
            ->get();
        
        $lowStockLabels = $lowStockBooks->pluck('title')->map(function($title) {
            return strlen($title) > 18 ? substr($title, 0, 18) . '...' : $title;
        })->toArray();
        $lowStockData = $lowStockBooks->pluck('stock')->toArray();

        return view('dashboard', compact(
            'totalLoans', 
            'totalBooks', 
            'popularBooks', 
            'recentLoans',
            'monthlyLabels',
            'monthlyData',
            'statusLabels',
            'statusData',
            'bookLabels',
            'bookData',
            'prodiLabels',
            'prodiData',
            'dayLabels',
            'weeklyData',
            'lowStockLabels',
            'lowStockData',
            'totalPendingFines',
            'overdueCount'
        ));
    }
}
