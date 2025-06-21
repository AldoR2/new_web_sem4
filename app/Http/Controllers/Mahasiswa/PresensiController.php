<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Requests\Admin\StorePresensi;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


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
            $q->where('status','aktif');
        })
        ->whereDate('tgl_presensi', Carbon::today())
        ->whereTime('jam_awal', '<=', $now->format('H:i:s'))
        ->whereTime('jam_akhir', '>=', $now->format('H:i:s'))
        ->first();

        $presensiTercatat = optional($presensi?->detailPresensi->first())->waktu_presensi;

        $riwayat =  Presensi::with(['pertemuan','ruangan','detailPresensi' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])
        // $riwayat =  Presensi::with('pertemuan','ruangan','detailPresensi')
        ->whereDate('tgl_presensi', '=', $now->toDateString()) // hari ini atau sebelumnya
        ->whereTime('jam_akhir', '<', $now->format('H:i:s'))     // pastikan sudah selesai
        ->whereHas('detailPresensi', function ($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })
        // ->whereHas('pertemuan', function ($p) {
        //     $p->where('status','aktif');
        // })
        ->orderByDesc('tgl_presensi')
        ->get();

        return view('mahasiswa.presensi', compact('presensi','title','biodata','presensiTercatat','riwayat'));
    }

    public function prosesPresensi(Request $request){
        $rfid = $request->input('rfid');

        $mahasiswa = Mahasiswa::where('rfid', $rfid)->first();
        if (!$mahasiswa) {
            return response()->json(['status'=>'error', 'message'=>'Mahasiswa Tidak ditemukan'], 404);
        }

        $presensi = DetailPresensi::where('id', $mahasiswa->id)->where('status',0)->whereHas('presensi', function ($query){
            $query->whereNull('link_zoom')->orWhere('link_zoom','');
        })->with('presensi')->first();

        if (!$presensi) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada presensi aktif'], 404);
        }

        $tglPresensi = $presensi->presensi->tgl_presensi;
        $jamAwal = $presensi->presensi->jam_awal;
        $jamAkhir = $presensi->presensi->jam_akhir;

        $timeMulai = Carbon::parse("$tglPresensi $jamAwal");
        $timeBerakhir = Carbon::parse("$tglPresensi $jamAkhir");
        $now = Carbon::now();

        if ($now->lt($timeMulai)) {
            return response()->json(['status' => 'error', 'message' => 'Absensi belum dimulai']);
        } elseif ($now->gt($timeBerakhir)) {
            return response()->json(['status' => 'error', 'message' => 'Absensi sudah kadaluarsa']);
        }

        $presensi->update([
            'status' => 1,
            'waktu_presensi' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Presensi berhasil']);

    }
}
