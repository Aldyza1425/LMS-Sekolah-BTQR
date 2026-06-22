<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Materi;
use App\Models\MateriSoal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MateriController extends Controller
{
    public function store(Request $request, $modul_id)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'tipe' => 'required|in:dokumen,video,link,post_test,teks',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|max:51200', // max 50MB
            'link_url' => 'nullable|string|max:500',
            'durasi' => 'nullable|integer|min:0',
            'has_pretest' => 'nullable|boolean',
            'has_posttest' => 'nullable|boolean',
            'is_arabic' => 'nullable|boolean',

            'quizzes' => 'nullable|array',
            'quizzes.*.soal' => 'required_with:quizzes|string',
            'quizzes.*.a' => 'required_with:quizzes|string',
            'quizzes.*.b' => 'required_with:quizzes|string',
            'quizzes.*.c' => 'required_with:quizzes|string',
            'quizzes.*.d' => 'required_with:quizzes|string',
            'quizzes.*.jawaban' => 'required_with:quizzes|in:a,b,c,d',
            'pre_tests' => 'nullable|array',
            'pre_tests.*.soal' => 'required_with:pre_tests|string',
            'pre_tests.*.a' => 'required_with:pre_tests|string',
            'pre_tests.*.b' => 'required_with:pre_tests|string',
            'pre_tests.*.c' => 'required_with:pre_tests|string',
            'pre_tests.*.d' => 'required_with:pre_tests|string',
            'pre_tests.*.jawaban' => 'required_with:pre_tests|in:a,b,c,d',
        ]);

        try {
            return DB::transaction(function () use ($request, $modul_id) {
                $materi = new Materi();
                $materi->modul_id = $modul_id;
                $materi->judul = $request->judul;
                $materi->deskripsi = $request->deskripsi;
                $materi->tipe = $request->tipe;
                $materi->link_url = $request->link_url;
                $materi->durasi = $request->durasi;
                
                $materi->has_pretest = filter_var($request->has_pretest, FILTER_VALIDATE_BOOLEAN);
                $materi->has_posttest = filter_var($request->has_posttest, FILTER_VALIDATE_BOOLEAN);
                $materi->is_arabic = filter_var($request->is_arabic, FILTER_VALIDATE_BOOLEAN);

                if ($request->hasFile('file')) {
                    $path = $request->file('file')->store('materi', 'public');
                    $materi->file_path = $path;
                }

                $materi->save();



                if ($request->has('pre_tests')) {
                    foreach ($request->pre_tests as $index => $q) {
                        $soal = new MateriSoal([
                            'materi_id' => $materi->id,
                            'tipe_soal' => 'pre_test',
                            'soal' => $q['soal'],
                            'a' => $q['a'],
                            'b' => $q['b'],
                            'c' => $q['c'],
                            'd' => $q['d'],
                            'jawaban' => $q['jawaban'],
                            'media_type' => $q['media_type'] ?? 'None',
                            'video_link' => $q['video_link'] ?? null,
                        ]);

                        if ($request->hasFile("pre_tests.$index.video_file")) {
                            $soal->video_path = $request->file("pre_tests.$index.video_file")->store('materi/soal', 'public');
                        }
                        if ($request->hasFile("pre_tests.$index.pdf_file")) {
                            $soal->pdf_path = $request->file("pre_tests.$index.pdf_file")->store('materi/soal', 'public');
                        }

                        $soal->save();
                    }
                }

                if ($request->has('quizzes')) {
                    foreach ($request->quizzes as $index => $q) {
                        $soal = new MateriSoal([
                            'materi_id' => $materi->id,
                            'tipe_soal' => 'post_test',
                            'soal' => $q['soal'],
                            'a' => $q['a'],
                            'b' => $q['b'],
                            'c' => $q['c'],
                            'd' => $q['d'],
                            'jawaban' => $q['jawaban'],
                            'media_type' => $q['media_type'] ?? 'None',
                            'video_link' => $q['video_link'] ?? null,
                        ]);

                        if ($request->hasFile("quizzes.$index.video_file")) {
                            $soal->video_path = $request->file("quizzes.$index.video_file")->store('materi/soal', 'public');
                        }
                        if ($request->hasFile("quizzes.$index.pdf_file")) {
                            $soal->pdf_path = $request->file("quizzes.$index.pdf_file")->store('materi/soal', 'public');
                        }

                        $soal->save();
                    }
                }



                return response()->json([
                    'success' => true,
                    'message' => 'Materi berhasil ditambahkan',
                    'data' => $materi->load('soals')
                ], 201);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Materi store error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan materi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($modul_id, $id)
    {
        $materi = Materi::with('soals')->find($id);
        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $materi
        ]);
    }

    public function update(Request $request, $modul_id, $id)
    {
        $materi = Materi::find($id);
        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'judul' => 'required|string|max:200',
            'tipe' => 'required|in:dokumen,video,link,post_test,teks',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
            'link_url' => 'nullable|string|max:500',
            'durasi' => 'nullable|integer|min:0',
            'has_pretest' => 'nullable|boolean',
            'has_posttest' => 'nullable|boolean',
            'is_arabic' => 'nullable|boolean',
            'quizzes' => 'nullable|array',
            'quizzes.*.soal' => 'required_with:quizzes|string',
            'quizzes.*.a' => 'required_with:quizzes|string',
            'quizzes.*.b' => 'required_with:quizzes|string',
            'quizzes.*.c' => 'required_with:quizzes|string',
            'quizzes.*.d' => 'required_with:quizzes|string',
            'quizzes.*.jawaban' => 'required_with:quizzes|in:a,b,c,d',
            'pre_tests' => 'nullable|array',
            'pre_tests.*.soal' => 'required_with:pre_tests|string',
            'pre_tests.*.a' => 'required_with:pre_tests|string',
            'pre_tests.*.b' => 'required_with:pre_tests|string',
            'pre_tests.*.c' => 'required_with:pre_tests|string',
            'pre_tests.*.d' => 'required_with:pre_tests|string',
            'pre_tests.*.jawaban' => 'required_with:pre_tests|in:a,b,c,d',
        ]);

        try {
            return DB::transaction(function () use ($request, $materi) {
                $materi->judul = $request->judul;
                $materi->deskripsi = $request->deskripsi;
                $materi->tipe = $request->tipe;
                $materi->link_url = $request->link_url;
                $materi->durasi = $request->durasi;
                
                $materi->has_pretest = filter_var($request->has_pretest, FILTER_VALIDATE_BOOLEAN);
                $materi->has_posttest = filter_var($request->has_posttest, FILTER_VALIDATE_BOOLEAN);
                $materi->is_arabic = filter_var($request->is_arabic, FILTER_VALIDATE_BOOLEAN);

                if ($request->hasFile('file')) {
                    if ($materi->file_path) {
                        Storage::disk('public')->delete($materi->file_path);
                    }
                    $path = $request->file('file')->store('materi', 'public');
                    $materi->file_path = $path;
                }

                $materi->save();

                if ($request->has('quizzes') && filter_var($request->has_posttest, FILTER_VALIDATE_BOOLEAN)) {
                    // Delete old media files for post_test questions
                    $oldSoals = $materi->soals()->where('tipe_soal', 'post_test')->get();
                    foreach ($oldSoals as $oldSoal) {
                        if ($oldSoal->video_path) Storage::disk('public')->delete($oldSoal->video_path);
                        if ($oldSoal->pdf_path) Storage::disk('public')->delete($oldSoal->pdf_path);
                    }
                    $materi->soals()->where('tipe_soal', 'post_test')->delete();

                    foreach ($request->quizzes as $index => $q) {
                        $soal = new MateriSoal([
                            'materi_id' => $materi->id,
                            'tipe_soal' => 'post_test',
                            'soal' => $q['soal'],
                            'a' => $q['a'],
                            'b' => $q['b'],
                            'c' => $q['c'],
                            'd' => $q['d'],
                            'jawaban' => $q['jawaban'],
                            'media_type' => $q['media_type'] ?? 'None',
                            'video_link' => $q['video_link'] ?? null,
                        ]);

                        if ($request->hasFile("quizzes.$index.video_file")) {
                            $soal->video_path = $request->file("quizzes.$index.video_file")->store('materi/soal', 'public');
                        }
                        if ($request->hasFile("quizzes.$index.pdf_file")) {
                            $soal->pdf_path = $request->file("quizzes.$index.pdf_file")->store('materi/soal', 'public');
                        }

                        $soal->save();
                    }
                } else if (!filter_var($request->has_posttest, FILTER_VALIDATE_BOOLEAN)) {
                    // If posttest is set to false/0, delete any existing post-test questions
                    $oldSoals = $materi->soals()->where('tipe_soal', 'post_test')->get();
                    foreach ($oldSoals as $oldSoal) {
                        if ($oldSoal->video_path) Storage::disk('public')->delete($oldSoal->video_path);
                        if ($oldSoal->pdf_path) Storage::disk('public')->delete($oldSoal->pdf_path);
                    }
                    $materi->soals()->where('tipe_soal', 'post_test')->delete();
                }

                if ($request->has('pre_tests') && filter_var($request->has_pretest, FILTER_VALIDATE_BOOLEAN)) {
                    $oldPreTests = $materi->soals()->where('tipe_soal', 'pre_test')->get();
                    foreach ($oldPreTests as $oldSoal) {
                        if ($oldSoal->video_path) Storage::disk('public')->delete($oldSoal->video_path);
                        if ($oldSoal->pdf_path) Storage::disk('public')->delete($oldSoal->pdf_path);
                    }
                    $materi->soals()->where('tipe_soal', 'pre_test')->delete();

                    foreach ($request->pre_tests as $index => $q) {
                        $soal = new MateriSoal([
                            'materi_id' => $materi->id,
                            'tipe_soal' => 'pre_test',
                            'soal' => $q['soal'],
                            'a' => $q['a'],
                            'b' => $q['b'],
                            'c' => $q['c'],
                            'd' => $q['d'],
                            'jawaban' => $q['jawaban'],
                            'media_type' => $q['media_type'] ?? 'None',
                            'video_link' => $q['video_link'] ?? null,
                        ]);

                        if ($request->hasFile("pre_tests.$index.video_file")) {
                            $soal->video_path = $request->file("pre_tests.$index.video_file")->store('materi/soal', 'public');
                        }
                        if ($request->hasFile("pre_tests.$index.pdf_file")) {
                            $soal->pdf_path = $request->file("pre_tests.$index.pdf_file")->store('materi/soal', 'public');
                        }

                        $soal->save();
                    }
                } else if (!filter_var($request->has_pretest, FILTER_VALIDATE_BOOLEAN)) {
                    // If pretest is set to false/0, delete any existing pre-test questions
                    $oldPreTests = $materi->soals()->where('tipe_soal', 'pre_test')->get();
                    foreach ($oldPreTests as $oldSoal) {
                        if ($oldSoal->video_path) Storage::disk('public')->delete($oldSoal->video_path);
                        if ($oldSoal->pdf_path) Storage::disk('public')->delete($oldSoal->pdf_path);
                    }
                    $materi->soals()->where('tipe_soal', 'pre_test')->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Materi berhasil diperbarui',
                    'data' => $materi->load('soals')
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui materi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($modul_id, $id)
    {
        $materi = Materi::find($id);
        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan'
            ], 404);
        }

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        // Delete question media
        foreach ($materi->soals as $soal) {
            if ($soal->video_path) Storage::disk('public')->delete($soal->video_path);
            if ($soal->pdf_path) Storage::disk('public')->delete($soal->pdf_path);
        }

        $materi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dihapus'
        ]);
    }

    public function getSubmissions($modul_id, $id)
    {
        $progresses = \App\Models\MateriProgress::with('user')
            ->where('materi_id', $id)
            ->where(function($query) {
                $query->where('is_resume_submitted', true)
                      ->orWhere('is_tugas_submitted', true);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progresses
        ]);
    }

    public function gradeSubmission(Request $request, $modul_id, $id, $progress_id)
    {
        $request->validate([
            'resume_score' => 'nullable|integer|min:0|max:100',
            'tugas_score' => 'nullable|integer|min:0|max:100'
        ]);

        $progress = \App\Models\MateriProgress::findOrFail($progress_id);

        if ($request->has('resume_score')) {
            $progress->resume_score = $request->resume_score;
        }

        if ($request->has('tugas_score')) {
            $progress->tugas_score = $request->tugas_score;
        }

        $progress->save();

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan',
            'data' => $progress
        ]);
    }
}
