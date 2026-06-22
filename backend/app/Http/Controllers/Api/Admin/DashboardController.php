<?php

namespace App\Http\Controllers\Api\Admin;

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
        $total_siswa = \App\Models\Siswa::count();

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

        // 2. Event Jadwal TryOut
        $tryouts = \App\Models\TryOut::with('creator')
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
                    'is_active' => $t->is_active,
                    'creator_name' => $t->creator ? $t->creator->name : 'Pengajar'
                ];
            });

        // 3. Modul yang Telah Dibuat
        $moduls = \App\Models\Modul::with(['creator', 'materis'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'judul' => $m->judul,
                    'arabic_title' => $m->arabic_title,
                    'level' => $m->level,
                    'mata_pelajaran' => $m->mata_pelajaran,
                    'materi_count' => $m->materis->count(),
                    'creator_name' => $m->creator ? $m->creator->name : 'Pengajar',
                    'is_active' => $m->is_active,
                    'created_at' => $m->created_at ? $m->created_at->format('d M Y') : '-'
                ];
            });

        return response()->json([
            'stats' => [
                'siswa_aktif' => $siswa_aktif,
                'modul_aktif' => $modul_aktif,
                'verifikasi' => $verifikasi,
                'rerata_nilai' => round($rerata_nilai, 1),
                'total_siswa' => $total_siswa
            ],
            'recent_activities' => $recent_activities,
            'tryouts' => $tryouts,
            'moduls' => $moduls
        ]);
    }
}
