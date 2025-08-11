<?php

namespace App\Http\Controllers\Api\Listview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceStudentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|exists:mahasiswas,nim',
        ]);

        $nim = $request->nim;

        $rekap = DB::table('detail_presensis')
            ->join('presensis', 'presensis.id', '=', 'detail_presensis.presensi_id')
            ->join('pertemuans', 'pertemuans.id', '=', 'presensis.pertemuan_id')
            ->join('matkuls', 'pertemuans.matkul_id', '=', 'matkuls.id')
            ->join('mahasiswas', function ($join) {
                $join->on('mahasiswas.id', '=', 'detail_presensis.mahasiswa_id')
                    ->on('mahasiswas.semester', '=', 'matkuls.semester');
            })
            ->where('mahasiswas.nim', $nim)
            ->select(
                'detail_presensis.mahasiswa_id',
                'mahasiswas.nim',
                'matkuls.nama_matkul',
                'matkuls.kode_matkul',
                'detail_presensis.status',
                'matkuls.semester'
            )
            ->get();

        if ($rekap->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Data rekap tidak ditemukan'
            ], 404);
        }

        $rekap = $rekap->map(function ($item) {
            return [
                'mahasiswa_id' => (int) $item->mahasiswa_id,
                'nim' => $item->nim,
                'nama_matkul' => $item->nama_matkul,
                'kode_matkul' =>  $item->kode_matkul,
                'status' => (int) $item->status,
                'semester' => (int) $item->semester,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data Rekap semester sekarang ditemukan',
            'data' => $rekap
        ], 200);
    }
}
