<?php

namespace App\Http\Controllers\Api\activity;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AllScheduleController extends Controller
{
    public function scheduleStudent(Request $request)
    {
        $mahasiswaId = $request->query('mahasiswa_id');

        if (!$mahasiswaId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa Id tidak boleh kosong'
            ], 400);
        }

        $mahasiswa = Mahasiswa::find($mahasiswaId);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $jadwals = Jadwal::with(['matkul', 'ruangan'])
            ->where('prodi_id', $mahasiswa->prodi_id)
            ->where('semester', $mahasiswa->semester)
            ->whereHas('tahun', function ($query) {
                $query->where('status', 1); // hanya ambil tahun ajaran aktif
            })
            ->get();

        if ($jadwals->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data jadwal yang ditampilkan'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data jadwal berhasil diambil',
            'data' => $jadwals->map(function ($jadwal) {
                return [
                    'durasi' => $jadwal->durasi,
                    'hari' => $jadwal->hari,
                    'jam' => $jadwal->jam,
                    'nama_matkul' => optional($jadwal->matkul)->nama_matkul,
                    'nama_ruangan' => optional($jadwal->ruangan)->nama_ruangan,
                ];
            }),
        ]);
    }

    public function scheduleLecturer(Request $request)
    {
        $dosenId = $request->query('dosen_id');

        if (!$dosenId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dosen Id tidak boleh kosong'
            ], 400);
        }

        $jadwals = Jadwal::with(['matkul', 'ruangan'])
            ->where('dosen_id', $dosenId)
            ->whereHas('tahun', function ($query) {
                $query->where('status', 1); // hanya tahun ajaran aktif
            })
            ->get();

        if ($jadwals->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data jadwal yang ditampilkan'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data jadwal berhasil diambil',
            'data' => $jadwals->map(function ($jadwal) {
                return [
                    'durasi' => $jadwal->durasi,
                    'hari' => $jadwal->hari,
                    'jam' => $jadwal->jam,
                    'semester' => $jadwal->semester,
                    'nama_matkul' => optional($jadwal->matkul)->nama_matkul,
                    'kode_matkul' => optional($jadwal->matkul)->kode_matkul,
                    'nama_ruangan' => optional($jadwal->ruangan)->nama_ruangan,
                ];
            }),
        ]);
    }
}
