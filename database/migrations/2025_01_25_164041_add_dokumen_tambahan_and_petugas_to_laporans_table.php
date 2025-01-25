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
        Schema::table('laporans', function (Blueprint $table) {
            $table->string('dokumen_tambahan')->nullable()->after('dokumen_pendukung');
            $table->string('petugas')->nullable()->after('complaint_id');
        });
    }
  
    /**  
     * Reverse the migrations.  
     *  
     * @return void  
     */  
    public function down()
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropColumn('dokumen_tambahan');
            $table->dropColumn('petugas');
        });
    }
};
