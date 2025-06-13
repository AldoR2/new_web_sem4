<?php

namespace App\Http\Controllers\Api\Listview;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceLecturerController extends Controller
{
    public function showToday(Request $request)
    {
        $dosenId = $request->query('dosen_id');

        if (!$dosenId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dosen ID tidak boleh kosong'
            ], 404);
        }

        $data = Presensi::with(['matkul:id,nama_matkul,durasi_matkul,kode_matkul'])
            ->whereDate('tgl_presensi', now())
            ->where('dosen_id', $dosenId)
            ->whereNotNull('link_zoom')
            ->orderByRaw('TIME(jam_awal)')
            ->get()
            ->map(function ($item) {
                return [
                    'semester' => $item->semester,
                    'presensis_id' => $item->id,
                    'jam_awal' => Carbon::parse($item->jam_awal)->format('H:i'),
                    'jam_akhir' => Carbon::parse($item->jam_akhir)->format('H:i'),
                    'nama_matkul' => $item->matkul->nama_matkul,
                    'kode_matkul' => $item->matkul->kode_matkul,
                    'durasi_matkul' => $item->matkul->durasi_matkul,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => $data->isEmpty() ? 'Tidak ada data presensi yang ditampilkan' : 'Data presensi berhasil ditampilkan',
            'data' => $data
        ]);
    }

    public function updatePresence(Request $request)
    {
        $request->validate([
            'presensis_id' => 'required|exists:presensis,id',
            'jam_awal' => 'required|date_format:H:i',
            'jam_akhir' => 'required|date_format:H:i',
        ]);

        $presensi = Presensi::find($request->presensis_id);
        $currentTime = now()->format('H:i');

        // Validasi: Tidak boleh update saat jam_awal sampai jam_presensi atau setelah jam_presensi
        if ($currentTime >= $presensi->jam_awal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat mengubah waktu presensi ketika presensi sedang/sudah berjalan'
            ], 422);
        }

        // Validasi tambahan: jam_awal harus kurang dari jam_akhir
        if ($request->jam_awal >= $request->jam_akhir) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jam awal harus kurang dari jam akhir'
            ], 422);
        }

        $presensi->jam_awal = $request->jam_awal;
        $presensi->jam_akhir = $request->jam_akhir;
        $presensi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Waktu Presensi berhasil diperbarui'
        ]);
    }

    public function deletePresence(Request $request)
    {
        $request->validate([
            'presensis_id' => 'required|exists:presensis,id',
        ]);

        $presensiId = $request->presensis_id;

        // Hapus relasi detail presensi dulu
        DetailPresensi::where('presensi_id', $presensiId)->delete();
        Presensi::destroy($presensiId);

        return response()->json([
            'status' => 'success',
            'message' => 'Data presensi berhasil dihapus'
        ]);
    }
}
