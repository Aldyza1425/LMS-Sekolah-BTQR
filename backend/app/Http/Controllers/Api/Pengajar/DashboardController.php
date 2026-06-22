<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pengajar_id = $request->user()->id;
        $siswa_aktif = \App\Models\Siswa::where('is_active', true)->count();
        $modul_aktif = \App\Models\Modul::where('is_active', true)->count();
        $siswa_selesai_tryout = \App\Models\TryOutHasil::distinct('user_id')->count('user_id');
        $nilai_tertinggi = \App\Models\TryOutHasil::max('nilai') ?? 0;
 
        // 1. Aktivitas Terbaru Siswa (Registrations, Tryout attempts, Module progress)
        $registrations = \App\Models\Siswa::latest()
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'registration',
                    'title' => $s->name,
                    'subtitle' => 'Mendaftar sebagai Calon Santri',
                    'time' => $s->created_at ? $s->created_at->diffForHumans() : '-',
                    'score' => null,
                    'badge' => $s->is_active ? 'Aktif' : 'Perlu Verifikasi',
                    'badge_type' => $s->is_active ? 'success' : 'warning',
                    'created_at' => $s->created_at
                ];
            });

        $tryout_results = \App\Models\TryOutHasil::with(['user', 'tryOut'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($h) {
                return [
                    'type' => 'tryout',
                    'title' => $h->user ? $h->user->name : 'Siswa',
                    'subtitle' => 'Menyelesaikan Try Out: ' . ($h->tryOut ? $h->tryOut->judul : 'Try Out'),
                    'time' => $h->created_at ? $h->created_at->diffForHumans() : ($h->selesai_at ? \Carbon\Carbon::parse($h->selesai_at)->diffForHumans() : '-'),
                    'score' => $h->nilai,
                    'badge' => $h->lulus ? 'Lulus' : 'Tidak Lulus',
                    'badge_type' => $h->lulus ? 'success' : 'danger',
                    'created_at' => $h->created_at ?: $h->selesai_at
                ];
            });

        $materi_progresses = \App\Models\MateriProgress::with(['user', 'materi.modul'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($p) {
                $materi_title = $p->materi ? $p->materi->judul : 'Materi';
                $modul_title = ($p->materi && $p->materi->modul) ? $p->materi->modul->judul : '';
                $subtitle = 'Mempelajari ' . $materi_title . ($modul_title ? " ({$modul_title})" : "");
                
                if ($p->is_completed) {
                    $subtitle = 'Menyelesaikan ' . $materi_title . ($modul_title ? " ({$modul_title})" : "");
                }
                
                return [
                    'type' => 'progress',
                    'title' => $p->user ? $p->user->name : 'Siswa',
                    'subtitle' => $subtitle,
                    'time' => $p->updated_at ? $p->updated_at->diffForHumans() : '-',
                    'score' => $p->is_post_test_done ? $p->post_test_score : ($p->is_pre_test_done ? $p->pre_test_score : null),
                    'badge' => $p->is_completed ? 'Selesai' : 'Sedang Dipelajari',
                    'badge_type' => $p->is_completed ? 'success' : 'info',
                    'created_at' => $p->updated_at
                ];
            });

        // Gabungkan & urutkan aktivitas berdasarkan created_at
        $recent_activities = collect()
            ->concat($registrations)
            ->concat($tryout_results)
            ->concat($materi_progresses)
            ->sortByDesc('created_at')
            ->take(8)
            ->values()
            ->all();

        // 2. Event Jadwal TryOut yang dibuat oleh pengajar ini
        $tryouts = \App\Models\TryOut::where('created_by', $pengajar_id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'judul' => $t->judul,
                    'deskripsi' => $t->deskripsi,
                    'mulai_at' => $t->mulai_at,
                    'selesai_at' => $t->selesai_at,
                    'durasi_menit' => $t->durasi_menit,
                    'nilai_lulus' => $t->nilai_lulus,
                    'is_active' => $t->is_active
                ];
            });
 
        return response()->json([
            'stats' => [
                'siswa_aktif' => $siswa_aktif,
                'modul_aktif' => $modul_aktif,
                'siswa_selesai_tryout' => $siswa_selesai_tryout,
                'nilai_tertinggi' => round($nilai_tertinggi, 1)
            ],
            'recent_activities' => $recent_activities,
            'tryouts' => $tryouts
        ]);
    }
}
