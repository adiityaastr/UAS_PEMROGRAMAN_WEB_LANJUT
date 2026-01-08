<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'return_date',
        'actual_return_date',
        'status',
        'fine',
        'renewal_count',
        'renewed_at',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'renewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function finePayments()
    {
        return $this->hasMany(FinePayment::class);
    }

    /**
     * Calculate fine for overdue loans
     * Denda per hari: Rp 2.000
     */
    public function calculateFine()
    {
        if ($this->status === 'returned') {
            // Jika sudah dikembalikan, gunakan fine yang sudah ada
            return $this->fine ?? 0;
        }

        $dueDate = Carbon::parse($this->return_date)->startOfDay();
        $now = Carbon::now()->startOfDay();

        if ($now->gt($dueDate)) {
            // Hitung selisih hari: jika now > dueDate, maka hasilnya positif
            $daysLate = $dueDate->diffInDays($now, false);
            // Pastikan nilai positif
            if ($daysLate > 0) {
                return $daysLate * 2000;
            }
        }

        return 0;
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue()
    {
        if ($this->status === 'returned') {
            return false;
        }

        $dueDate = Carbon::parse($this->return_date)->startOfDay();
        $now = Carbon::now()->startOfDay();
        return $now->gt($dueDate);
    }

    /**
     * Get days late (returns integer, no decimals)
     */
    public function getDaysLate()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $dueDate = Carbon::parse($this->return_date)->startOfDay();
        $now = Carbon::now()->startOfDay();
        
        // Hitung selisih hari: jika now > dueDate, maka hasilnya positif
        // Menggunakan diffInDays dengan absolute = true untuk mendapatkan nilai positif
        $daysLate = $dueDate->diffInDays($now, false);
        
        // Pastikan nilai positif dan bulat
        return max(0, (int) $daysLate);
    }

    /**
     * Check if loan can be renewed
     * Maksimal 1 kali perpanjangan per peminjaman
     */
    public function canBeRenewed()
    {
        // Tidak bisa di-renew jika sudah dikembalikan
        if ($this->status === 'returned') {
            return false;
        }

        // Tidak bisa di-renew jika sudah pernah di-renew 1 kali
        if ($this->renewal_count >= 1) {
            return false;
        }

        // Tidak bisa di-renew jika sudah terlambat lebih dari 7 hari
        if ($this->isOverdue() && $this->getDaysLate() > 7) {
            return false;
        }

        return true;
    }

    /**
     * Get total paid fines
     */
    public function getTotalPaidFines()
    {
        return $this->finePayments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get remaining fine to pay
     */
    public function getRemainingFine()
    {
        $totalFine = $this->fine ?? $this->calculateFine();
        $paidFine = $this->getTotalPaidFines();
        return max(0, $totalFine - $paidFine);
    }
}
