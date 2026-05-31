<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use App\Models\ModulProgress;
use App\Models\TryOut;
use App\Models\TryOutHasil;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Active moduls
        $moduls = Modul::where('is_active', true)->withCount('materis')->get();
        $activeCourses = $moduls->count();

        // Completed moduls (selesai = true)
        $completedModules = ModulProgress::where('user_id', $userId)
            ->where('selesai', true)
            ->count();

        // Average score from try out
        $averageScore = TryOutHasil::where('user_id', $userId)->avg('nilai') ?? 0;

        // Module progress per modul
        $modulProgress = $moduls->map(function ($modul) use ($userId) {
            $totalMateris = $modul->materis_count ?? 0;
            $completedMateris = ModulProgress::where('user_id', $userId)
                ->where('modul_id', $modul->id)
                ->where('selesai', true)
                ->count();

            $percent = $totalMateris > 0 ? round(($completedMateris / $totalMateris) * 100) : 0;

            return [
                'id' => $modul->id,
                'title' => $modul->judul,
                'subtitle' => $percent > 0
                    ? 'Modul Aktif: ' . ($modul->mata_pelajaran ?? $modul->judul)
                    : 'Belum Dimulai',
                'progress' => $percent,
                'thumbnail' => $modul->thumbnail_url,
            ];
        });

        // Dynamic schedule: upcoming Try Out announcements
        $upcomingTryOuts = TryOut::where('is_active', true)
            ->where('mulai_at', '>', now())
            ->orderBy('mulai_at', 'asc')
            ->take(5)
            ->get();

        $schedule = $upcomingTryOuts->map(function ($tryout) {
            $mulaiAt = \Carbon\Carbon::parse($tryout->mulai_at);
            return [
                'date' => $mulaiAt->format('d'),
                'month' => strtoupper($mulaiAt->locale('id')->isoFormat('MMM')),
                'title' => 'PENGUMUMAN: Try Out ' . $tryout->judul,
                'desc' => 'Ujian dimulai pada ' . $mulaiAt->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm'),
            ];
        })->toArray();

        // Fallback ke static jika tidak ada upcoming try out
        if (empty($schedule)) {
            $schedule = [
                ['date' => '10', 'month' => 'APR', 'title' => 'Pre-Test Modul 4', 'desc' => 'Ujian Nahwu Modul 4 dijadwalkan tanggal 12 Apr'],
                ['date' => '9', 'month' => 'APR', 'title' => 'Tugas Shorof', 'desc' => 'Deadline Tashrif Fi\'il Tsulatsi: 18 April'],
                ['date' => '8', 'month' => 'APR', 'title' => 'Kelas Muhadatsah', 'desc' => 'Sesi 5 akan dimulai 14 April jam 13:00'],
            ];
        }

        return response()->json([
            'stats' => [
                'active_courses' => $activeCourses,
                'completed_modules' => $completedModules,
                'learning_time' => '0j 3m',
                'average_score' => round($averageScore, 1),
            ],
            'modul_progress' => $modulProgress,
            'schedule' => $schedule,
        ]);
    }
}
