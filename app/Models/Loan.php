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
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
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
            $daysLate = $now->diffInDays($dueDate, false);
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
        $daysLate = $now->diffInDays($dueDate, false);
        
        // Pastikan nilai positif dan bulat
        return max(0, (int) $daysLate);
    }
}
