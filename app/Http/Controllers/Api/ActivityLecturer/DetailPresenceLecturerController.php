<?php

namespace App\Http\Controllers\Api\ActivityLecturer;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DetailPresenceLecturerController extends Controller
{
    public function showHeader(Request $request)
    {
        $presensiId = $request->query('presensis_id');

        if (!$presensiId) {
            return response()->json(['status' => 'error', 'message' => 'Presensi id tidak boleh kosong'], 400);
        }

        $presensi = Presensi::where('id', $presensiId)->first();

        if (!$presensi) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data header yang ditampilkan'], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data header detail presensi berhasil ditampilkan',
            'data' => [
                'nama_matkul' => $presensi->matkul->nama_matkul,
                'durasi_presensi' => Carbon::parse($presensi->jam_awal)->format('H:i') . ' - ' . Carbon::parse($presensi->jam_akhir)->format('H:i'),
                'nama_prodi' => $presensi->prodi->nama_prodi,
            ]
        ]);
    }
    public function showDetailPresence(Request $request)
    {
        $presensiId = $request->query('presensis_id');

        if (!$presensiId) {
            return response()->json(['status' => 'error', 'message' => 'Presensi id tidak boleh kosong'], 400);
        }

        $details = DetailPresensi::where('presensi_id', $presensiId)->get();

        if ($details->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data detail presensi yang ditampilkan'], 200);
        }

        $data = $details->map(function ($item) {
            $mahasiswa = Mahasiswa::where('id', $item->mahasiswa_id)->first();
            return [
                'nim' => $item->mahasiswa->nim,
                'nama' => $item->mahasiswa->nama,
                'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                'status' => $item->status,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data detail presensi berhasil ditampilkan',
            'data' => $data
        ]);
    }
    public function showDetailStudent(Request $request)
    {
        $nim = $request->query('nim');

        if (!$nim) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIM tidak boleh kosong'
            ], 400);
        }

        $detail = DetailPresensi::whereHas('mahasiswa', function ($query) use ($nim) {
            $query->where('nim', $nim);
        })->with('mahasiswa')->first();

        if (!$detail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data detail mahasiswa yang ditampilkan',
                'data' => null
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data detail mahasiswa berhasil ditampilkan',
            'data' => [
                'status' => $detail->status,
                'waktu_presensi' => $detail->waktu_presensi,
                'alasan' => $detail->alasan,
                'bukti' => $detail->bukti,
            ]
        ]);
    }
    public function showInformationStudent(Request $request)
    {
        $nim = $request->query('nim');

        if (!$nim) {
            return response()->json(['status' => 'error', 'message' => 'NIM tidak boleh kosong'], 400);
        }

        $mahasiswa = Mahasiswa::with('prodi')->where('nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada data biodata yang ditampilkan'], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data biodata berhasil ditampilkan',
            'data' => [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'semester' => $mahasiswa->semester,
                'nama_prodi' => $mahasiswa->prodi->nama_prodi,
                'foto' => $mahasiswa->foto,
            ]
        ]);
    }
}
