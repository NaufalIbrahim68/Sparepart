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
        // SQL Server tidak mendukung index pada VARCHAR dengan panjang besar
        // Query autocomplete akan tetap cukup cepat tanpa index karena:
        // 1. Tabel mntrng_sparepart tidak terlalu besar
        // 2. SQL Server sudah memiliki query optimization
        // 3. DISTINCT clause sudah cukup efisien

        // Jika performa menjadi masalah di masa depan, pertimbangkan:
        // - Membuat computed column dengan substring nama_barang
        // - Atau menggunakan full-text search indexing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback
    }
};
