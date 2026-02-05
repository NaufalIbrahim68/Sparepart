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
        Schema::dropIfExists('nama_kode_barang');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: create basic structure
        Schema::create('nama_kode_barang', function (Blueprint $table) {
            $table->id('id_nkb');
            $table->string('nama_barang', 255);
            $table->string('kode_barang', 255);
        });
    }
};
