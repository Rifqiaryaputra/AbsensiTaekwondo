<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AnggotaDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PengaturanAnggotaController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\SettingsController;
use App\Livewire\PengajuanIzin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin & Petugas
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        Route::post('/jadwal/{id}/tutup', [AbsensiController::class, 'closeManual'])->name('jadwal.tutup');
        Route::get('/data-anggota', [AnggotaController::class, 'index'])->name('anggota.index');
        Route::get('/data-anggota/export', [AnggotaController::class, 'export'])->name('anggota.export');
        Route::get('/perizinan', [PerizinanController::class, 'index'])->name('perizinan.index');
        Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/export', [RekapController::class, 'export'])->name('rekap.export');
    });

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas.index');
        Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    });

    // Khusus Anggota
    Route::middleware('role:anggota')->group(function () {
        Route::get('/dashboard-anggota', [AnggotaDashboardController::class, 'index'])->name('anggota.dashboard');
        Route::get('/dashboard-anggota/download-qr', [AnggotaDashboardController::class, 'downloadQR'])->name('anggota.download.qr');
        Route::get('/anggota/izin', PengajuanIzin::class)->name('anggota.izin');
        Route::get('/pengaturan-anggota', [PengaturanAnggotaController::class, 'index'])->name('anggota.pengaturan');
        Route::patch('/pengaturan-anggota/password', [PengaturanAnggotaController::class, 'updatePassword'])->name('anggota.pengaturan.password');
    });

    // Profil (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
