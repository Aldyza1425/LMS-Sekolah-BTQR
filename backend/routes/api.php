<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Auth ---
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// --- LMS (Protected) ---
Route::middleware('auth:sanctum')->group(function () {

    // User Profile (General)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Notifications (General for all authenticated roles)
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);

        // Settings
        Route::get('/settings/lembaga', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'getLembaga']);
        Route::post('/settings/lembaga', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'saveLembaga']);
        Route::get('/settings/system', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'getSystem']);
        Route::post('/settings/system', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'saveSystem']);
        Route::post('/settings/backup', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'backup']);
        Route::post('/settings/change-password', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'changePassword']);

        // Pengajar Management
        Route::get('pengajar', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'index']);
        Route::post('pengajar', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'store']);
        Route::get('pengajar/{id}', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'show']);
        Route::post('pengajar/{id}/update', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'update']);
        Route::post('pengajar/{id}/verify', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'verify']);
        Route::delete('pengajar/{id}', [\App\Http\Controllers\Api\Admin\PengajarController::class, 'destroy']);

        // Siswa Management
        Route::get('siswa', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'index']);
        Route::post('siswa', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'store']);
        Route::get('siswa/{id}', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'show']);
        Route::post('siswa/{id}', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'update']);
        Route::post('siswa/{id}/verify', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'verify']);
        Route::delete('siswa/{id}', [\App\Http\Controllers\Api\Admin\SiswaController::class, 'destroy']);
        Route::post('upload', [\App\Http\Controllers\Api\Admin\UploadController::class, 'upload']);
    });

    // Pengajar Routes
    Route::middleware('pengajar')->prefix('pengajar')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Pengajar\DashboardController::class, 'index']);
        Route::apiResource('moduls', \App\Http\Controllers\Api\Pengajar\ModulController::class);
        Route::post('moduls/{id}/materi', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'store']);
        Route::get('moduls/{id}/materi/{mid}', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'show']);
        Route::post('moduls/{id}/materi/{mid}', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'update']);
        Route::delete('moduls/{id}/materi/{mid}', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'destroy']);
        Route::get('moduls/{id}/materi/{mid}/submissions', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'getSubmissions']);
        Route::post('moduls/{id}/materi/{mid}/submissions/{progress_id}/grade', [\App\Http\Controllers\Api\Pengajar\MateriController::class, 'gradeSubmission']);
        Route::apiResource('try-out', \App\Http\Controllers\Api\Pengajar\TryOutController::class);
        Route::post('try-out/{id}/soal/batch', [\App\Http\Controllers\Api\Pengajar\TryOutSoalController::class, 'storeBatch']);
        Route::post('try-out/{id}/soal', [\App\Http\Controllers\Api\Pengajar\TryOutSoalController::class, 'store']);
        Route::put('try-out/{id}/soal/{soal_id}', [\App\Http\Controllers\Api\Pengajar\TryOutSoalController::class, 'update']);
        Route::delete('try-out/{id}/soal/{soal_id}', [\App\Http\Controllers\Api\Pengajar\TryOutSoalController::class, 'destroy']);
        Route::get('try-out/{id}/rekap', [\App\Http\Controllers\Api\Pengajar\TryOutController::class, 'rekap']);
        Route::post('upload', [\App\Http\Controllers\Api\Pengajar\UploadController::class, 'upload']);

        // Penilaian Siswa
        Route::get('penilaian', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'index']);
        Route::get('penilaian/siswa/{id}', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'show']);
        Route::post('penilaian/try-out/{try_out_id}/siswa/{user_id}/approve-retake', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'approveRetake']);
        Route::post('penilaian/kuis', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'storeKuis']);
        Route::put('penilaian/kuis/{id}', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'updateKuis']);
        Route::delete('penilaian/kuis/{id}', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'destroyKuis']);
        Route::post('penilaian/try-out', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'storeTryOut']);
        Route::put('penilaian/try-out/{id}', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'updateTryOut']);
        Route::delete('penilaian/try-out/{id}', [\App\Http\Controllers\Api\Pengajar\PenilaianController::class, 'destroyTryOut']);
    });

    // Siswa Routes
    Route::middleware('siswa')->prefix('siswa')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Siswa\DashboardController::class, 'index']);
        Route::post('/update-profile', [\App\Http\Controllers\Api\Siswa\ProfilController::class, 'update']);
        Route::post('/change-password', [\App\Http\Controllers\Api\Siswa\ProfilController::class, 'changePassword']);
        Route::get('/grades', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'grades']);
        Route::get('/moduls', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'index']);
        Route::get('/moduls/{id}', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'show']);
        Route::get('/materis/{id}', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'showMateri']);
        Route::get('/materis/{id}/history', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'getQuizHistory']);
        Route::post('/materis/{id}/submit', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'submitQuiz']);
        Route::post('/materis/{id}/complete-component', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'completeComponent']);
        Route::post('/materis/{id}/submit-resume', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'submitResume']);
        Route::post('/materis/{id}/submit-tugas', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'submitTugas']);
        Route::get('/submissions/{id}', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'showSubmission']);
        Route::post('/moduls/{id}/progress', [\App\Http\Controllers\Api\Siswa\ModulController::class, 'updateProgress']);
        Route::get('/try-out', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'index']);
        Route::get('/try-out-grades', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'grades']);
        Route::get('/try-out/{id}', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'show']);
        Route::post('/try-out/{id}/submit', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'submit']);
        Route::get('/try-out/{id}/hasil', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'hasil']);
        Route::post('/try-out/{id}/request-retake', [\App\Http\Controllers\Api\Siswa\TryOutController::class, 'requestRetake']);
    });
});
