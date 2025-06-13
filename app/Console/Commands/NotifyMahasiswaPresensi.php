<?php

namespace App\Console\Commands;

use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\Presensi;
use App\Services\FcmV1Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyMahasiswaPresensi extends Command
{
    protected $signature = 'presensi:notify-mahasiswa';
    protected $description = 'Kirim notifikasi ke mahasiswa untuk presensi online yang akan dimulai atau segera berakhir';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $fcmService = new FcmV1Service();

        $this->notifyPresensiOnlineAkanDimulai($now, $fcmService);
        $this->notifyPresensiOnlineAkanBerakhir($now, $fcmService);

        $this->info('Notifikasi berhasil dikirim.');
    }

    protected function notifyPresensiOnlineAkanDimulai($now, $fcmService)
    {
        $presensis = Presensi::whereDate('tgl_presensi', $now->toDateString())
            ->whereNull('ruangan_id')
            ->whereNotNull('link_zoom')
            ->whereTime('jam_awal', '>', $now->format('H:i:s'))
            ->whereTime('jam_awal', '<=', $now->copy()->addMinutes(5)->format('H:i:s'))
            ->with(['detailPresensi.mahasiswa.user', 'matkul'])
            ->get();

        foreach ($presensis as $presensi) {
            foreach ($presensi->detailPresensi as $detail) {
                $user = $detail->mahasiswa->user ?? null;
                if (!$user)
                    continue;

                $this->createNotificationAndSend(
                    $user->id,
                    $user->name,
                    $presensi->matkul->nama_matkul ?? '-',
                    'pengumuman',
                    'Presensi Online akan dimulai',
                    'Presensi Anda akan dimulai pukul ' . Carbon::parse($presensi->jam_awal)->format('H:i'),
                    $fcmService,
                    $presensi->id
                );
            }
        }
    }

    protected function notifyPresensiOnlineAkanBerakhir($now, $fcmService)
    {
        $presensis = Presensi::whereDate('tgl_presensi', $now->toDateString())
            ->whereNull('ruangan_id')
            ->whereNotNull('link_zoom')
            ->whereTime('jam_akhir', '>', $now->format('H:i:s'))
            ->whereTime('jam_akhir', '<=', $now->copy()->addMinutes(5)->format('H:i:s'))
            ->with(['detailPresensi.mahasiswa.user', 'matkul'])
            ->get();

        foreach ($presensis as $presensi) {
            foreach ($presensi->detailPresensi as $detail) {
                // ✅ Tambahkan pengecekan status
                if ($detail->status != 0) {
                    continue;
                }

                $user = $detail->mahasiswa->user ?? null;
                if (!$user)
                    continue;

                $this->createNotificationAndSend(
                    $user->id,
                    $user->name,
                    $presensi->matkul->nama_matkul ?? '-',
                    'presensiAkanHabis',
                    'Presensi Online segera berakhir',
                    'Presensi Anda akan ditutup pukul ' . Carbon::parse($presensi->jam_akhir)->format('H:i'),
                    $fcmService,
                    $presensi->id
                );
            }
        }
    }

    protected function createNotificationAndSend($userId, $userName, $namaMatkul, $type, $title, $message, $fcmService, $presensiId)
    {
        $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
        $tanggal = $waktu->format('d') . ' ' . $waktu->translatedFormat('F Y'); // hasil: 01 Juni 2025
        $jam = $waktu->format('H.i'); // hasil: 15.21

        // Cek apakah notifikasi sudah pernah dikirim sebelumnya
        $alreadySent = Notification::where('user_id', $userId)
            ->where('title', $title)
            ->where('message', $message)
            ->whereDate('created_at', $waktu->toDateString())
            ->whereTime('created_at', '>=', $waktu->subMinutes(10)->format('H:i:s')) 
            ->exists();

        if ($alreadySent) {
            return; // Stop, sudah pernah dikirim
        }

        // Simpan notifikasi ke database
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'nama_user' => $userName,
            'tanggal' => $tanggal,
            'jam' => $jam,
            'mata_kuliah' => $namaMatkul,
            'presensi_id' => $presensiId
        ]);

        // Kirim FCM ke semua token user
        $tokens = FcmToken::where('user_id', $userId)->pluck('token');
        foreach ($tokens as $token) {
            $fcmService->send($token, $title, $message);
        }
    }
}
