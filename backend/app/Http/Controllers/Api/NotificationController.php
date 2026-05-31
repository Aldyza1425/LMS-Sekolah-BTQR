<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $class = get_class($user);
        
        $role = 'siswa';
        if (str_contains($class, 'Admin')) {
            $role = 'admin';
        } elseif (str_contains($class, 'Pengajar')) {
            $role = 'pengajar';
        }

        // Auto-seed some realistic notifications for development if empty
        if (Notification::count() === 0) {
            $this->seedDemoNotifications();
        }

        $notifications = Notification::where(function($query) use ($user, $role) {
            $query->where('role', $role)
                  ->orWhere('user_id', $user->id);
        })
        ->latest()
        ->take(15)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    private function seedDemoNotifications()
    {
        // Admin Notifications
        Notification::create([
            'role' => 'admin',
            'title' => 'Verifikasi Santri Baru',
            'message' => 'Santri baru bernama "Ahmad Fikri" telah mendaftar dan menunggu verifikasi akun.',
            'is_read' => false
        ]);
        Notification::create([
            'role' => 'admin',
            'title' => 'Pendaftaran Pengajar',
            'message' => 'Ustadzah Sarah telah melengkapi berkas profil pengajar baru.',
            'is_read' => false
        ]);

        // Pengajar Notifications
        Notification::create([
            'role' => 'pengajar',
            'title' => 'Tugas Perlu Dinilai',
            'message' => 'Siswa "Rizqi Pratama" telah mengirimkan jawaban tugas untuk Modul 1 (Tajwid).',
            'is_read' => false
        ]);
        Notification::create([
            'role' => 'pengajar',
            'title' => 'Permintaan Ujian Ulang',
            'message' => 'Siswa "Zaskia Nur" meminta izin mengulang (retake) Try Out Akbar 2.',
            'is_read' => false
        ]);

        // Siswa Notifications
        Notification::create([
            'role' => 'siswa',
            'title' => 'Try Out Baru Tersedia',
            'message' => 'Try Out Syarat Kelulusan Jilid 3 telah dibuka oleh Ustadz Faqih.',
            'is_read' => false
        ]);
        Notification::create([
            'role' => 'siswa',
            'title' => 'Tugas Selesai Dinilai',
            'message' => 'Tugas Modul Hifzhul Quran Anda telah dinilai. Skor: 95/100.',
            'is_read' => true
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $class = get_class($user);
        
        $role = 'siswa';
        if (str_contains($class, 'Admin')) {
            $role = 'admin';
        } elseif (str_contains($class, 'Pengajar')) {
            $role = 'pengajar';
        }

        $notification = Notification::where('id', $id)
            ->where(function($query) use ($user, $role) {
                $query->where('role', $role)
                      ->orWhere('user_id', $user->id);
            })
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai telah dibaca'
        ]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $class = get_class($user);
        
        $role = 'siswa';
        if (str_contains($class, 'Admin')) {
            $role = 'admin';
        } elseif (str_contains($class, 'Pengajar')) {
            $role = 'pengajar';
        }

        Notification::where('is_read', false)
            ->where(function($query) use ($user, $role) {
                $query->where('role', $role)
                      ->orWhere('user_id', $user->id);
            })
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai telah dibaca'
        ]);
    }
}
