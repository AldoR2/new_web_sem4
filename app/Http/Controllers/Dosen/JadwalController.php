<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class JadwalController extends Controller
{
    public function index()
    {
        $title = 'Jadwal Mengajar Dosen';
        $dosen = Auth::user()->dosen;
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        $jadwal = Jadwal::with('prodi','dosen','ruangan','tahun','matkul')->where('dosen_id', $dosen->id)->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();
        return view('dosen.jadwal', compact('title','jadwal','tahun'));
    }

        public function exportPdf(Request $request)
    {
        $dosen = Auth::user()->dosen;
        $tahunId = $request->query('tahun_ajaran');
        $tahunAjaran = $tahunId ? TahunAjaran::find($tahunId) : TahunAjaran::where('status', true)->first();

        if (!$dosen) {
            abort(403, 'Dosen tidak ditemukan atau tidak terhubung dengan akun.');
        }

        // $rekapData = $service->getRekapDosen($dosen->id);
        $jadwal = Jadwal::with('prodi','dosen','ruangan','tahun','matkul')->where('dosen_id', $dosen->id)->when($tahunAjaran, function ($q) use ($tahunAjaran){
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();

        $data = [
            'nip' => $dosen->nip,
            'nama' => $dosen->nama,
            'prodi' => $dosen->prodi->jenjang . ' ' . $dosen->prodi->nama_prodi,
            'tahun' => $tahunAjaran,
            // 'tahun_ajaran' => $tahunAjaran ? "{$tahunAjaran->tahun_awal}/{$tahunAjaran->tahun_akhir}" : '-',
            'jadwal' => $jadwal,
            // 'totalPertemuan' => $rekapData['totalPertemuan'],
        ];

        $pdf = Pdf::loadView('dosen.jadwal-dosen-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Jadwal Dosen.pdf');
    }

    public function exportExcel(Request $request,)
    {
        $dosen = Auth::user()->dosen;

        $tahunId = $request->query('tahun_ajaran');
        $tahunAjaran = $tahunId ? TahunAjaran::find($tahunId) : TahunAjaran::where('status', true)->first();

        // $rekapData = $service->getRekapDosen($dosen->id);
        $jadwal = Jadwal::with('prodi','dosen','ruangan','tahun','matkul')->where('dosen_id', $dosen->id)->when($tahunAjaran, function ($q) use ($tahunAjaran){
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();
        // $totalPertemuan = $rekapData['totalPertemuan'] ?? 16;

        $export = new class($dosen, $jadwal, $tahunAjaran) implements FromView {

            protected $dosen;
            protected $jadwal;
            protected $tahunAjaran;

            public function __construct($dosen, $jadwal, $tahunAjaran)
            {
                $this->dosen = $dosen;
                $this->jadwal = $jadwal;
                $this->tahunAjaran = $tahunAjaran;
            }

            public function view(): View
            {
                return view('dosen.jadwal-dosen-excel', [
                    'nip' => $this->dosen->nip,
                    'nama' => $this->dosen->nama,
                    'prodi' => $this->dosen->prodi->jenjang . ' ' . $this->dosen->prodi->nama_prodi,
                    'jadwal' => $this->jadwal,
                    'tahun' => $this->tahunAjaran,

                ]);
            }
        };
        return Excel::download($export, 'Jadwal Dosen.xlsx');
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
