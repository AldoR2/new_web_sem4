<?php

namespace App\Http\Controllers\Api\ActivityLecturer;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PresenceIncrementController extends Controller
{
    public function getLastIncrement(Request $request)
    {
        $year = now()->format('y'); // contoh: '25'

        // Cari presensi_id yang diawali dengan 'TR' + tahun, lalu ambil 4 digit terakhir sebagai angka
        $lastIncrement = Presensi::where('presensi_id', 'like', 'TR' . $year . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(presensi_id, -4) AS UNSIGNED)) as lastIncrement')
            ->value('lastIncrement');

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor presensi terakhir berhasil diambil',
            'data' => [
                'tahun' => $year,
                'lastIncrement' => (int) $lastIncrement
            ]
        ]);

    }
}
