<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Presensi;
use App\Models\Prodi;


use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
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

}
