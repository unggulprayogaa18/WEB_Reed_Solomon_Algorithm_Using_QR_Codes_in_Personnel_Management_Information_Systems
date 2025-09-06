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
        Schema::create('profil', function (Blueprint $table) {
            $table->id('id_profile'); 
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade'); 
            $table->unique('id_user'); 
            $table->string('alamat')->nullable(); 
            $table->date('tanggal_lahir')->nullable(); 
            $table->enum('detail_pekerjaan', ['admin', 'pimpinan', 'staf', 'pengajar']); 
            $table->timestamps(); 
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil');
    }
};

