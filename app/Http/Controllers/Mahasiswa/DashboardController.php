<?php

namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){
        $title = 'Dashboard';
        $mahasiswa = Auth::user()->mahasiswa;
        $presensiHariIni = Presensi::with(['pertemuan','dosen','ruangan','detailPresensi' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])
        ->whereHas('detailPresensi', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        ->whereDate('tgl_presensi', Carbon::today())->get();
        // $presensiHariIni = Presensi::with('pertemuan','dosen','ruangan')->whereHas('detailPresensi')
        //     ->whereDate('tgl_presensi', Carbon::today())->get();
        $biodata = Mahasiswa::with('prodi','provinsi','kota','kecamatan','kelurahan')->findOrFail($mahasiswa->id);

        return view('mahasiswa.dashboard',compact('title','presensiHariIni','biodata'));
    }
}
