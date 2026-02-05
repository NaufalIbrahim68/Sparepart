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
        Schema::dropIfExists('mo_sparepart');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika ingin rollback, tabel akan dibuat kembali
        // Namun karena struktur tabel tidak diketahui, ini hanya placeholder
        // Jika tabel memang sudah tidak digunakan, rollback tidak perlu implementasi lengkap
        Schema::create('mo_sparepart', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Tambahkan kolom-kolom lain jika struktur tabel diketahui
            // Untuk saat ini dibiarkan kosong karena tabel tidak digunakan
        });
    }
};
