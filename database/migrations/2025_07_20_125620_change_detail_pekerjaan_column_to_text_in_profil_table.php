<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profil', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi TEXT untuk menampung data lebih panjang
            $table->text('detail_pekerjaan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profil', function (Blueprint $table) {
            // Kode untuk mengembalikan perubahan jika diperlukan
            $table->string('detail_pekerjaan', 255)->nullable()->change();
        });
    }
};