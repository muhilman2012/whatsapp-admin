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
            $table->text('lembar_kerja_analis')->nullable()->after('tanggapan');
            $table->string('status_analisis')->default('Pending')->after('lembar_kerja_analis'); // Status analisis: Pending, Approved, Rejected
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
            $table->dropColumn('lembar_kerja_analis');
            $table->dropColumn('status_analisis');
        });
    }
};
