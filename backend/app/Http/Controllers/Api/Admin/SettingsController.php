<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // ── Profil Lembaga ──────────────────────────────────────
    public function getLembaga()
    {
        $settings = [
            'nama'    => config('btqr.nama',    'BTQR - Baca Tulis Qur\'an & Relevansi'),
            'telepon' => config('btqr.telepon', '08123456789'),
            'email'   => config('btqr.email',   'info@btqr.ac.id'),
            'alamat'  => config('btqr.alamat',  'Jl. Pendidikan No. 1, Jakarta'),
        ];

        // Ambil dari DB jika sudah pernah disimpan
        $stored = \App\Models\Setting::whereIn('key', ['nama','telepon','email','alamat'])->pluck('value','key');
        foreach ($stored as $key => $val) {
            $settings[$key] = $val;
        }

        return response()->json($settings);
    }

    public function saveLembaga(Request $request)
    {
        $data = $request->validate([
            'nama'    => 'required|string|max:200',
            'telepon' => 'required|string|max:20',
            'email'   => 'required|email|max:100',
            'alamat'  => 'required|string|max:300',
        ]);

        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['message' => 'Profil lembaga berhasil disimpan.']);
    }

    // ── Pengaturan Sistem ────────────────────────────────────
    public function getSystem()
    {
        $passing = \App\Models\Setting::where('key', 'passing_grade')->value('value') ?? 70;

        return response()->json([
            'passing_grade' => (int) $passing,
        ]);
    }

    public function saveSystem(Request $request)
    {
        $data = $request->validate([
            'passing_grade' => 'required|integer|min:0|max:100',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'passing_grade'],
            ['value' => $data['passing_grade']]
        );

        return response()->json(['message' => 'Pengaturan sistem berhasil disimpan.']);
    }

    // ── Ganti Password Admin ─────────────────────────────────
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $admin = $request->user();

        if (!Hash::check($data['current_password'], $admin->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.'
            ], 422);
        }

        $admin->update(['password' => Hash::make($data['new_password'])]);

        return response()->json(['message' => 'Password berhasil diubah.']);
    }

    // ── Backup Database ──────────────────────────────────────
    public function backup(Request $request)
    {
        $type = $request->input('type', 'all'); // all | siswa | modul | try_out | nilai

        $timestamp = now()->format('Ymd_His');
        $filename  = "backup_{$type}_{$timestamp}.json";

        $data = match ($type) {
            'siswa'   => $this->backupSiswa(),
            'modul'   => $this->backupModul(),
            'try_out' => $this->backupTryOut(),
            'nilai'   => $this->backupNilai(),
            default   => $this->backupAll(),
        };

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function backupSiswa(): array
    {
        return [
            'type'        => 'Siswa',
            'exported_at' => now()->toIso8601String(),
            'data'        => \App\Models\Siswa::all()->toArray(),
        ];
    }

    private function backupModul(): array
    {
        return [
            'type'        => 'Modul & Materi',
            'exported_at' => now()->toIso8601String(),
            'data'        => \App\Models\Modul::with('materis')->get()->toArray(),
        ];
    }

    private function backupTryOut(): array
    {
        return [
            'type'        => 'Try Out & Soal',
            'exported_at' => now()->toIso8601String(),
            'data'        => \App\Models\TryOut::with('soals')->get()->toArray(),
        ];
    }

    private function backupNilai(): array
    {
        return [
            'type'          => 'Nilai & Hasil',
            'exported_at'   => now()->toIso8601String(),
            'try_out_hasil' => \App\Models\TryOutHasil::with(['user','tryOut'])->get()->toArray(),
        ];
    }

    private function backupAll(): array
    {
        return [
            'type'        => 'Full Backup',
            'exported_at' => now()->toIso8601String(),
            'siswa'       => \App\Models\Siswa::all()->toArray(),
            'modul'       => \App\Models\Modul::with('materis')->get()->toArray(),
            'try_out'     => \App\Models\TryOut::with('soals')->get()->toArray(),
            'nilai'       => \App\Models\TryOutHasil::with(['siswa','tryOut'])->get()->toArray(),
        ];
    }
}
