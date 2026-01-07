<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class UpdateLoanFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:update-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update fines for overdue loans (runs daily)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating fines for overdue loans...');

        $overdueLoans = Loan::where('status', 'borrowed')
            ->whereDate('return_date', '<', now())
            ->get();

        $updatedCount = 0;
        $totalFine = 0;

        foreach ($overdueLoans as $loan) {
            $calculatedFine = $loan->calculateFine();
            
            if ($loan->fine != $calculatedFine) {
                $loan->fine = $calculatedFine;
                $loan->save();
                $updatedCount++;
                $totalFine += $calculatedFine;
            }
        }

        $this->info("Updated {$updatedCount} loans with fines.");
        $this->info("Total fine amount: Rp " . number_format($totalFine, 0, ',', '.'));

        return Command::SUCCESS;
    }
}
