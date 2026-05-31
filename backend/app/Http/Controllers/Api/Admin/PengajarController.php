<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PengajarController extends Controller
{
    public function index()
    {
        $pengajar = Pengajar::latest()
            ->paginate(10);
            
        return response()->json($pengajar);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengajars,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pengajar/photos', 'public');
        }

        $pengajar = Pengajar::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'photo' => $photoPath,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Pengajar berhasil dibuat!',
            'user' => $pengajar
        ], 201);
    }

    public function show($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $pengajar
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengajar = Pengajar::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengajars,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image|max:2048',
        ]);

        $pengajar->name = $request->name;
        $pengajar->email = $request->email;
        $pengajar->phone = $request->phone;

        if ($request->filled('password')) {
            $pengajar->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($pengajar->photo && Storage::disk('public')->exists($pengajar->photo)) {
                Storage::disk('public')->delete($pengajar->photo);
            }
            $path = $request->file('photo')->store('pengajar/photos', 'public');
            $pengajar->photo = $path;
        }

        $pengajar->save();

        return response()->json([
            'success' => true,
            'message' => 'Data pengajar berhasil diperbarui!',
            'data' => $pengajar
        ]);
    }

    public function verify($id)
    {
        $user = Pengajar::findOrFail($id);
        $user->is_active = true;
        $user->save();

        return response()->json([
            'message' => 'Pengajar berhasil diverifikasi!',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = Pengajar::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Pengajar berhasil dihapus!'
        ]);
    }
}
