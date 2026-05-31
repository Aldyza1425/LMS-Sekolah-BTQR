<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\ModulProgress;
use App\Models\TryOutHasil;
use App\Models\QuizSubmission;
use App\Models\Materi;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $siswa = Siswa::get()->map(function ($user) {
            $modulProgress = ModulProgress::where('user_id', $user->id)
                ->with('modul')
                ->get();
            $tryOutHasil = TryOutHasil::where('user_id', $user->id)
                ->with('tryOut')
                ->get();
                
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'modul_progress' => $modulProgress,
                'try_out_hasil' => $tryOutHasil,
            ];
        });

        return response()->json($siswa);
    }

    public function show($id)
    {
        $user = Siswa::findOrFail($id);
        
        // Fetch pre-tests
        $preTests = QuizSubmission::where('user_id', $user->id)
            ->where('tipe_kuis', 'pre_test')
            ->with('materi.modul')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Fetch post-tests
        $postTests = QuizSubmission::where('user_id', $user->id)
            ->where('tipe_kuis', 'post_test')
            ->with('materi.modul')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Fetch tryouts
        $tryOutHasil = TryOutHasil::where('user_id', $user->id)
            ->with('tryOut')
            ->orderBy('selesai_at', 'desc')
            ->get();
            
        // Fetch all materis and tryouts to allow creating/adding manual scores
        $allMateris = Materi::with('modul')->orderBy('judul', 'asc')->get();
        $allTryOuts = \App\Models\TryOut::orderBy('judul', 'asc')->get();
            
        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'pre_tests' => $preTests,
                'post_tests' => $postTests,
                'try_out_hasil' => $tryOutHasil,
                'all_materis' => $allMateris,
                'all_tryouts' => $allTryOuts
            ]
        ]);
    }

    public function approveRetake($try_out_id, $user_id)
    {
        $hasil = TryOutHasil::where('try_out_id', $try_out_id)
            ->where('user_id', $user_id)
            ->first();

        if ($hasil) {
            // Delete the record completely so the student can take it again
            \App\Models\TryOutJawaban::where('try_out_id', $try_out_id)
                ->where('user_id', $user_id)
                ->delete();

            $hasil->delete();

            return response()->json(['success' => true, 'message' => 'Ujian ulang disetujui.']);
        }

        return response()->json(['success' => false, 'message' => 'Riwayat tidak ditemukan.'], 404);
    }

    public function storeKuis(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:siswas,id',
            'materi_id' => 'required|exists:materis,id',
            'tipe_kuis' => 'required|in:pre_test,post_test',
            'score' => 'required|integer|min:0|max:100',
        ]);

        $score = $request->score;
        $passed = $score >= 70;

        $submission = QuizSubmission::create([
            'user_id' => $request->user_id,
            'materi_id' => $request->materi_id,
            'score' => $score,
            'correct_count' => round(($score / 100) * 10),
            'total_count' => 10,
            'passed' => $passed,
            'tipe_kuis' => $request->tipe_kuis,
        ]);

        // Update MateriProgress
        $materi = Materi::find($request->materi_id);
        $materiProgress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $request->user_id,
            'materi_id' => $materi->id
        ]);
        if ($request->tipe_kuis === 'pre_test') {
            $materiProgress->is_pre_test_done = true;
            $materiProgress->pre_test_score = $score;
        } else {
            $materiProgress->is_post_test_done = true;
            $materiProgress->post_test_score = $score;
        }
        $materiProgress->save();

        // Update ModulProgress
        $modulProgress = ModulProgress::firstOrCreate([
            'user_id' => $request->user_id,
            'modul_id' => $materi->modul_id,
            'materi_id' => $materi->id
        ]);
        $modulProgress->persen = 100;
        $modulProgress->selesai = true;
        $modulProgress->skor = $score;
        $modulProgress->save();

        return response()->json([
            'success' => true,
            'message' => 'Nilai kuis berhasil ditambahkan!',
            'data' => $submission
        ]);
    }

    public function updateKuis(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100',
        ]);

        $submission = QuizSubmission::findOrFail($id);
        $score = $request->score;
        $passed = $score >= 70;

        $submission->update([
            'score' => $score,
            'passed' => $passed,
        ]);

        // Update progress scores
        $materi = Materi::find($submission->materi_id);
        if ($materi) {
            $materiProgress = \App\Models\MateriProgress::where('user_id', $submission->user_id)
                ->where('materi_id', $materi->id)
                ->first();
            if ($materiProgress) {
                if ($submission->tipe_kuis === 'pre_test') {
                    $materiProgress->pre_test_score = $score;
                } else {
                    $materiProgress->post_test_score = $score;
                }
                $materiProgress->save();
            }

            $modulProgress = ModulProgress::where('user_id', $submission->user_id)
                ->where('materi_id', $materi->id)
                ->first();
            if ($modulProgress) {
                $modulProgress->skor = $score;
                $modulProgress->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Nilai kuis berhasil diubah!',
            'data' => $submission
        ]);
    }

    public function destroyKuis($id)
    {
        $submission = QuizSubmission::findOrFail($id);
        
        $materi = Materi::find($submission->materi_id);
        if ($materi) {
            $materiProgress = \App\Models\MateriProgress::where('user_id', $submission->user_id)
                ->where('materi_id', $materi->id)
                ->first();
            if ($materiProgress) {
                if ($submission->tipe_kuis === 'pre_test') {
                    $materiProgress->is_pre_test_done = false;
                    $materiProgress->pre_test_score = null;
                } else {
                    $materiProgress->is_post_test_done = false;
                    $materiProgress->post_test_score = null;
                }
                $materiProgress->save();
            }

            $modulProgress = ModulProgress::where('user_id', $submission->user_id)
                ->where('materi_id', $materi->id)
                ->first();
            if ($modulProgress) {
                $modulProgress->delete();
            }
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nilai kuis berhasil dihapus!'
        ]);
    }

    public function storeTryOut(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:siswas,id',
            'try_out_id' => 'required|exists:try_outs,id',
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        $nilai = $request->nilai;
        $lulus = $nilai >= 70;

        $hasil = TryOutHasil::create([
            'user_id' => $request->user_id,
            'try_out_id' => $request->try_out_id,
            'nilai' => $nilai,
            'lulus' => $lulus,
            'selesai_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nilai try out berhasil ditambahkan!',
            'data' => $hasil
        ]);
    }

    public function updateTryOut(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
        ]);

        $hasil = TryOutHasil::findOrFail($id);
        $nilai = $request->nilai;
        $lulus = $nilai >= 70;

        $hasil->update([
            'nilai' => $nilai,
            'lulus' => $lulus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nilai try out berhasil diubah!',
            'data' => $hasil
        ]);
    }

    public function destroyTryOut($id)
    {
        $hasil = TryOutHasil::findOrFail($id);
        
        \App\Models\TryOutJawaban::where('try_out_id', $hasil->try_out_id)
            ->where('user_id', $hasil->user_id)
            ->delete();

        $hasil->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nilai try out berhasil dihapus!'
        ]);
    }
}
