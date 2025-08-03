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
                'nama_matkul' => $presensi->pertemuan->matkul->nama_matkul,
                'durasi_presensi' => Carbon::parse($presensi->jam_awal)->format('H:i') . ' - ' . Carbon::parse($presensi->jam_akhir)->format('H:i'),
                'nama_prodi' => $presensi->pertemuan->prodi->nama_prodi,
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
        $presensiId = $request->query('presensis_id');

        if (!$nim || !$presensiId) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIM dan presensis_id tidak boleh kosong'
            ], 400);
        }

        // 1. Cari mahasiswa berdasarkan NIM
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan',
                'data' => null
            ], 404);
        }

        // 2. Cari data detail presensi berdasarkan mahasiswa_id dan presensi_id
        $detail = DetailPresensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('presensi_id', $presensiId)
            ->first();

        if (!$detail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data detail presensi untuk mahasiswa ini pada presensi tersebut',
                'data' => null
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data detail mahasiswa berhasil ditampilkan',
            'data' => [
                'status' => (int) $detail->status,
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
