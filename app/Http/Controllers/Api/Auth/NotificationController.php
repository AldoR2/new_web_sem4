<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getByMahasiswa($mahasiswaId)
    {
        $mahasiswa = Mahasiswa::with('user.notifications')->findOrFail($mahasiswaId);

        $notifications = $mahasiswa->user->notifications->map(function ($notif) {
            return [
                'title' => $notif->title,
                'message' => $notif->message,
                'time' => $notif->jam, // atau format waktu sebenarnya jika tersedia
                'type' => $notif->type,
                'nama_user' => $notif->nama_user,
                'tanggal' => $notif->tanggal,
                'jam' => $notif->jam,
                'mata_kuliah' => $notif->mata_kuliah,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data notifikasi berhasil ditampilkan',
            'data' => $notifications,
        ]);
    }

    public function getByDosen($dosenId)
    {
        $dosen = Dosen::with('user.notifications')->findOrFail($dosenId);

        $notifications = $dosen->user->notifications->map(function ($notif) {
            return [
                'title' => $notif->title,
                'message' => $notif->message,
                'time' => $notif->jam, // atau format waktu sebenarnya jika tersedia
                'type' => $notif->type,
                'nama_user' => $notif->nama_user,
                'tanggal' => $notif->tanggal,
                'jam' => $notif->jam,
                'mata_kuliah' => $notif->mata_kuliah,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data notifikasi berhasil ditampilkan',
            'data' => $notifications,
        ]);
    }
}
