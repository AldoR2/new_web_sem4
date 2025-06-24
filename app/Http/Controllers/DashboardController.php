<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Presensi;
use App\Models\Prodi;


use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexAdmin()
    {
        $data = [
        'title' => 'Dashboard',
        'mahasiswa' => Mahasiswa::count(),
        'dosen' => Dosen::count(),
        'matkul' => Matkul::count(),
        'prodi' => Prodi::count(),
        'dosenMengajar' => Presensi::with('pertemuan','dosen','ruangan')->whereDate('tgl_presensi', Carbon::today())->get(),
        'mingguan' => [],
        ];

        $statusMap = [
            1 => 'Hadir',
            2 => 'Izin',
            3 => 'Sakit',
            0 => 'Alpha'
        ];

        $chartData = [];

        foreach ($statusMap as $statusValue => $statusLabel) {
            $minggu = [];
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $weeksInMonth = ceil($startOfMonth->diffInDays($endOfMonth) / 7);


            for ($i = 1; $i <= $weeksInMonth; $i++) {
                $start = Carbon::now()->startOfMonth()->addWeeks($i - 1)->startOfWeek();
                $end = (clone $start)->endOfWeek();

                if ($start > $endOfMonth) break;
                if ($end > $endOfMonth) $end = $endOfMonth;

                $count = DetailPresensi::where('status', $statusValue)
                    ->whereHas('presensi', function ($q) use ($start, $end) {
                        $q->whereBetween('tgl_presensi', [$start, $end]);
                    })
                    ->count();

                $minggu[] = $count;
            }

            $chartData[] = [
                'name' => $statusLabel,
                'data' => $minggu
            ];
        }

        $data['tidakHadir'] = DetailPresensi::with(['mahasiswa','presensi.pertemuan.matkul','presensi.ruangan','presensi.pertemuan.prodi'])->whereIn('status', [0,2,3])->whereHas('presensi', function ($query){
            $query->whereDate('tgl_presensi', Carbon::today())->whereTime('jam_akhir', '<=', Carbon::now()->toTimeString());
        })->get();

        $data['hadir'] = DetailPresensi::with(['mahasiswa','presensi.pertemuan.matkul','presensi.ruangan','presensi.pertemuan.prodi'])->where('status', 1)->whereHas('presensi', function ($query){
            $query->whereDate('tgl_presensi', Carbon::today())->whereTime('jam_akhir', '<=', Carbon::now()->toTimeString());
        })->orderByDesc('waktu_presensi')->limit('40')->get();

        $data['mingguan'] = $chartData;
        return view('admin.dashboard',$data);
    }

        public function indexDosen()
    {
        $user = Auth::user()->dosen;

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'presensiHariIni' => Presensi::with(['pertemuan', 'dosen','ruangan'])
                ->whereDate('tgl_presensi', Carbon::today())
                ->where('dosen_id', $user->id)
                ->get(),
            'tidakHadir' => DetailPresensi::with(['mahasiswa','pertemuan.matkul','presensi.ruangan','pertemuan.prodi'])->whereIn('status', [0,2,3])->whereHas('presensi', function ($query) use ($user){
                $query->whereDate('tgl_presensi', Carbon::today())->where('dosen_id', $user->id)->whereTime('jam_akhir', '<=', Carbon::now()->toTimeString());
            })->get(),

            'dosenMingguan' => [],
        ];

        $minggu = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $weeksInMonth = ceil($startOfMonth->diffInDays($endOfMonth) / 7);

        for ($i = 1; $i <= $weeksInMonth; $i++) {
            $start = Carbon::now()->startOfMonth()->addWeeks($i - 1)->startOfWeek();
            $end = (clone $start)->endOfWeek();

            if ($start > $endOfMonth) break;
            if ($end > $endOfMonth) $end = $endOfMonth;

            $count = Presensi::where('dosen_id', $user->id)
                ->whereBetween('tgl_presensi', [$start, $end])
                ->count();

            $minggu[] = $count;
        }

        $data['dosenMingguan'] = [
            'name' => 'Pertemuan Mengajar',
            'data' => $minggu,
        ];

        return view('dosen.dashboard', $data);
    }

    public function indexMahasiswa(){
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

    public function indexSuperAdmin(){
        $data = [
        'title' => 'Dashboard',
        'admin' => Admin::count(),
        // 'dosen' => Dosen::count(),
        // 'matkul' => Matkul::count(),
        // 'prodi' => Prodi::count(),
        // 'dosenMengajar' => Presensi::with('prodi','dosen','matkul','tahunAjaran','ruangan')->whereDate('tgl_presensi', Carbon::today())->get(),
        // 'mingguan' => [],
        ];

        // $statusMap = [
        //     1 => 'Hadir',
        //     2 => 'Izin',
        //     3 => 'Sakit',
        //     0 => 'Alpha'
        // ];

        // $chartData = [];

        // foreach ($statusMap as $statusValue => $statusLabel) {
        //     $minggu = [];
        //     $startOfMonth = Carbon::now()->startOfMonth();
        //     $endOfMonth = Carbon::now()->endOfMonth();
        //     $weeksInMonth = ceil($startOfMonth->diffInDays($endOfMonth) / 7);


        //     for ($i = 1; $i <= $weeksInMonth; $i++) {
        //         $start = Carbon::now()->startOfMonth()->addWeeks($i - 1)->startOfWeek();
        //         $end = (clone $start)->endOfWeek();

        //         if ($start > $endOfMonth) break;
        //         if ($end > $endOfMonth) $end = $endOfMonth;

        //         $count = DetailPresensi::where('status', $statusValue)
        //             ->whereHas('presensi', function ($q) use ($start, $end) {
        //                 $q->whereBetween('tgl_presensi', [$start, $end]);
        //             })
        //             ->count();

        //         $minggu[] = $count;
        //     }

        //     $chartData[] = [
        //         'name' => $statusLabel,
        //         'data' => $minggu
        //     ];
        // }

        // $data['tidakHadir'] = DetailPresensi::with(['mahasiswa','presensi.matkul','presensi.ruangan','presensi.prodi'])->whereIn('status', [0,2,3])->whereHas('presensi', function ($query){
        //     $query->whereDate('tgl_presensi', Carbon::today())->whereTime('jam_akhir', '<=', Carbon::now()->toTimeString());
        // })->get();

        // $data['hadir'] = DetailPresensi::with(['mahasiswa','presensi.matkul','presensi.ruangan','presensi.prodi'])->where('status', 1)->whereHas('presensi', function ($query){
        //     $query->whereDate('tgl_presensi', Carbon::today())->whereTime('jam_akhir', '<=', Carbon::now()->toTimeString());
        // })->orderByDesc('waktu_presensi')->limit('40')->get();

        // $data['mingguan'] = $chartData;
        return view('superadmin.dashboard',$data);
    }
}
