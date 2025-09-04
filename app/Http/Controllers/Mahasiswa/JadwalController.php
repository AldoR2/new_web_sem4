<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class JadwalController extends Controller
{
    public function index()
    {
        $title = "Jadwal Mahasiswa";
        $mahasiswa = Auth::user()->mahasiswa;
        $jadwal = Jadwal::with(['prodi','dosen','matkul','ruangan','detailJadwal' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])
        ->whereHas('detailJadwal', function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        return view('mahasiswa.jadwal',compact('title','jadwal','tahun'));
    }

    public function exportJadwalPdf(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $tahunId = $request->query('tahun_ajaran');
        $tahunAjaran = $tahunId ? TahunAjaran::find($tahunId) : TahunAjaran::where('status', true)->first();

        if (!$mahasiswa) {
            abort(403, 'Mahasiswa tidak ditemukan atau tidak terhubung dengan akun.');
        }

        $jadwal = Jadwal::with(['prodi','dosen','ruangan','tahun','matkul','detailJadwal' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])->whereHas('detailJadwal', function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->when($tahunAjaran, function ($q) use ($tahunAjaran){
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();

        $data = [
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->nama,
            'prodi' => $mahasiswa->prodi->jenjang . ' ' . $mahasiswa->prodi->nama_prodi,
            'tahun' => $tahunAjaran,
            'jadwal' => $jadwal,
        ];

        $pdf = Pdf::loadView('mahasiswa.export.jadwal-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Jadwal Mahasiswa.pdf');
    }

    public function exportJadwalExcel(Request $request,)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $tahunId = $request->query('tahun_ajaran');
        $tahunAjaran = $tahunId ? TahunAjaran::find($tahunId) : TahunAjaran::where('status', true)->first();

        $jadwal = Jadwal::with(['prodi','dosen','ruangan','tahun','matkul','detailJadwal' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])->whereHas('detailJadwal', function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->when($tahunAjaran, function ($q) use ($tahunAjaran){
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();

        $export = new class($mahasiswa, $jadwal, $tahunAjaran) implements FromView {

            protected $mahasiswa;
            protected $jadwal;
            protected $tahunAjaran;

            public function __construct($mahasiswa, $jadwal, $tahunAjaran)
            {
                $this->mahasiswa = $mahasiswa;
                $this->jadwal = $jadwal;
                $this->tahunAjaran = $tahunAjaran;
            }

            public function view(): View
            {
                return view('mahasiswa.export.jadwal-excel', [
                    'nim' => $this->mahasiswa->nim,
                    'nama' => $this->mahasiswa->nama,
                    'prodi' => $this->mahasiswa->prodi->jenjang . ' ' . $this->mahasiswa->prodi->nama_prodi,
                    'jadwal' => $this->jadwal,
                    'tahun' => $this->tahunAjaran,

                ]);
            }
        };
        return Excel::download($export, 'Jadwal Mahasiswa.xlsx');
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
