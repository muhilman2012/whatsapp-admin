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
            // Menambahkan kolom 'catatan_analisis'
            $table->text('catatan_analisis')->nullable()->after('status_analisis'); // Kolom catatan_analisis (nullable)

            // Mengubah default value pada kolom 'status_analisis'
            $table->string('status_analisis')->default('Menunggu Telaahan')->change(); // Mengubah default menjadi 'Menunggu Telaahan'
        });
    }

    /**
     * Rollback migrasi untuk menghapus perubahan yang dilakukan.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Menghapus kolom 'catatan_analisis'
            $table->dropColumn('catatan_analisis');

            // Mengembalikan default status_analisis menjadi 'Pending'
            $table->string('status_analisis')->default('Pending')->change();
        });
    }
};
