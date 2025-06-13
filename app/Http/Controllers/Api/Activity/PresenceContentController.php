<?php

namespace App\Http\Controllers\Api\activity;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\FcmToken;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Notification;
use App\Services\FcmV1Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PresenceContentController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'presensi_id' => 'required|exists:presensis,id',
            'status' => 'required|in:1,2,3,4', // sesuaikan range status valid
            'waktu_presensi' => 'required|date',
            'alasan' => 'nullable|string|max:255',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120', // max:5120 KB = 5 MB
        ]);

        // Persiapkan data untuk update
        $data = [
            'status' => $request->status,
            'alasan' => $request->alasan,
            'waktu_presensi' => $request->waktu_presensi,
        ];

        // Handle file upload jika ada
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = 'bukti-' . $request->mahasiswa_id . '-' . now()->format('Ymd') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Simpan ke storage/app/public/bukti
            $file->storeAs('bukti', $filename, 'public');
            // Simpan path relatif ke database
            $data['bukti'] = 'bukti/' . $filename;
        }

        // Jalankan update data
        $updated = DB::table('detail_presensis')
            ->where('presensi_id', $request->presensi_id)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->update($data);

        // Ambil data mahasiswa & user
        $mahasiswa = Mahasiswa::with('user')->findOrFail($request->mahasiswa_id);
        $user = $mahasiswa->user;
        $matkul = Matkul::whereHas('presensi', function ($q) use ($request) {
            $q->where('id', $request->presensi_id);
        })->first();

        $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
        $tanggal = $waktu->translatedFormat('d F Y');
        $jam = $waktu->format('H.i');

        // Simpan notifikasi
        Notification::create([
            'user_id' => $user->id,
            'title' => $updated ? 'Presensi Berhasil Ditambahkan!' : 'Presensi Gagal Ditambahkan!',
            'message' => $updated ? 'Presensi Anda berhasil direkam.' : 'Presensi Anda gagal direkam.',
            'type' => $updated ? 'presensiBerhasil' : 'presensiGagal',
            'nama_user' => $mahasiswa->nama,
            'tanggal' => $tanggal,
            'jam' => $jam,
            'mata_kuliah' => $matkul?->nama_matkul ?? '-',
        ]);

        $fcmService = new FcmV1Service();

        // Kirim notifikasi ke mahasiswa
        $mahasiswaUserId = Mahasiswa::find($request->mahasiswa_id)->user_id;
        $Tokens = FcmToken::where('user_id', $mahasiswaUserId)->pluck('token');
        $namaMatkul = $matkul?->nama_matkul ?? '-';

        foreach ($Tokens as $token) {
            $fcmService->send(
                $token,
                'Presensi Berhasil',
                'Presensi untuk matkul ' . $namaMatkul . ' sudah berhasil dilakukan.'
            );
        }

        // Kirim response
        return response()->json([
            'status' => $updated ? 'success' : 'error',
            'message' => $updated ? 'Absensi berhasil' : 'Data tidak ditemukan atau tidak ada perubahan',
        ], $updated ? 200 : 404);
    }
}
