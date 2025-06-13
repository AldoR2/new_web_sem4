<?php

namespace App\Http\Controllers\Api\ActivityLecturer;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Http\Request;

class AttendanceLecturerController extends Controller
{
    public function showMajor(Request $request)
    {
        $prodis = Prodi::select('id', 'nama_prodi')->get();

        if ($prodis->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data prodi yang ditampilkan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data prodi berhasil ditampilkan',
            'data' => $prodis
        ], 200);
    }

    public function showStudent(Request $request)
    {
        $semester = $request->query('semester');
        $prodi_id = $request->query('prodi_id');

        if (!$semester || !$prodi_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Semester dan Id Prodi tidak boleh kosong'
            ], 400);
        }

        $mahasiswa = Mahasiswa::where('semester', $semester)
            ->where('prodi_id', $prodi_id)
            ->select('nim', 'nama', 'email', 'jenis_kelamin')
            ->get();

        if ($mahasiswa->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data mahasiswa yang ditampilkan',
                'data' => null
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil ditampilkan',
            'data' => $mahasiswa
        ], 200);
    }
}
