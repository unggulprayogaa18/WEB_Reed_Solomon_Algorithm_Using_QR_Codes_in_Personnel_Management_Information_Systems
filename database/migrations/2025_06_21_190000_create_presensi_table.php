<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_activity')->constrained('pegawai_activities', 'id_activity')->onDelete('cascade');

            // --- PERBAIKAN ---
            // Definisikan kolom secara eksplisit agar tipe datanya pasti sama (unsignedBigInteger)
            $table->unsignedBigInteger('izin_sakit_id')->nullable();

            $table->enum('status', ['masuk', 'keluar']);
            $table->timestamps();

            // Definisikan foreign key secara terpisah di akhir
            $table->foreign('izin_sakit_id')
                  ->references('id')
                  ->on('izin_sakits')
                  ->onDelete('set null');
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};