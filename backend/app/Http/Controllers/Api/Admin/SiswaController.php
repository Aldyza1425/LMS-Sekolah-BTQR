<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::orderBy('id', 'asc')
            ->paginate(10);
            
        return response()->json($siswa);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:siswas',
            'phone' => 'nullable|string|max:20',
            'domisili' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'required|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'domisili' => $request->domisili,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('siswa/photos', 'public');
            $data['photo'] = $path;
        }

        $user = Siswa::create($data);

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan!',
            'user' => $user
        ], 201);
    }

    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:siswas,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'domisili' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8',
        ]);

        $siswa->name = $request->name;
        $siswa->email = $request->email;
        $siswa->phone = $request->phone;
        $siswa->domisili = $request->domisili;

        if ($request->hasFile('photo')) {
            if ($siswa->photo) {
                Storage::disk('public')->delete($siswa->photo);
            }
            $path = $request->file('photo')->store('siswa/photos', 'public');
            $siswa->photo = $path;
        }

        if ($request->filled('password')) {
            $siswa->password = Hash::make($request->password);
        }

        $siswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui!',
            'data' => $siswa
        ]);
    }

    public function verify($id)
    {
        $user = Siswa::findOrFail($id);
        $user->is_active = true;
        $user->save();

        return response()->json([
            'message' => 'Siswa berhasil diverifikasi!',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = Siswa::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Siswa berhasil dihapus!'
        ]);
    }
}
