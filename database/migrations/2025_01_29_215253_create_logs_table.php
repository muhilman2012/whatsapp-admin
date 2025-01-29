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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_id'); // ID laporan yang terkait
            $table->string('activity'); // Deskripsi aktivitas
            $table->unsignedBigInteger('user_id'); // ID pengguna yang melakukan aktivitas
            $table->timestamps();
            
            // Foreign key untuk laporan
            $table->foreign('laporan_id')->references('id')->on('laporans')->onDelete('cascade');
            // Foreign key untuk pengguna
            $table->foreign('user_id')->references('id_admins')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
