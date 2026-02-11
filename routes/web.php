<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HalaqahController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MusyrifController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\CeklistKegiatanController;
use App\Http\Controllers\LaporanKegiatanController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\KesehatanController;
use App\Http\Controllers\DashboardController;

// ======================
// 🔹 ROUTE DASHBOARD
// ======================
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ======================
// 🔹 GROUP ROUTES UNTUK SEMUA YANG LOGIN
// ======================
Route::middleware(['auth'])->group(function () {
    
    // ======================
    // 🔹 DASHBOARD UTAMA
    // ======================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // ======================
    // 🔹 DASHBOARD API
    // ======================
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'mobileStats'])
            ->name('dashboard.stats');
        Route::get('/dashboard/activities', [DashboardController::class, 'mobileActivities'])
            ->name('dashboard.activities');
        Route::get('/dashboard/statistik', [DashboardController::class, 'statistik'])
            ->name('dashboard.statistik');
        Route::get('/dashboard/aktivitas', [DashboardController::class, 'aktivitas'])
            ->name('dashboard.aktivitas');
        Route::get('/dashboard/notifikasi', [DashboardController::class, 'notifikasi'])
            ->name('dashboard.notifikasi');
    });
    
    // ======================
    // 🔹 CRUD HALAQAH (Admin & Musyrif)
    // ======================
    Route::prefix('halaqah')->group(function () {
        Route::get('/', [HalaqahController::class, 'index'])
            ->name('halaqah.index');
        Route::get('/data', [HalaqahController::class, 'data'])
            ->name('halaqah.data');
        Route::post('/check-name', [HalaqahController::class, 'checkName'])
            ->name('halaqah.check-name');
        Route::post('/', [HalaqahController::class, 'store'])
            ->name('halaqah.store');
        Route::get('/{id}', [HalaqahController::class, 'show'])
            ->name('halaqah.show');
        Route::put('/{id}', [HalaqahController::class, 'update'])
            ->name('halaqah.update');
        Route::delete('/{id}', [HalaqahController::class, 'destroy'])
            ->name('halaqah.destroy');
        Route::post('/restore/{id}', [HalaqahController::class, 'restore'])
            ->name('halaqah.restore');
        Route::delete('/force/{id}', [HalaqahController::class, 'forceDelete'])
            ->name('halaqah.forceDelete');
    });
    
    // ======================
    // 🔹 CEKLIST KEGIATAN SANTRI
    // ======================
    Route::prefix('ceklist')->group(function () {
        Route::get('/', [CeklistKegiatanController::class, 'index'])
            ->name('ceklist.index');
        Route::get('/data', [CeklistKegiatanController::class, 'data'])
            ->name('ceklist.data');
        Route::get('/sub-kegiatan', [CeklistKegiatanController::class, 'subKegiatan']);
        Route::get('/data-nested', [CeklistKegiatanController::class, 'getDataNested'])
            ->name('ceklist.dataNested');
        Route::post('/update-status', [CeklistKegiatanController::class, 'updateStatus'])
            ->name('ceklist.updateStatus');
        Route::post('/store', [CeklistKegiatanController::class, 'store'])
            ->name('ceklist.store');
        Route::get('/mobile-data', [CeklistKegiatanController::class, 'dataMobile'])
            ->name('ceklist.dataMobile');
        Route::get('/mobile', [CeklistKegiatanController::class, 'mobile'])
            ->name('ceklist.mobile');
    });
    
    // ======================
    // 🔹 ABSEN
    // ======================
    Route::prefix('absen')->group(function () {
        Route::get('/', [AbsenController::class, 'index'])
            ->name('absen.index');
        Route::get('/mobile', [AbsenController::class, 'index'])
            ->name('absen.mobile');
        Route::get('/data-mobile', [AbsenController::class, 'dataMobile'])
            ->name('absen.dataMobile');
        Route::post('/', [AbsenController::class, 'store'])
            ->name('absen.store');
        Route::get('/sub-kegiatan', [AbsenController::class, 'subKegiatan'])
            ->name('absen.sub');
        Route::get('/data', [AbsenController::class, 'dataNested'])
            ->name('absen.dataNested');
    });
    
    // ======================
    // 🔹 KESEHATAN
    // ======================
    Route::prefix('kesehatan')->group(function () {
        Route::get('/', [KesehatanController::class, 'index'])
            ->name('kesehatan.index');
        Route::get('/mobile', [KesehatanController::class, 'index'])
            ->name('kesehatan.mobile');
        Route::get('/data-mobile', [KesehatanController::class, 'dataMobile'])
            ->name('kesehatan.data-mobile');
        Route::post('/', [KesehatanController::class, 'store'])
            ->name('kesehatan.store');
        Route::post('/update-batch', [KesehatanController::class, 'updateBatch'])
            ->name('kesehatan.updateBatch');
        Route::get('/sub-kegiatan', [KesehatanController::class, 'subKegiatan'])
            ->name('kesehatan.sub');
        Route::get('/data', [KesehatanController::class, 'dataNested'])
            ->name('kesehatan.dataNested');
    });
    
    // ======================
    // 🔹 LAPORAN KEGIATAN
    // ======================
    Route::prefix('laporan')->group(function () {
        Route::get('/', [LaporanKegiatanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/mobile', [LaporanKegiatanController::class, 'index'])
            ->name('laporan.mobile');
        Route::get('/data-mobile', [LaporanKegiatanController::class, 'dataMobile'])
            ->name('laporan.mobileData');
        Route::post('/generate-pdf', [LaporanKegiatanController::class, 'generatePdf'])
            ->name('laporan.generate-pdf');
        Route::get('/view/{filename}', [LaporanKegiatanController::class, 'viewPdf'])
            ->name('laporan.view');
        Route::get('/download/{filename}', [LaporanKegiatanController::class, 'downloadPdf'])
            ->name('laporan.download');
        Route::post('/pdf', [LaporanKegiatanController::class, 'exportPdf'])
            ->name('laporan.pdf');
        Route::post('/send-whatsapp', [LaporanKegiatanController::class, 'sendWhatsApp'])
            ->name('laporan.send-whatsapp');
        Route::post('/send-whatsapp-all', [LaporanKegiatanController::class, 'sendWhatsAppToAll'])
            ->name('laporan.send-whatsapp-all');
        Route::post('/send-whatsapp-selected', [LaporanKegiatanController::class, 'sendWhatsAppSelected'])
            ->name('laporan.send-whatsapp-selected');
        Route::get('/test-pdf', [LaporanKegiatanController::class, 'testPdf'])
            ->name('laporan.test-pdf');
        Route::get('/cleanup', [LaporanKegiatanController::class, 'cleanupOldFiles'])
            ->name('laporan.cleanup');
    });
    
    // ======================
    // 🔹 ROUTE PROFILE
    // ======================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ======================
// 🔹 GROUP ROUTES UNTUK ADMIN SAJA
// ======================
Route::middleware(['auth', 'can:akses-admin'])->group(function () {
    
    // ======================
    // 🔹 CRUD KEGIATAN (Admin Only)
    // ======================
    Route::prefix('kegiatan')->group(function () {
        Route::get('/', [KegiatanController::class, 'index'])
            ->name('kegiatan.index');
        Route::post('/', [KegiatanController::class, 'store'])
            ->name('kegiatan.store');
        Route::get('/{id}', [KegiatanController::class, 'show'])
            ->name('kegiatan.show');
        Route::put('/{id}', [KegiatanController::class, 'update'])
            ->name('kegiatan.update');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])
            ->name('kegiatan.destroy');
        Route::get('/parents', [KegiatanController::class, 'parents'])
            ->name('kegiatan.parents');
        Route::post('/restore/{id}', [KegiatanController::class, 'restore'])
            ->name('kegiatan.restore');
        Route::delete('/force/{id}', [KegiatanController::class, 'forceDelete'])
            ->name('kegiatan.forceDelete');
    });
    
    // ======================
    // 🔹 CRUD MUSYRIF (Admin Only)
    // ======================
    Route::prefix('musyrif')->group(function () {
        Route::get('/', [MusyrifController::class, 'index'])
            ->name('musyrif.index');
        Route::post('/', [MusyrifController::class, 'store'])
            ->name('musyrif.store');
        Route::get('/{id}/edit', [MusyrifController::class, 'edit'])
            ->name('musyrif.edit');
        Route::delete('/{id}', [MusyrifController::class, 'destroy'])
            ->name('musyrif.destroy');
        Route::get('/deleted', [MusyrifController::class, 'deleted'])
            ->name('musyrif.deleted');
        Route::post('/restore/{id}', [MusyrifController::class, 'restore'])
            ->name('musyrif.restore');
    });
    
    // ======================
    // 🔹 CRUD SANTRI (Admin Only)
    // ======================
    Route::prefix('santri')->group(function () {
        Route::get('/', [SantriController::class, 'index'])
            ->name('santri.index');
        Route::get('/edit/{id}', [SantriController::class, 'edit'])
            ->name('santri.edit');
        Route::post('/', [SantriController::class, 'store'])
            ->name('santri.store');
        Route::delete('/{id}', [SantriController::class, 'destroy'])
            ->name('santri.destroy');
        Route::post('/restore/{id}', [SantriController::class, 'restore'])
            ->name('santri.restore');
        Route::delete('/force/{id}', [SantriController::class, 'forceDelete'])
            ->name('santri.forceDelete');
    });
});

// ======================
// 🔹 API INTERNAL (untuk AJAX calls) - juga perlu auth
// ======================
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Get musyrif untuk dropdown
    Route::get('/musyrif/list', function () {
        $musyrif = \App\Models\User::role('musyrif')
            ->select('id', 'name', 'email')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            });
            
        return response()->json($musyrif);
    });
    
    // Get halaqah untuk dropdown
    Route::get('/halaqah/list', function () {
        $halaqah = \App\Models\Halaqah::select('id', 'kode', 'nama_halaqah')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->kode . ' - ' . $item->nama_halaqah
                ];
            });
            
        return response()->json($halaqah);
    });
    
    // Get santri untuk dropdown
    Route::get('/santri/list', function () {
        $santri = \App\Models\User::role('santri')
            ->select('id', 'name', 'email')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            });
            
        return response()->json($santri);
    });
    
    // Get kegiatan untuk dropdown
    Route::get('/kegiatan/list', function () {
        $kegiatan = \App\Models\Kegiatan::whereNull('parent_id')
            ->select('id', 'nama_kegiatan')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nama_kegiatan
                ];
            });
            
        return response()->json($kegiatan);
    });
});

require __DIR__.'/auth.php';