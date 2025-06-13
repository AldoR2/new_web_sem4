<?php

namespace App\Http\Controllers\Api\Listview;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;

class LectureStudentController extends Controller
{
    public function lecture(Request $request)
    { {
            $nim = $request->query('nim');
            if (!$nim) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIM tidak boleh kosong'
                ], 400);
            }

            $data = DetailPresensi::with(['presensi.matkul', 'presensi.dosen'])
                ->whereHas('mahasiswa', function ($q) use ($nim) {
                    $q->where('nim', $nim);
                })
                ->whereHas('presensi', function ($q) {
                    $q->whereDate('tgl_presensi', now())
                        ->whereNotNull('link_zoom');
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'presensis_id' => $item->presensi->id,
                        'nama_matkul' => $item->presensi->matkul->nama_matkul,
                        'semester' => $item->mahasiswa->semester,
                        'nama_dosen' => $item->presensi->dosen->nama,
                        'durasi_presensi' => date('H:i', strtotime($item->presensi->jam_awal)) . ' - ' . date('H:i', strtotime($item->presensi->jam_akhir)),
                        'link_zoom' => $item->presensi->link_zoom,
                        'tgl_presensi' => $item->presensi->tgl_presensi,
                    ];
                });

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data zoom tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data presensi online berhasil diambil',
                'data' => $data
            ]);
        }
    }

     public function lectureContent(Request $request)
    {
        $presensiId = $request->query('presensis_id');
        if (!$presensiId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Presensis Id tidak boleh kosong'
            ], 400);
        }

        $detail = DetailPresensi::with(['presensi.matkul', 'presensi.dosen'])
            ->where('presensi_id', $presensiId)
            ->whereHas('presensi', function ($q) {
                $q->whereDate('tgl_presensi', now())->whereNotNull('link_zoom');
            })
            ->first();

        if (!$detail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data zoom tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data presensi online berhasil diambil',
            'data' => [
                'presensis_id'    => $detail->presensi->id,
                'nama_matkul'     => $detail->presensi->matkul->nama_matkul,
                'semester'        => $detail->presensi->matkul->semester,
                'nama_dosen'      => $detail->presensi->dosen->nama,
                'durasi_presensi' => date('H:i', strtotime($detail->presensi->jam_awal)) . ' - ' . date('H:i', strtotime($detail->presensi->jam_akhir)),
                'link_zoom'       => $detail->presensi->link_zoom,
                'tgl_presensi'    => $detail->presensi->tgl_presensi,
            ]
        ]);
    }
}
