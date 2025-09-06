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
        Schema::create('gaji', function (Blueprint $table) {
            $table->id('id_gaji'); 
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade'); 
            $table->integer('total_jam'); 
            $table->date('tanggal_penggajian'); 
            $table->enum('status_persetujuan', ['belum disetujui', 'disetujui'])->default('belum disetujui'); 
            $table->timestamps(); 
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};

