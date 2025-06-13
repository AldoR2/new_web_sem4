<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        $title = 'Jadwal Mengajar Dosen';
        $dosen = Auth::user()->dosen;
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        $jadwal = Jadwal::with('prodi','dosen','ruangan','tahun','matkul')->where('dosen_id', $dosen->id)->get();
        return view('dosen.jadwal', compact('title','jadwal','tahun'));
    }

    public function getFilterJadwal(Request $request){
        $tahun = $request->query('tahun_ajaran');

        $query = Jadwal::query()->with('prodi','tahun','dosen','matkul','ruangan');

        if ($tahun) {
            $query->where('tahun_ajaran_id', $tahun);
        }

        $jadwal = $query->get();

        return response()->json($jadwal);
    }
}
