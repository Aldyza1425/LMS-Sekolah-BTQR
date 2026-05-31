<?php

namespace App\Http\Controllers\Api\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Modul;

class ModulController extends Controller
{
    public function index(Request $request)
    {
        $moduls = Modul::withCount('materis')
            ->where('is_active', true)
            ->orderBy('urutan', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $moduls
        ]);
    }

    public function show($id)
    {
        $modul = Modul::with(['materis' => function($q) {
            $q->withCount('soals');
            $q->orderBy('urutan', 'asc');
        }])->where('id', $id)->orWhere('slug', $id)->first();

        if (!$modul) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $modul
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Store modul attempt', $request->all());
        
        try {
            $request->validate([
                'judul' => 'required|string|max:200',
                'arabic_title' => 'nullable|string|max:200',
                'deskripsi' => 'nullable|string',
                'mata_pelajaran' => 'required|string|max:100',
                'level' => 'required|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            throw $e;
        }

        try {
            $modul = new Modul();
            $modul->judul = $request->judul;
            $modul->arabic_title = $request->arabic_title;
            $modul->deskripsi = $request->deskripsi;
            $modul->mata_pelajaran = $request->mata_pelajaran;
            $modul->level = $request->level;
            $modul->created_by = $request->user()->id;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('moduls', 'public');
                $modul->thumbnail = $path;
            }

            $modul->save();

            return response()->json([
                'success' => true,
                'message' => 'Modul berhasil ditambahkan',
                'data' => $modul
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Modul save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $modul = Modul::find($id);
        if (!$modul) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan'
            ], 404);
        }

        $modul->materis()->delete();
        $modul->delete();

        return response()->json([
            'success' => true,
            'message' => 'Modul berhasil dihapus'
        ]);
    }
}
