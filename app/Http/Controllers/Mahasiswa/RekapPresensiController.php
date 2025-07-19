<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Models\TahunAjaran;
use App\Services\RekapMahasiswaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class RekapPresensiController extends Controller
{
    public function index(Request $request, RekapMahasiswaService $service){
        $mahasiswa = Auth::user()->mahasiswa;
        $data['title'] = "Rekap Presensi";
        $tahunAktif = TahunAjaran::where('status', 1)->first();
        $data['tahun'] = TahunAjaran::where('tahun_awal', '>=', $mahasiswa->tahun_masuk)
            ->when($tahunAktif, function ($query) use ($tahunAktif) {
                $query->where('tahun_awal', '<=', $tahunAktif->tahun_awal);
            })
            ->orderBy('tahun_awal')
            ->get();
        $hasil = $service->getRekap();
        $data['rekap'] = $hasil['rekap'];
        $data['totalPertemuan'] = $hasil['totalPertemuan'];
        return view('mahasiswa.rekap_mahasiswa',$data);
    }

    public function exportRekapPdf(Request $request, RekapMahasiswaService $service)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Mahasiswa tidak ditemukan atau tidak terhubung dengan akun.');
        }

        $rekapData = $service->getRekap();

        $data = [
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->nama,
            'prodi' => $mahasiswa->prodi->jenjang . ' ' . $mahasiswa->prodi->nama_prodi,
            'semester' => 'Ganjil',
            'matkul' => '-',
            'rekap' => $rekapData['rekap'],
            'totalPertemuan' => $rekapData['totalPertemuan'],
        ];

        $pdf = Pdf::loadView('mahasiswa.export.rekap-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Rekap Kehadiran Mahasiswa.pdf');
    }

    public function exportRekapExcel(Request $request, RekapMahasiswaService $service)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $rekapData = $service->getRekap();

        $totalPertemuan = $rekapData['totalPertemuan'] ?? 16;

        $export = new class($mahasiswa, $rekapData, $totalPertemuan) implements FromView {

            protected $mahasiswa;
            protected $rekapData;
            protected $totalPertemuan;

            public function __construct($mahasiswa, $rekapData, $totalPertemuan)
            {
                $this->mahasiswa = $mahasiswa;
                $this->rekapData = $rekapData;
                $this->totalPertemuan = $totalPertemuan;
            }

            public function view(): View
            {
                return view('mahasiswa.export.rekap-excel', [
                    'nim' => $this->mahasiswa->nim,
                    'nama' => $this->mahasiswa->nama,
                    'prodi' => $this->mahasiswa->prodi->jenjang . ' ' . $this->mahasiswa->prodi->nama_prodi,
                    'semester' => 'Ganjil', // bisa kamu sesuaikan
                    'matkul' => '-',        // bisa kamu sesuaikan
                    'rekap' => $this->rekapData['rekap'],
                    'totalPertemuan' => $this->totalPertemuan,
                ]);
            }
        };

        return Excel::download($export, 'Rekap Kehadiran Mahasiswa.xlsx');
    }
        public function getFilterRekap(Request $request, RekapMahasiswaService $service)
    {
        $data['title'] = 'Rekap Mahasiswa';
        $data['judul'] = 'Rekap Mahasiswa';
        $mahasiswa = Auth::user()->mahasiswa;
        $data['rekap'] = [];
        $data['totalPertemuan'] = 16;

        $hasil = $service->getFilterRekap( $request->tahun_ajaran);
        $data['rekap'] = $hasil['rekap'];
        $data['totalPertemuan'] = $hasil['totalPertemuan'];

        return response()->json($data);
    }

}
