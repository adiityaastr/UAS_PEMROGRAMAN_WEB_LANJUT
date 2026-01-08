<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\FinePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinePaymentController extends Controller
{
    public function index(Request $request)
    {
        // Hanya tampilkan pembayaran dengan status 'paid' (Lunas)
        $query = FinePayment::with(['loan.user', 'loan.book', 'paidBy'])
            ->where('status', 'paid');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('loan.user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('kode_unik', 'like', "%{$search}%");
                })->orWhereHas('loan.book', function($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $payments = $query->latest()->paginate(20);

        // Get loans with unpaid fines for creating new payments
        $loansWithFinesQuery = Loan::with(['user', 'book', 'finePayments'])
            ->where(function($q) {
                // Loans with fines (overdue or returned with fine)
                $q->where(function($subQ) {
                    $subQ->where('status', 'borrowed')
                         ->whereDate('return_date', '<', now());
                })->orWhere(function($subQ) {
                    $subQ->where('status', 'returned')
                         ->where('fine', '>', 0);
                });
            });

        // Search for loans with fines
        if ($request->filled('search_loan')) {
            $searchLoan = $request->search_loan;
            $loansWithFinesQuery->where(function($q) use ($searchLoan) {
                $q->whereHas('user', function($userQuery) use ($searchLoan) {
                    $userQuery->where('name', 'like', "%{$searchLoan}%")
                              ->orWhere('kode_unik', 'like', "%{$searchLoan}%");
                })->orWhereHas('book', function($bookQuery) use ($searchLoan) {
                    $bookQuery->where('title', 'like', "%{$searchLoan}%");
                });
            });
        }

        $loansWithFines = $loansWithFinesQuery->latest()->get();
        
        // Update fines for overdue loans
        foreach ($loansWithFines as $loan) {
            if ($loan->status === 'borrowed' && $loan->isOverdue()) {
                $calculatedFine = $loan->calculateFine();
                if ($loan->fine != $calculatedFine) {
                    $loan->fine = $calculatedFine;
                    $loan->save();
                }
            }
        }
        
        // Filter loans that still have remaining fine
        $loansWithFines = $loansWithFines->filter(function($loan) {
            return $loan->getRemainingFine() > 0;
        })->values();

        return view('fine-payments.index', compact('payments', 'loansWithFines'));
    }

    public function create(Loan $loan)
    {
        // Validasi loan
        if ($loan->status === 'returned' && $loan->fine == 0) {
            return back()->with('error', 'Peminjaman ini tidak memiliki denda.');
        }

        $remainingFine = $loan->getRemainingFine();
        if ($remainingFine <= 0) {
            return back()->with('error', 'Denda untuk peminjaman ini sudah lunas.');
        }

        return view('fine-payments.create', compact('loan', 'remainingFine'));
    }

    public function store(Request $request, Loan $loan)
    {
        $remainingFine = $loan->getRemainingFine();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $remainingFine,
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,qris',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.max' => 'Jumlah pembayaran tidak boleh melebihi sisa denda (Rp ' . number_format($remainingFine, 0, ',', '.') . ')',
            'payment_method.in' => 'Metode pembayaran harus Tunai atau QRIS.',
        ]);

        // Cek apakah pembayaran akan melebihi sisa denda
        $totalAfterPayment = $loan->getTotalPaidFines() + $request->amount;
        $totalFine = $loan->fine ?? $loan->calculateFine();

        if ($totalAfterPayment > $totalFine) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi total denda.'])->withInput();
        }

        FinePayment::create([
            'loan_id' => $loan->id,
            'paid_by' => auth()->id(),
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'notes' => $request->notes,
        ]);

        return redirect()->route('fine-payments.index')->with('success', 'Pembayaran denda berhasil dicatat.');
    }
}
