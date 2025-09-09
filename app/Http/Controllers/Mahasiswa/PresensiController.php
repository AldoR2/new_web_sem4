<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\DetailPresensi;
use App\Models\Mahasiswa;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presensi;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Data Presensi';
        $mahasiswa = Auth::user()->mahasiswa;
        $biodata = Mahasiswa::findOrFail($mahasiswa->id);

        $presensiHariIni = Presensi::with(['pertemuan','dosen','ruangan','detailPresensi' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])->whereDate('tgl_presensi', Carbon::today())->get();

        $now = Carbon::now();
        $start = $now->copy()->subMinutes(30);
        $end = $now->copy()->addMinutes(30);

        $presensi = Presensi::with('pertemuan.matkul', 'ruangan', 'detailPresensi')
        ->whereHas('detailPresensi', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        ->whereHas('pertemuan', function ($q) {
            $q->whereIn('status', ['aktif','uts','uas']);
        })
        ->whereDate('tgl_presensi', Carbon::today())
        ->whereTime('jam_awal', '<=', $now->format('H:i:s'))
        ->whereTime('jam_akhir', '>=', $now->format('H:i:s'))
        ->first();

        $presensiTercatat = optional($presensi?->detailPresensi->first())->waktu_presensi;

        $riwayat =  Presensi::with(['pertemuan','ruangan','detailPresensi' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])
        ->whereDate('tgl_presensi', '=', $now->toDateString())
        ->whereTime('jam_akhir', '<', $now->format('H:i:s'))
        ->whereHas('detailPresensi', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        ->whereHas('pertemuan', function ($p) {
            $p->whereIn('status',['aktif','uts','uas']);
        })
        ->orderByDesc('tgl_presensi')
        ->get();

        return view('mahasiswa.presensi', compact('presensi','title','biodata','presensiTercatat','riwayat'));
    }
}
