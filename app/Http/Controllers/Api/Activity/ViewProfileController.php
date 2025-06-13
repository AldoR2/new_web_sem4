<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class ViewProfileController extends Controller
{
    public function show(Request $request)
    {
        $nip = $request->query('nip');
        $nim = $request->query('nim');

        if ($nip) {
            $dosen = Dosen::with('prodi')->where('nip', $nip)->first();

            if (!$dosen) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data dosen tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data view profil berhasil diambil',
                'role' => 'dosen',
                'data' => [
                    'nama' => $dosen->nama,
                    'nip' => $dosen->nip,
                    'email' => $dosen->email,
                    'jenis_kelamin' => $dosen->jenis_kelamin,
                    'agama' => $dosen->agama,
                    'tempat_lahir' => $dosen->tempat_lahir,
                    'tgl_lahir' => $dosen->tgl_lahir,
                    'alamat' => $dosen->alamat,
                    'no_telp' => $dosen->no_telp,
                    'nama_prodi' => $dosen->prodi->nama_prodi ?? null,
                ]
            ]);
        }

        if ($nim) {
            $mahasiswa = Mahasiswa::with('prodi')->where('nim', $nim)->first();

            if (!$mahasiswa) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data mahasiswa tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data view profil berhasil diambil',
                'role' => 'mahasiswa',
                'data' => [
                    'nama' => $mahasiswa->nama,
                    'nim' => $mahasiswa->nim,
                    'email' => $mahasiswa->email,
                    'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                    'agama' => $mahasiswa->agama,
                    'tempat_lahir' => $mahasiswa->tempat_lahir,
                    'tgl_lahir' => $mahasiswa->tgl_lahir,
                    'alamat' => $mahasiswa->alamat,
                    'semester' => $mahasiswa->semester,
                    'no_telp' => $mahasiswa->no_telp,
                    'nama_prodi' => $mahasiswa->prodi->nama_prodi ?? null,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'code' => 422,
            'message' => 'NIP atau NIM harus diisi',
            'data' => null
        ], 422);
    }
}
