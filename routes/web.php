<?php

// admins controllers
use App\Http\Controllers\admin\indexAdmin;
use App\Http\Controllers\admin\laporanAdmin;
use App\Http\Controllers\admin\laporanAnalisController;
use App\Http\Controllers\admin\profileAdmin;
use App\Http\Controllers\admin\ExportController;
use App\Http\Controllers\admin\ImportController;
use App\Http\Controllers\admin\UserManagementController;
use App\Http\Controllers\admin\NotificationController;

// auth controllers
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
Route::get('/check-session', function () {
    if (!auth('admin')->check()) {
        return response()->json(['session_active' => false], 401); // Sesi tidak aktif
    }
    return response()->json(['session_active' => true]); // Sesi aktif
})->name('admin.check-session');

// Group routes untuk admin, deputi, asdep, dan analis
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {
    // Dashboard
    Route::get('/dashboard', [indexAdmin::class, 'index'])->name('admin.index');
    
    // Profile Routing
    Route::get('/dashboard/profile', [profileAdmin::class, 'index'])->name('admin.profile');

    // Laporan Routing untuk Admin, Deputi, dan Asdep
    Route::group(['middleware' => 'role.access:superadmin|admin|deputi_1|deputi_2|deputi_3|deputi_4|asdep|analis'], function () {
        Route::get('/dashboard/laporan/create', [laporanAdmin::class, 'create'])->name('admin.laporan.create');
        Route::post('/dashboard/laporan/store', [laporanAdmin::class, 'store'])->name('admin.laporan.store');
        Route::get('/dashboard/laporan', [laporanAdmin::class, 'index'])->name('admin.laporan');
        Route::get('/dashboard/laporan/{nomor_tiket}', [laporanAdmin::class, 'show'])->name('admin.laporan.detail');
        Route::get('/dashboard/pengaduan/{nomor_tiket}', [laporanAdmin::class, 'detail'])->name('admin.laporan.detail2');
        Route::put('/dashboard/pengaduan/ubah/{nomor_tiket}', [laporanAdmin::class, 'ubah'])->name('admin.laporan.ubah');
        Route::get('/dashboard/laporan/edit/{nomor_tiket}', [laporanAdmin::class, 'edit'])->name('admin.laporan.edit');
        Route::put('/dashboard/laporan/update/{nomor_tiket}', [laporanAdmin::class, 'update'])->name('admin.laporan.update');
        Route::post('/dashboard/laporan/{nomor_tiket}/teruskan-ke-instansi', [laporanAdmin::class, 'teruskanKeInstansi'])->name('admin.laporan.teruskanKeInstansi');
        Route::post('/dashboard/laporan/update/{nomor_tiket}/analis', [laporanAdmin::class, 'storeAnalis'])->name('admin.laporan.analis.store');
        Route::post('/dashboard/laporan/upload/editor', [laporanAdmin::class, 'editor'])->name('admin.laporan.upload.editor');
        Route::put('/dashboard/laporan/update-nama/{nomor_tiket}', [laporanAdmin::class, 'updateNama'])->name('admin.laporan.updateNama');
        Route::get('/dashboard/laporan/{nomor_tiket}/download', [laporanAdmin::class, 'downloadPDF'])->name('admin.laporan.download');
        Route::get('/dashboard/laporan/{nomor_tiket}/downloadtandaterima', [laporanAdmin::class, 'tandaterimaPDF'])->name('admin.laporan.tandaterima');
        Route::put('/laporan/{nomorTiket}/approval', [laporanAdmin::class, 'approval'])->name('admin.laporan.approval');

        // Assign to Analis
        Route::post('/dashboard/laporan/assign', [laporanAdmin::class, 'assignToAnalis'])->name('admin.laporan.assign');
        // Rute untuk mengambil notifikasi berdasarkan analis
        Route::get('/dashboard/notifications/analyst', [NotificationController::class, 'indexForAnalyst']);
        // Rute untuk mengambil notifikasi berdasarkan role (misalnya pelimpahan)
        Route::get('/dashboard/notifications/role-based', [NotificationController::class, 'roleBasedNotifications']);
        // Rute untuk menandai notifikasi sebagai sudah dibaca
        Route::post('/dashboard/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    });

    // Export/Import untuk Admin, Deputi, dan Asdep
    Route::group(['middleware' => 'role.access:superadmin|admin|deputi_1|deputi_2|deputi_3|deputi_4|asdep|analis'], function () {
        Route::post('/admin/laporan/import', [ImportController::class, 'import'])->name('admin.laporan.import');
        Route::get('/laporan/export', [laporanAdmin::class, 'export'])->name('admin.laporan.export');
        Route::get('admin/laporan/export/tanggal', [ExportController::class, 'exportByDate'])->name('admin.laporan.export.tanggal');
        Route::get('admin/laporan/export/all', [ExportController::class, 'exportAll'])->name('admin.laporan.export.all');
        Route::get('admin/laporan/export/pdf', [ExportController::class, 'exportPdf'])->name('admin.laporan.export.pdf');
        Route::get('admin/laporan/export/tanggal/pdf', [ExportController::class, 'exportByDatePdf'])->name('admin.laporan.export.tanggal.pdf');
        Route::post('admin/laporan/checkExportStatus', [ExportController::class, 'checkExportStatus'])->name('admin.laporan.checkExportStatus');
        Route::get('admin/laporan/export/filtered/excel', [ExportController::class, 'exportFilteredData'])->name('admin.laporan.export.filtered.excel');
        Route::get('admin/laporan/export/filtered/pdf', [ExportController::class, 'exportFilteredPdf'])->name('admin.laporan.export.filtered.pdf');
        Route::get('admin/laporan/export/pelimpahan', [ExportController::class, 'exportPelimpahan'])->name('admin.laporan.export.pelimpahan');
    });

    Route::get('/dashboard/user-management', [UserManagementController::class, 'index'])->name('admin.user_management.index');
    Route::get('/dashboard/user-management/create', [UserManagementController::class, 'create'])->name('admin.user_management.create');
    Route::post('/dashboard/user-management/store', [UserManagementController::class, 'store'])->name('admin.user_management.store');
    Route::get('/dashboard/user-management/edit/{id_admins}', [UserManagementController::class, 'edit'])->name('admin.user_management.edit');
    Route::put('/dashboard/user-management/update/{id_admins}', [UserManagementController::class, 'update'])->name('admin.user_management.update');
    Route::delete('/dashboard/user-management/{id_admins}', [UserManagementController::class, 'destroy'])->name('admin.user_management.destroy');
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