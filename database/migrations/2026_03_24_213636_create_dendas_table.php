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
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->onDelete('cascade');
            $table->foreignId('pengembalian_id')->nullable()->constrained('pengembalians')->onDelete('set null');
            $table->enum('jenis_denda', ['Telat', 'Rusak Berat', 'Hilang']);
            $table->integer('jumlah_hari')->default(0);        // untuk denda telat
            $table->bigInteger('tarif_per_hari')->default(5000); // BIGINT untuk uang
            $table->bigInteger('total_denda')->default(0);       // BIGINT untuk uang
            $table->enum('status_bayar', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->date('tanggal_lunas')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
