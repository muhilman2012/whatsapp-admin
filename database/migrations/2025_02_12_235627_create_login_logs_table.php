<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // Menyimpan user_id yang merujuk ke id_admins
            $table->string('ip_address');  // Menyimpan IP Address pengunjung
            $table->string('user_agent');  // Menyimpan informasi browser atau perangkat
            $table->timestamps();

            // Menghubungkan user_id dengan id_admins di tabel admins
            $table->foreign('user_id')->references('id_admins')->on('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_logs');
    }
};
