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
        Schema::create('izin_sakits', function (Blueprint $table) {
            $table->id();

            // Define the column for the foreign key first.
            $table->unsignedBigInteger('user_id');

            // Explicitly define the foreign key constraint
            $table->foreign('user_id')
                  ->references('id_user') // Reference the correct 'id_user' column
                  ->on('users')           // on the 'users' table
                  ->onDelete('cascade');   // and delete on cascade

            $table->string('detail_pekerjaan');
            $table->string('aktivitas'); // New column for the activity
            $table->enum('jenis_pembayaran', ['harian', 'bulanan', 'per_jam']);
            $table->date('tanggal_izin');
            $table->text('keterangan');
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('izin_sakits');
    }
};
