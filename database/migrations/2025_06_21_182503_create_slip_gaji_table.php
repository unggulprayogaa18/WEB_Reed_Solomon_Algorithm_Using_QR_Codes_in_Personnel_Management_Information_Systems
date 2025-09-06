<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('slip_gaji', function (Blueprint $table) {
            $table->id('id_slip_gaji');

            $table->foreignId('id_gaji')->constrained('gaji', 'id_gaji')->onDelete('cascade');

            $table->string('periode');

            $table->enum('tipe_pembayaran', ['bulanan', 'harian', 'per_jam'])->comment('Tipe skema gaji yang diterapkan');

            $table->decimal('gaji_berdasarkan_tipe', 15, 2)->comment('Jumlah gaji dari tipe pembayaran (pokok, total harian, atau total per jam)');

            $table->decimal('tunjangan', 15, 2)->default(0);

            $table->decimal('total_gaji', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('slip_gaji');
    }
};
