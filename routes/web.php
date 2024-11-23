<?php

// admins controllers
use App\Http\Controllers\admin\indexAdmin;
use App\Http\Controllers\admin\laporanAdmin;
use App\Http\Controllers\admin\profileAdmin;
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
//Admin Routing
Route::get('/', [authAdmin::class, 'login'])->name('admin.login');
Route::post('/store', [authAdmin::class, 'loginPost'])->name('admin.login.store');
Route::group(['prefix' => 'admin',  'middleware' => 'auth:admin'], function () {
    Route::get('/dashboard', [indexAdmin::class, 'index'])->name('admin.index');
    // Profile Routeing
    Route::get('/dashboard/profile', [profileAdmin::class, 'index'])->name('admin.profile');

    // News Routing
    Route::get('/dashboard/laporan', [laporanAdmin::class, 'index'])->name('admin.laporan');
    Route::get('/dashboard/laporan/edit/{id}', [laporanAdmin::class, 'edit'])->name('admin.laporan.edit');
    Route::put('/dashboard/laporan/update/{id}', [laporanAdmin::class, 'update'])->name('admin.laporan.update');
    Route::post('/dashboard/laporan/upload/editor', [laporanAdmin::class, 'editor'])->name('admin.laporan.upload.editor');

    Route::get('/dashboard/laporan/export', [laporanAdmin::class, 'export'])->name('admin.laporan.export');

    Route::get('/logout', [indexAdmin::class, 'logout'])->name('admin.logout');
});