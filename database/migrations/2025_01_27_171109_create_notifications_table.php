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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigner_id')->constrained('admins', 'id_admins')->onDelete('cascade');
            $table->foreignId('assignee_id')->nullable()->constrained('admins', 'id_admins')->onDelete('cascade');
            $table->foreignId('laporan_id')->constrained('laporans', 'id')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->text('message')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
