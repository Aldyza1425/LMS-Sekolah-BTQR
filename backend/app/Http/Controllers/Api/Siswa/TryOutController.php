<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TryOut;

class TryOutController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $tryouts = TryOut::where('is_active', true)
            ->withCount('soals')
            ->orderBy('id', 'desc')
            ->get();

        $now = now();

        $tryouts = $tryouts->map(function($tryout) use ($userId, $now) {
            $hasil = \App\Models\TryOutHasil::where('try_out_id', $tryout->id)
                ->where('user_id', $userId)
                ->first();
                
            $tryout->sudah_dikerjakan = $hasil ? true : false;
            $tryout->retake_status = $hasil ? $hasil->retake_status : 'none';

            // Hitung status jadwal
            if ($tryout->mulai_at && $now->lt($tryout->mulai_at)) {
                $tryout->status_jadwal = 'terjadwal';
            } elseif ($tryout->selesai_at && $now->gt($tryout->selesai_at)) {
                $tryout->status_jadwal = 'kedaluwarsa';
            } else {
                $tryout->status_jadwal = 'tersedia';
            }

            return $tryout;
        });
        
        return response()->json([
            'success' => true,
            'data' => $tryouts
        ]);
    }

    public function requestRetake(Request $request, $id)
    {
        $userId = $request->user()->id;

        $hasil = \App\Models\TryOutHasil::where('try_out_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($hasil) {
            $hasil->update(['retake_status' => 'requested']);
            return response()->json(['success' => true, 'message' => 'Permintaan ujian ulang berhasil dikirim.']);
        }

        return response()->json(['success' => false, 'message' => 'Riwayat ujian tidak ditemukan.'], 404);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $tryout = TryOut::where('is_active', true)->with('soals')->findOrFail($id);

        // Validasi waktu ujian
        $now = now();
        if ($tryout->mulai_at && $now->lt($tryout->mulai_at)) {
            $tanggalMulai = \Carbon\Carbon::parse($tryout->mulai_at)->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm');
            return response()->json([
                'success' => false,
                'message' => "Ujian belum dimulai. Ujian dijadwalkan pada {$tanggalMulai}."
            ], 403);
        }

        if ($tryout->selesai_at && $now->gt($tryout->selesai_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu pelaksanaan ujian ini telah berakhir.'
            ], 403);
        }

        $hasil = \App\Models\TryOutHasil::where('try_out_id', $tryout->id)
            ->where('user_id', $userId)
            ->first();

        if ($hasil) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah mengerjakan ujian ini. Menunggu persetujuan pengajar jika ingin mengulang.'
            ], 403);
        }

        $soals = $tryout->soals->map(function ($soal) {
            return [
                'id' => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'gambar' => $soal->gambar,
                'tipe' => $soal->tipe,
                'pilihan_a' => $soal->pilihan_a,
                'pilihan_b' => $soal->pilihan_b,
                'pilihan_c' => $soal->pilihan_c,
                'pilihan_d' => $soal->pilihan_d,
                'urutan' => $soal->urutan
                // Perhatikan: kunci jawaban dan bobot tidak dikirim ke klien
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tryout' => $tryout->only(['id', 'judul', 'deskripsi', 'durasi_menit']),
                'soals' => $soals
            ]
        ]);
    }

    public function submit(Request $request, $id)
    {
        $tryout = TryOut::where('is_active', true)->findOrFail($id);
        $userId = $request->user()->id;

        $answers = $request->input('answers'); // Format: [{soal_id: 1, jawaban: 'a'}, ...]
        
        $totalSkor = 0;
        $soals = $tryout->soals->keyBy('id');

        // Simpan jawaban
        foreach ($answers as $ans) {
            $soal = $soals->get($ans['soal_id']);
            if ($soal) {
                $isBenar = strtolower($soal->jawaban) === strtolower($ans['jawaban']);
                if ($isBenar) {
                    $totalSkor += $soal->bobot;
                }

                \App\Models\TryOutJawaban::updateOrCreate(
                    [
                        'try_out_id' => $tryout->id,
                        'user_id' => $userId,
                        'soal_id' => $soal->id,
                    ],
                    [
                        'jawaban' => $ans['jawaban'] ?? '-',
                        'is_benar' => $isBenar,
                        'submitted_at' => now(),
                    ]
                );
            }
        }

        // Cek lulus / tidak lulus (Misalnya passing grade 70)
        $isLulus = $totalSkor >= 70;

        // Simpan hasil tryout
        \App\Models\TryOutHasil::updateOrCreate(
            [
                'try_out_id' => $tryout->id,
                'user_id' => $userId,
            ],
            [
                'nilai' => $totalSkor,
                'lulus' => $isLulus,
                'selesai_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil diselesaikan.',
            'data' => [
                'nilai' => $totalSkor,
                'lulus' => $isLulus
            ]
        ]);
    }

    public function grades(Request $request)
    {
        $userId = $request->user()->id;

        $grades = \App\Models\TryOutHasil::where('user_id', $userId)
            ->with('tryout')
            ->orderBy('selesai_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades
        ]);
    }

    public function hasil(Request $request, $id)
    {
        $userId = $request->user()->id;

        $tryout = TryOut::findOrFail($id);
        
        $hasil = \App\Models\TryOutHasil::where('try_out_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$hasil) {
            return response()->json(['success' => false, 'message' => 'Belum ada hasil'], 404);
        }

        $jawabans = \App\Models\TryOutJawaban::where('try_out_id', $id)
            ->where('user_id', $userId)
            ->get()->keyBy('soal_id');

        $soals = $tryout->soals->map(function ($soal) use ($jawabans) {
            $jawabanUser = $jawabans->get($soal->id);
            return [
                'soal_id' => $soal->id,
                'soal' => $soal->toArray(),
                'user_answer' => $jawabanUser ? $jawabanUser->jawaban : null,
                'is_correct' => $jawabanUser ? $jawabanUser->is_benar : false,
                'correct_answer' => $soal->jawaban
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'score' => $hasil->nilai,
                'passed' => $hasil->lulus,
                'created_at' => $hasil->selesai_at,
                'question_results' => $soals,
            ]
        ]);
    }
}
