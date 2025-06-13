<?php

namespace App\Http\Controllers\Api\Listview;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LectureLecturerController extends Controller
{
    public function showLecture(Request $request)
    {
        $dosenId = $request->query('dosen_id');

        $presensis = Presensi::with('matkul')
            ->whereDate('tgl_presensi', now()->toDateString())
            ->where('dosen_id', $dosenId)
            ->whereNotNull('link_zoom')
            ->get();

        if ($presensis->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data presensi online yang ditampilkan',
            ], 200);
        }

        $data = $presensis->map(function ($item) {
            return [
                'presensis_id' => $item->id,
                'nama_matkul' => optional($item->matkul)->nama_matkul,
                'durasi_presensi' => Carbon::parse($item->jam_awal)->format('H:i') . ' - ' . Carbon::parse($item->jam_akhir)->format('H:i'),
                'link_zoom' => $item->link_zoom,
                'nama_dosen' => '', // bisa ambil dari relasi dosen jika dibutuhkan
                'tgl_presensi' => Carbon::parse($item->tgl_presensi)->format('d-m-Y'),
                'semester' => $item->semester
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data presensi online berhasil ditampilkan',
            'data' => $data
        ]);
    }

    public function showLectureContent(Request $request)
    {
        $presensisId = $request->query('presensis_id');

        $presensi = Presensi::with('matkul')
            ->whereDate('tgl_presensi', now()->toDateString())
            ->where('id', $presensisId)
            ->whereNotNull('link_zoom')
            ->first();

        if (!$presensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data presensi online tidak ditemukan'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data presensi online berhasil diambil',
            'data' => [
                'presensis_id' => $presensi->id,
                'nama_matkul' => optional($presensi->matkul)->nama_matkul,
                'semester' => $presensi->semester,
                'nama_dosen' => '', // bisa relasi
                'durasi_presensi' => Carbon::parse($presensi->jam_awal)->format('H:i') . ' - ' . Carbon::parse($presensi->jam_akhir)->format('H:i'),
                'link_zoom' => $presensi->link_zoom,
                'tgl_presensi' => Carbon::parse($presensi->tgl_presensi)->format('d-m-Y')
            ]
        ]);
    }

    public function updateLecture(Request $request)
    {
        $request->validate([
            'link_zoom' => 'required|string',
            'presensis_id' => 'required|exists:presensis,id'
        ]);

        $presensi = Presensi::find($request->presensis_id);
        $presensi->link_zoom = $request->link_zoom;

        if ($presensi->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Link Zoom berhasil diperbarui'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memperbarui Link Zoom'
        ], 400);
    }
}
