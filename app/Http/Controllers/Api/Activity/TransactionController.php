<?php

namespace App\Http\Controllers\Api\activity;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show(Request $request)
    {
        $presensiId = $request->query('presensi_id');
        $mahasiswaId = $request->query('mahasiswa_id');

        if (!$presensiId || !$mahasiswaId) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Presensi Id & Mahasiswa Id tidak boleh kosong'
            ], 400);
        }

        // Cek apakah sudah presensi
        $detail = DetailPresensi::where('presensi_id', $presensiId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->first();

        if (!$detail) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data Presensi tidak ditemukan'
            ], 404);
        }

        if ($detail->status > 0) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Anda sudah absensi'
            ], 200);
        }

        // Ambil relasi ke Presensi dan Matkul
        $presensi = $detail->presensi;
        $matkul = $presensi->matkul;

        return response()->json([
            'status' => 'success',
            'message' => 'Data presensi berhasil ditemukan',
            'data' => [
                'status' => $detail->status,
                'durasi_presensi' => date('H:i', strtotime($detail->presensi->jam_awal)) . ' - ' . date('H:i', strtotime($detail->presensi->jam_akhir)),
                'tgl_presensi' => $presensi->tgl_presensi,
                'mahasiswa_id' => $mahasiswaId,
                'presensi_id' => $presensi->presensi_id,
                'nama_matkul' => $matkul->nama_matkul,
                'kode_matkul' => $matkul->kode_matkul,
            ]
        ], 200);
    }
}
