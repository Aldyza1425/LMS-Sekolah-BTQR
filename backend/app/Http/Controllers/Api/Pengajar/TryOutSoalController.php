<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TryOut;
use App\Models\TryOutSoal;
use Illuminate\Support\Facades\Validator;

class TryOutSoalController extends Controller
{
    public function store(Request $request, $id)
    {
        $tryout = TryOut::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|string',
            'tipe' => 'required|in:pilihan_ganda,essay',
            'pilihan_a' => 'nullable|string',
            'pilihan_b' => 'nullable|string',
            'pilihan_c' => 'nullable|string',
            'pilihan_d' => 'nullable|string',
            'jawaban' => 'required|string|size:1',
            'bobot' => 'nullable|integer|min:1',
            'urutan' => 'nullable|integer',
            'is_arabic' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $urutan = $request->urutan ?? ($tryout->soals()->max('urutan') + 1);

        $soal = TryOutSoal::create([
            'try_out_id' => $tryout->id,
            'pertanyaan' => $request->pertanyaan,
            'gambar' => $request->gambar,
            'tipe' => $request->tipe,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'jawaban' => strtolower($request->jawaban),
            'bobot' => $request->bobot ?? 1,
            'urutan' => $urutan,
            'is_arabic' => filter_var($request->is_arabic, FILTER_VALIDATE_BOOLEAN)
        ]);

        $this->recalculateBobot($id);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil ditambahkan',
            'data' => $soal
        ], 201);
    }

    public function storeBatch(Request $request, $id)
    {
        $tryout = TryOut::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'soals' => 'required|array|min:1',
            'soals.*.pertanyaan' => 'required|string',
            'soals.*.gambar' => 'nullable|string',
            'soals.*.tipe' => 'required|in:pilihan_ganda,essay',
            'soals.*.pilihan_a' => 'nullable|string',
            'soals.*.pilihan_b' => 'nullable|string',
            'soals.*.pilihan_c' => 'nullable|string',
            'soals.*.pilihan_d' => 'nullable|string',
            'soals.*.jawaban' => 'required|string|size:1',
            'soals.*.is_arabic' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $urutanMaks = $tryout->soals()->max('urutan') ?? 0;

        foreach ($request->soals as $index => $item) {
            TryOutSoal::create([
                'try_out_id' => $tryout->id,
                'pertanyaan' => $item['pertanyaan'],
                'gambar' => $item['gambar'] ?? null,
                'tipe' => $item['tipe'],
                'pilihan_a' => $item['pilihan_a'],
                'pilihan_b' => $item['pilihan_b'],
                'pilihan_c' => $item['pilihan_c'],
                'pilihan_d' => $item['pilihan_d'],
                'jawaban' => strtolower($item['jawaban']),
                'bobot' => 1,
                'urutan' => $urutanMaks + $index + 1,
                'is_arabic' => filter_var($item['is_arabic'] ?? false, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        $this->recalculateBobot($id);

        return response()->json([
            'success' => true,
            'message' => 'Semua soal berhasil ditambahkan'
        ], 201);
    }

    public function update(Request $request, $id, $soalId)
    {
        $tryout = TryOut::findOrFail($id);
        $soal = TryOutSoal::where('try_out_id', $tryout->id)->findOrFail($soalId);

        $validator = Validator::make($request->all(), [
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|string',
            'tipe' => 'required|in:pilihan_ganda,essay',
            'pilihan_a' => 'nullable|string',
            'pilihan_b' => 'nullable|string',
            'pilihan_c' => 'nullable|string',
            'pilihan_d' => 'nullable|string',
            'jawaban' => 'required|string|size:1',
            'bobot' => 'nullable|integer|min:1',
            'urutan' => 'nullable|integer',
            'is_arabic' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $soal->update([
            'pertanyaan' => $request->pertanyaan,
            'gambar' => $request->gambar,
            'tipe' => $request->tipe,
            'pilihan_a' => $request->pilihan_a,
            'pilihan_b' => $request->pilihan_b,
            'pilihan_c' => $request->pilihan_c,
            'pilihan_d' => $request->pilihan_d,
            'jawaban' => strtolower($request->jawaban),
            'bobot' => $request->bobot ?? $soal->bobot,
            'urutan' => $request->urutan ?? $soal->urutan,
            'is_arabic' => filter_var($request->is_arabic ?? $soal->is_arabic, FILTER_VALIDATE_BOOLEAN)
        ]);

        $this->recalculateBobot($id);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diperbarui',
            'data' => $soal
        ]);
    }

    public function destroy($id, $soalId)
    {
        $soal = TryOutSoal::where('try_out_id', $id)->findOrFail($soalId);
        $soal->delete();

        $this->recalculateBobot($id);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil dihapus'
        ]);
    }

    private function recalculateBobot($tryoutId)
    {
        $soals = TryOutSoal::where('try_out_id', $tryoutId)->orderBy('id', 'asc')->get();
        $count = $soals->count();
        
        if ($count > 0) {
            $baseBobot = floor(100 / $count);
            $remainder = 100 % $count;

            foreach ($soals as $index => $soal) {
                // Berikan sisa ke soal-soal pertama agar total tepat 100
                $bobot = $baseBobot + ($index < $remainder ? 1 : 0);
                $soal->update(['bobot' => $bobot]);
            }
        }
    }
}
