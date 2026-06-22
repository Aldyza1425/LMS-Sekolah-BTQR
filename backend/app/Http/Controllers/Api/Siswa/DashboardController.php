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

        // Module progress per modul — uses ModulProgress.selesai which is set when student completes pre/post tests
        $modulProgress = $moduls->map(function ($modul) use ($userId) {
            $totalMateris = $modul->materis_count ?? $modul->materis()->count();

            // Count materis this student has completed (selesai=true in ModulProgress)
            $completedMateris = ModulProgress::where('user_id', $userId)
                ->where('modul_id', $modul->id)
                ->where('selesai', true)
                ->distinct('materi_id')
                ->count('materi_id');

            $percent = $totalMateris > 0 ? round(($completedMateris / $totalMateris) * 100) : 0;

            return [
                'id'        => $modul->id,
                'slug'      => $modul->slug,
                'title'     => $modul->judul,
                'subtitle'  => $completedMateris > 0
                    ? "{$completedMateris} dari {$totalMateris} materi selesai"
                    : 'Belum Dimulai',
                'progress'  => $percent,
                'thumbnail' => $modul->thumbnail_url,
            ];
        });

        // Dynamic schedule: Try Out announcements from pengajar
        $upcomingTryOuts = TryOut::where('is_active', true)
            ->orderBy('mulai_at', 'desc')
            ->take(5)
            ->get();

        $schedule = $upcomingTryOuts->map(function ($tryout) {
            $mulaiAt = \Carbon\Carbon::parse($tryout->mulai_at);
            return [
                'date' => $mulaiAt->format('d'),
                'month' => strtoupper($mulaiAt->locale('id')->isoFormat('MMM')),
                'title' => 'Try Out: ' . $tryout->judul,
                'desc' => 'Ujian dimulai pada ' . $mulaiAt->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm'),
            ];
        })->toArray();

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
