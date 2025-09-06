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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi'); 
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade'); 
            $table->text('isi_notifikasi'); 
            $table->timestamps(); 
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

