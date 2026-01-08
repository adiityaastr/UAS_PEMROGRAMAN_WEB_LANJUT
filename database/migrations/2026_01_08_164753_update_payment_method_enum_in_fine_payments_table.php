<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, change enum to VARCHAR temporarily to allow data update
        \DB::statement("ALTER TABLE fine_payments MODIFY COLUMN payment_method VARCHAR(20) DEFAULT 'cash'");
        
        // Update existing data: convert 'transfer' and 'other' to 'qris'
        \DB::statement("UPDATE fine_payments SET payment_method = 'qris' WHERE payment_method IN ('transfer', 'other')");
        
        // Then change back to enum with only 'cash' and 'qris'
        \DB::statement("ALTER TABLE fine_payments MODIFY COLUMN payment_method ENUM('cash', 'qris') DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        \DB::statement("ALTER TABLE fine_payments MODIFY COLUMN payment_method ENUM('cash', 'transfer', 'other') DEFAULT 'cash'");
    }
};
