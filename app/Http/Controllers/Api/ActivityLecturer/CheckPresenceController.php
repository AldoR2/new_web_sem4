<?php

namespace App\Http\Controllers\Api\ActivityLecturer;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\FcmToken;
use App\Models\Mahasiswa;
use App\Models\Notification;
use App\Models\Presensi;
use App\Services\FcmV1Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckPresenceController extends Controller
{
    public function notifyUpcomingPresensi()
    {
        $now = Carbon::now('Asia/Jakarta');

        // Ambil presensi yang jam_awal-nya kurang dari 1 jam lagi dari sekarang dan belum dimulai
        $presensis = Presensi::whereDate('tgl_presensi', $now->toDateString())
            ->whereTime('jam_awal', '>', $now->format('H:i:s'))
            ->whereTime('jam_awal', '<=', $now->copy()->addHour()->format('H:i:s'))
            ->with('detailPresensis.mahasiswa.user') // pastikan relasi ini ada
            ->get();

        $fcmService = new FcmV1Service();

        foreach ($presensis as $presensi) {
            foreach ($presensi->detailPresensis as $detail) {
                $user = $detail->mahasiswa->user ?? null;

                if ($user) {
                    $tokens = FcmToken::where('user_id', $user->id)->pluck('token');
                    foreach ($tokens as $token) {
                        $fcmService->send(
                            $token,
                            'Presensi akan dimulai',
                            'Presensi Anda akan dimulai pada pukul ' . Carbon::parse($presensi->jam_awal)->format('H:i')
                        );
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi dikirim ke mahasiswa dengan presensi < 1 jam.',
        ]);
    }
    public function checkRecentNotificationByDosenId(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|integer|exists:dosens,id',
        ]);

        $dosen = \App\Models\Dosen::with('user')->findOrFail($request->dosen_id);
        $userId = $dosen->user_id;

        $now = now()->timezone('Asia/Jakarta');
        $oneHourAgo = $now->copy()->subHour();

        $hasRecent = \App\Models\Notification::where('user_id', $userId)
            ->whereBetween('created_at', [$oneHourAgo, $now])
            ->exists();

        return response()->json([
            'hasNotification' => $hasRecent,
        ]);
    }


    public function checkRecentNotificationByMahasiswaId(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|integer|exists:mahasiswas,id',
        ]);

        $mahasiswa = \App\Models\Mahasiswa::with('user')->findOrFail($request->mahasiswa_id);
        $userId = $mahasiswa->user_id;

        $now = now()->timezone('Asia/Jakarta');
        $oneHourAgo = $now->copy()->subHour();

        $hasRecent = \App\Models\Notification::where('user_id', $userId)
            ->whereBetween('created_at', [$oneHourAgo, $now])
            ->exists();

        return response()->json([
            'hasNotification' => $hasRecent,
        ]);
    }


    public function checkPresenceEdit(Request $request)
    {
        $request->validate([
            'presensis_id' => 'required|integer',
            'dosen_id' => 'required',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
        ]);

        $presensi = Presensi::find($request->presensis_id);

        if (!$presensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Presensi tidak ditemukan',
            ], 404);
        }

        $conflict = Presensi::where('prodi_id', $presensi->prodi_id)
            ->where('semester', $presensi->semester)
            ->where('tahun_ajaran_id', $presensi->tahun_ajaran_id)
            ->where('tgl_presensi', $presensi->tgl_presensi)
            ->where('id', '!=', $presensi->id)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // Kasus 1: Jam baru dimulai selama jam yang ada
                    $q->where('jam_awal', '<', $request->jam_akhir)
                        ->where('jam_akhir', '>', $request->jam_awal);
                })->orWhere(function ($q) use ($request) {
                    // Kasus 2: Jam baru mencakup seluruh jam yang ada
                    $q->where('jam_awal', '>=', $request->jam_awal)
                        ->where('jam_akhir', '<=', $request->jam_akhir);
                })->orWhere(function ($q) use ($request) {
                    // Kasus 3: Jam baru dimulai sebelum dan berakhir selama jam yang ada
                    $q->where('jam_awal', '<', $request->jam_awal)
                        ->where('jam_akhir', '>', $request->jam_awal);
                })->orWhere(function ($q) use ($request) {
                    // Kasus 4: Jam baru dimulai selama dan berakhir setelah jam yang ada
                    $q->where('jam_awal', '<', $request->jam_akhir)
                        ->where('jam_akhir', '>', $request->jam_akhir);
                });
            })
            ->first();

        if ($conflict) {
            // Ambil data dosen & user
            $dosen = Dosen::with('user')->findOrFail($request->dosen_id);
            $user = $dosen->user;
            $matkul = $presensi->matkul;

            $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
            $tanggal = $waktu->translatedFormat('d F Y');
            $jam = $waktu->format('H.i');

            $tgl_presensi = Carbon::parse($conflict->tgl_presensi)->locale('id')->translatedFormat('d F Y');

            $message = "Presensi Anda gagal ditambahkan karena bentrok dengan jadwal lain pada " .
                Carbon::parse($conflict->jam_awal)->format('H:i') . " - " .
                Carbon::parse($conflict->jam_akhir)->format('H:i') . " di tanggal $tgl_presensi.";

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Presensi Gagal Ditambahkan!',
                'message' => $message,
                'type' => 'presensiGagal',
                'nama_user' => $dosen->nama,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'mata_kuliah' => $matkul?->nama_matkul ?? '-',
            ]);

            $fcmService = new FcmV1Service();

            // Kirim notifikasi ke dosen
            $dosenUserId = $dosen->user_id;
            $dosenTokens = FcmToken::where('user_id', $dosenUserId)->pluck('token');

            foreach ($dosenTokens as $token) {
                $fcmService->send(
                    $token,
                    'Presensi Gagal Ditambahkan',
                    'Presensi Anda gagal ditambahkan karena bentrok dengan jadwal lain.'
                );
            }
            return response()->json([
                'status' => 'conflict',
                'message' => 'Data presensi bentrok',
                'data' => [
                    'tanggal_presensi' => $conflict->tgl_presensi,
                    'durasi_presensi' => Carbon::parse($conflict->jam_awal)->format('H:i') . ' - ' . Carbon::parse($conflict->jam_akhir)->format('H:i'),
                ]
            ], 409);
        }

        return response()->json([
            'status' => 'no_conflict',
            'message' => 'Tidak terjadi konflik data',
        ], 200);

    }
    public function checkPresenceUpload(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
            'tgl_presensi' => 'required|date',
            'prodi_id' => 'required|integer',
            'semester' => 'required|integer',
        ]);

        $conflict = Presensi::where('prodi_id', $request->prodi_id)
            ->where('semester', $request->semester)
            ->where('tgl_presensi', $request->tgl_presensi)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('jam_awal', '<', $request->jam_akhir)
                        ->where('jam_akhir', '>', $request->jam_awal);
                })->orWhere(function ($q) use ($request) {
                    $q->where('jam_awal', '<', $request->jam_awal)
                        ->where('jam_akhir', '>', $request->jam_awal);
                });
            })->first();

        if ($conflict) {
            // Ambil data dosen & user
            $dosen = Dosen::with('user')->findOrFail($request->dosen_id);
            $user = $dosen->user;
            $matkul = $conflict->matkul ?? null;

            $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
            $tanggal = $waktu->translatedFormat('d F Y');
            $jam = $waktu->format('H.i');

            $tgl_presensi = Carbon::parse($conflict->tgl_presensi)->locale('id')->translatedFormat('d F Y');

            $message = "Presensi Anda gagal ditambahkan karena bentrok dengan jadwal lain pada " .
                Carbon::parse($conflict->jam_awal)->format('H:i') . " - " .
                Carbon::parse($conflict->jam_akhir)->format('H:i') . " di tanggal $tgl_presensi.";

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Presensi Gagal Ditambahkan!',
                'message' => $message,
                'type' => 'presensiGagal',
                'nama_user' => $dosen->nama,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'mata_kuliah' => $matkul?->nama_matkul ?? '-',
            ]);

            $fcmService = new FcmV1Service();

            // Kirim notifikasi ke dosen
            $dosenUserId = $dosen->user_id;
            $dosenTokens = FcmToken::where('user_id', $dosenUserId)->pluck('token');

            foreach ($dosenTokens as $token) {
                $fcmService->send(
                    $token,
                    'Presensi Gagal Ditambahkan',
                    'Presensi Anda gagal ditambahkan karena bentrok dengan jadwal lain.'
                );
            }

            return response()->json([
                'status' => 'conflict',
                'message' => 'Data presensi bentrok',
                'data' => [
                    'tanggal_presensi' => $conflict->tgl_presensi,
                    'durasi_presensi' => Carbon::parse($conflict->jam_awal)->format('H:i') . ' - ' . Carbon::parse($conflict->jam_akhir)->format('H:i'),
                ]
            ], 409);
        }

        return response()->json([
            'status' => 'no_conflict',
            'message' => 'Tidak terjadi konflik data',
        ], 200);
    }
}
