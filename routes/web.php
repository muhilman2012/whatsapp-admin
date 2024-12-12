<?php

// admins controllers
use App\Http\Controllers\admin\indexAdmin;
use App\Http\Controllers\admin\laporanAdmin;
use App\Http\Controllers\admin\laporanAnalisController;
use App\Http\Controllers\admin\profileAdmin;
use App\Http\Controllers\admin\ExportController;
use App\Http\Controllers\admin\ImportController;

use App\Http\Controllers\auth\authAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Admin Routing
Route::get('/', [authAdmin::class, 'login'])->name('admin.login');
Route::post('/store', [authAdmin::class, 'loginPost'])->name('admin.login.store');

// Group routes untuk admin, deputi, asdep, dan analis
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {
    // Dashboard
    Route::get('/dashboard', [indexAdmin::class, 'index'])->name('admin.index');
    
    // Profile Routing
    Route::get('/dashboard/profile', [profileAdmin::class, 'index'])->name('admin.profile');

    // Laporan Routing untuk Admin, Deputi, dan Asdep
    Route::group(['middleware' => 'role.access:admin|deputi_1|deputi_2|deputi_3|deputi_4|asdep|analis'], function () {
        Route::get('/dashboard/laporan/create', [laporanAdmin::class, 'create'])->name('admin.laporan.create');
        Route::post('/dashboard/laporan/store', [laporanAdmin::class, 'store'])->name('admin.laporan.store');
        Route::get('/dashboard/laporan', [laporanAdmin::class, 'index'])->name('admin.laporan');
        Route::get('/dashboard/laporan/{nomor_tiket}', [laporanAdmin::class, 'show'])->name('admin.laporan.detail');
        Route::get('/dashboard/laporan/edit/{nomor_tiket}', [laporanAdmin::class, 'edit'])->name('admin.laporan.edit');
        Route::put('/dashboard/laporan/update/{nomor_tiket}', [laporanAdmin::class, 'update'])->name('admin.laporan.update');
        Route::post('/dashboard/laporan/upload/editor', [laporanAdmin::class, 'editor'])->name('admin.laporan.upload.editor');
        Route::put('/admin/laporan/update-nama/{nomor_tiket}', [laporanAdmin::class, 'updateNama'])->name('admin.laporan.updateNama');
        Route::get('/laporan/{nomor_tiket}/download', [laporanAdmin::class, 'downloadPDF'])->name('admin.laporan.download');

        // Assign to Analis
        Route::post('/dashboard/laporan/assign', [laporanAdmin::class, 'assignToAnalis'])->name('admin.laporan.assign');
    });

    // Export/Import untuk Admin, Deputi, dan Asdep
    Route::group(['middleware' => 'role.access:admin|deputi_1|deputi_2|deputi_3|deputi_4|asdep'], function () {
        Route::get('/laporan/export', [laporanAdmin::class, 'export'])->name('admin.laporan.export');
        Route::get('/admin/export/tanggal', [ExportController::class, 'exportByDate'])->name('admin.laporan.export.tanggal');
        Route::get('/admin/export-all', [ExportController::class, 'exportAll'])->name('admin.laporan.export.all');
        Route::post('/admin/laporan/import', [ImportController::class, 'import'])->name('admin.laporan.import');
    });

    // Logout
    Route::get('/logout', [indexAdmin::class, 'logout'])->name('admin.logout');
});

// Routes untuk Analis
Route::group(['prefix' => 'analis', 'middleware' => 'auth:admin'], function () {
    Route::group(['middleware' => 'role.access:analis'], function () {
        Route::get('/dashboard/laporan', [laporanAnalisController::class, 'index'])->name('analis.laporan.index');
        Route::get('/dashboard/laporan/{nomor_tiket}', [laporanAnalisController::class, 'show'])->name('analis.laporan.detail');
        Route::get('/dashboard/laporan/edit/{nomor_tiket}', [laporanAnalisController::class, 'edit'])->name('analis.laporan.edit');
        Route::put('/dashboard/laporan/update/{nomor_tiket}', [laporanAnalisController::class, 'update'])->name('analis.laporan.update');
    });
});