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
            $table->string('program_studi')->nullable()->after('kode_unik');
            $table->integer('semester')->nullable()->after('program_studi');
            $table->integer('tahun_masuk')->nullable()->after('semester');
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif')->after('tahun_masuk');
            $table->string('nomor_telepon')->nullable()->after('status');
            $table->text('alamat')->nullable()->after('nomor_telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['program_studi', 'semester', 'tahun_masuk', 'status', 'nomor_telepon', 'alamat']);
        });
    }
};
