<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa_aktif = \App\Models\Siswa::where('is_active', true)->count();
        $modul_aktif = \App\Models\Modul::where('is_active', true)->count();
        $verifikasi = \App\Models\Siswa::where('is_active', false)->count();
        $rerata_nilai = \App\Models\TryOutHasil::avg('nilai') ?? 0;

        // Aktivitas Terbaru (New registrations)
        $recent_activities = \App\Models\Siswa::latest()
            ->take(5)
            ->get()
            ->map(function ($u) {
                return [
                    'title' => $u->name,
                    'subtitle' => 'Mendaftar sebagai Calon Santri',
                    'value' => $u->is_active ? 100 : 0,
                    'score' => 0
                ];
            });

        return response()->json([
            'stats' => [
                'siswa_aktif' => $siswa_aktif,
                'modul_aktif' => $modul_aktif,
                'verifikasi' => $verifikasi,
                'rerata_nilai' => round($rerata_nilai, 1)
            ],
            'recent_activities' => $recent_activities
        ]);
    }
}
