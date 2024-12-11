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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_id');
            $table->unsignedBigInteger('analis_id');
            $table->text('notes');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamps();
        
            $table->foreign('laporan_id')->references('id')->on('laporans')->onDelete('cascade');
            $table->foreign('analis_id')->references('id_admins')->on('admins')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id_admins')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};
