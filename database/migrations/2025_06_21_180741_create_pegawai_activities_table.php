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
        Schema::create('pegawai_activities', function (Blueprint $table) {
            $table->id('id_activity'); 
            $table->string('nama_aktivitas');
            $table->text('deskripsi');
            $table->dateTime('jam_mulai');
            $table->dateTime('jam_selesai');
            $table->uuid('uuid');
            $table->string('qrcode_path')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Balikkan (rollback) migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_activities');
    }
};

