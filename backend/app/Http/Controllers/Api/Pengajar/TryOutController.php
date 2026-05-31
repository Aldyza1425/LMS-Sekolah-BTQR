<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TryOut;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TryOutController extends Controller
{
    public function index()
    {
        $tryouts = TryOut::with('creator:id,name')->withCount('soals')->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $tryouts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'mulai_at' => 'required|date',
            'durasi_menit' => 'required|integer|min:1',
            'nilai_lulus' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mulaiAt = Carbon::parse($request->mulai_at);
        // Default jam mulai 08:00 jika hanya tanggal yang dikirim
        if ($mulaiAt->hour == 0 && $mulaiAt->minute == 0) {
            $mulaiAt->setHour(8);
        }

        $createdBy = $request->user()->id;

        $tryout = TryOut::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'mulai_at' => $mulaiAt,
            'selesai_at' => $mulaiAt->copy()->addDay(), // Aktif 24 jam default
            'durasi_menit' => $request->durasi_menit,
            'nilai_lulus' => $request->nilai_lulus ?? 70.00,
            'is_active' => true,
            'created_by' => $createdBy,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Try Out berhasil dibuat',
            'data' => $tryout
        ], 201);
    }

    public function show($id)
    {
        $tryout = TryOut::with(['creator:id,name', 'soals'])->findOrFail($id);

        $stats = [
            'total_soal' => $tryout->soals->count(),
            'sudah_mengerjakan' => \App\Models\TryOutHasil::where('try_out_id', $id)->count(),
            'durasi_menit' => $tryout->durasi_menit,
            'nilai_tertinggi' => null
        ];

        $highest = \App\Models\TryOutHasil::where('try_out_id', $id)
            ->with('user:id,name')
            ->orderBy('nilai', 'desc')
            ->first();

        if ($highest && $highest->user) {
            $stats['nilai_tertinggi'] = [
                'nama' => $highest->user->name,
                'nilai' => (float)$highest->nilai
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $tryout,
            'stats' => $stats
        ]);
    }

    public function destroy($id)
    {
        $tryout = TryOut::findOrFail($id);
        $tryout->delete();
        return response()->json([
            'success' => true,
            'message' => 'Try Out berhasil dihapus'
        ]);
    }
}
