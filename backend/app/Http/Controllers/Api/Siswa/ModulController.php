<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;

class ModulController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $modules = Modul::where('is_active', true)
            ->withCount('materis')
            ->get();

        $modules = $modules->map(function ($modul) use ($userId) {
            $totalMateris = $modul->materis_count ?? 0;
            $completedMateris = \App\Models\ModulProgress::where('user_id', $userId)
                ->where('modul_id', $modul->id)
                ->where('selesai', true)
                ->count();
            
            $modul->progress = $totalMateris > 0 ? round(($completedMateris / $totalMateris) * 100) : 0;
            return $modul;
        });

        return response()->json([
            'success' => true,
            'data' => $modules
        ]);
    }

    public function grades()
    {
        $userId = auth()->id();

        $grades = \App\Models\MateriProgress::where('user_id', $userId)
            ->where(function($query) {
                $query->whereNotNull('pre_test_score')
                      ->orWhereNotNull('post_test_score');
            })
            ->with(['materi.modul.materis' => function($q) {
                $q->orderBy('urutan', 'asc');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades
        ]);
    }

    public function show($id)
    {
        $userId = auth()->id();

        $module = Modul::with(['materis' => function ($q) use ($userId) {
            $q->orderBy('urutan', 'asc')
              ->with(['progresses' => function($pq) use ($userId) {
                  $pq->where('user_id', $userId);
              }]);
        }])
            ->withCount('materis')
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        // Attach progress directly to each materi for frontend convenience
        $module->materis->each(function($materi) {
            $materi->progress = $materi->progresses->first();
            unset($materi->progresses);
        });

        return response()->json([
            'success' => true,
            'data' => $module
        ]);
    }

    public function showMateri($id)
    {
        $userId = auth()->id();
        $materi = Materi::with('soals')->findOrFail($id);

        $progress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $userId,
            'materi_id' => $id
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $materi->id,
                'judul' => $materi->judul,
                'deskripsi' => $materi->deskripsi,
                'tipe' => $materi->tipe,
                'file_path' => $materi->file_path,
                'link_url' => $materi->link_url,
                'durasi' => $materi->durasi,
                
                'has_pretest' => $materi->has_pretest,
                'has_resume' => $materi->has_resume,
                'resume_instruction' => $materi->resume_instruction,
                'has_tugas' => $materi->has_tugas,
                'tugas_instruction' => $materi->tugas_instruction,
                'has_posttest' => $materi->has_posttest,
                'is_arabic' => $materi->is_arabic,

                'pre_tests' => $materi->has_pretest ? $materi->soals->where('tipe_soal', 'pre_test')->values() : [],
                'post_tests' => $materi->has_posttest ? $materi->soals->where('tipe_soal', 'post_test')->values() : [],
                
                'progress' => $progress
            ]
        ]);
    }

    public function submitQuiz(Request $request, $id)
    {
        $userId = auth()->id();
        $materi = Materi::with('soals')->findOrFail($id);
        $answers = $request->answers; // ['soal_id' => 'answer']
        $tipe_kuis = $request->input('tipe_kuis', 'post_test');
        
        $correct = 0;
        $soals = $materi->soals->where('tipe_soal', $tipe_kuis);
        $total = $soals->count();
        $results = [];
        foreach ($soals as $soal) {
            $userAnswer = $answers[$soal->id] ?? null;
            $isCorrect = $userAnswer == $soal->jawaban;
            if ($isCorrect) $correct++;
            
            $results[] = [
                'soal_id' => $soal->id,
                'user_answer' => $userAnswer,
                'correct_answer' => $soal->jawaban,
                'is_correct' => $isCorrect,
                'soal' => [
                    'soal' => $soal->soal,
                    'a' => $soal->a,
                    'b' => $soal->b,
                    'c' => $soal->c,
                    'd' => $soal->d,
                    'jawaban' => $soal->jawaban
                ]
            ];
        }

        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= 70; // 70 as passing grade

        // Create submission record for history
        $submission = QuizSubmission::create([
            'user_id' => $userId,
            'materi_id' => $materi->id,
            'score' => $score,
            'correct_count' => $correct,
            'total_count' => $total,
            'passed' => $passed,
            'time_used' => $request->time_used ?? null,
            'answers' => $results,
            'tipe_kuis' => $tipe_kuis
        ]);

        // Update MateriProgress
        $materiProgress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $userId,
            'materi_id' => $materi->id
        ]);

        if ($tipe_kuis === 'pre_test') {
            $materiProgress->is_pre_test_done = true;
            $materiProgress->pre_test_score = $score;
        } else if ($tipe_kuis === 'post_test') {
            $materiProgress->is_post_test_done = true;
            $materiProgress->post_test_score = $score;
        }
        $materiProgress->save();

        // Always update user modul progress (no KKM blocker)
        if (in_array($tipe_kuis, ['post_test', 'pre_test'])) {
            $this->updateUserProgress($materi, 100, true, $score);
        }

        return response()->json([
            'success' => true,
            'id' => $submission->id,
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'passed' => $passed,
            'question_results' => $results
        ]);
    }

    public function getQuizHistory($id)
    {
        $userId = auth()->id();
        
        $history = QuizSubmission::where('user_id', $userId)
            ->where('materi_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function showSubmission($id)
    {
        $submission = QuizSubmission::with('materi')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $submission
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);
        $this->updateUserProgress($materi, $request->persen, $request->selesai);

        return response()->json(['success' => true]);
    }

    private function updateUserProgress($materi, $persen, $selesai, $skor = null)
    {
        $userId = auth()->id();

        $progress = \App\Models\ModulProgress::firstOrNew([
            'user_id' => $userId, 
            'modul_id' => $materi->modul_id, 
            'materi_id' => $materi->id
        ]);
        
        $progress->persen = $persen;
        $progress->selesai = $selesai;
        if ($skor !== null) {
            $progress->skor = $skor;
        }
        $progress->save();
    }

    public function completeComponent(Request $request, $id)
    {
        $userId = auth()->id();
        $component = $request->component; // 'content', 'pre_test', 'post_test'

        $progress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $userId,
            'materi_id' => $id
        ]);

        if ($component === 'content') {
            $progress->is_content_read = true;
        }

        $progress->save();

        return response()->json(['success' => true, 'progress' => $progress]);
    }

    public function submitResume(Request $request, $id)
    {
        $userId = auth()->id();
        $request->validate([
            'resume_text' => 'required|string',
        ]);

        $progress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $userId,
            'materi_id' => $id
        ]);

        $progress->is_resume_submitted = true;
        $progress->resume_text = $request->resume_text;
        $progress->save();

        return response()->json([
            'success' => true,
            'message' => 'Resume berhasil dikirim!',
            'progress' => $progress
        ]);
    }

    public function submitTugas(Request $request, $id)
    {
        $userId = auth()->id();
        $request->validate([
            'tugas_file' => 'required|file|max:5120', // max 5MB
        ]);

        $progress = \App\Models\MateriProgress::firstOrCreate([
            'user_id' => $userId,
            'materi_id' => $id
        ]);

        if ($request->hasFile('tugas_file')) {
            $path = $request->file('tugas_file')->store('tugas', 'public');
            $progress->tugas_file_path = $path;
            $progress->is_tugas_submitted = true;
            $progress->save();

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dikirim!',
                'progress' => $progress
            ]);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 400);
    }
}
