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
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('email');
            $table->string('tempat_lahir')->nullable()->after('foto');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('kode_unik')->unique()->nullable()->after('tanggal_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['foto', 'tempat_lahir', 'tanggal_lahir', 'kode_unik']);
        });
    }
};
