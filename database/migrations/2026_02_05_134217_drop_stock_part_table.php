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
        Schema::dropIfExists('stock_part');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: create table structure based on original migration
        Schema::create('stock_part', function (Blueprint $table) {
            $table->id('id_stp');
            $table->string('nama_barang');
            $table->string('kode_barang');
            $table->string('address');
            $table->string('leadtime');
            $table->string('lifetime');
            $table->string('stock_wrhs');
            $table->timestamps();
        });
    }
};
