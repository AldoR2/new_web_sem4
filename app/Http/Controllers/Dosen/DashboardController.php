<?php

namespace App\Http\Controllers\Dosen;
use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->dosen;

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'presensiHariIni' => Presensi::with(['pertemuan', 'dosen','ruangan'])
                ->whereDate('tgl_presensi', Carbon::today())
                ->where('dosen_id', $user->id)
                ->get(),
            'tidakHadir' => DetailPresensi::with(['mahasiswa','presensi.pertemuan.matkul','presensi.ruangan','presensi.pertemuan.prodi'])->whereIn('status', [0,2,3])->whereHas('presensi', function ($query) use ($user){
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

}
