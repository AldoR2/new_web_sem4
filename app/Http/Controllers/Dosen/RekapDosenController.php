<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Services\RekapDosenService;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class RekapDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, RekapDosenService $service)
    {
        $data['title'] = 'Rekap Dosen';
        $data['judul'] = 'Rekap Dosen';
        $data['dosen'] = Dosen::all();
        $data['prodi'] = Prodi::all();
        $data['dosenTerpilih'] = Auth::user()->dosen;
        $data['tahun'] = TahunAjaran::orderBy('tahun_awal')->get();
        $data['rekap'] = [];
        $data['totalPertemuan'] = 16;

        $hasil = $service->getRekapDosen($data['dosenTerpilih']->id);
        $data['rekap'] = $hasil['rekap'];
        $data['totalPertemuan'] = $hasil['totalPertemuan'];


        return view('dosen.rekap-dosen', $data);
    }

    public function exportPdf(Request $request, RekapDosenService $service)
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Dosen tidak ditemukan atau tidak terhubung dengan akun.');
        }

        $rekapData = $service->getRekapDosen($dosen->id);

        $data = [
            'nip' => $dosen->nip,
            'nama' => $dosen->nama,
            'prodi' => $dosen->prodi->jenjang . ' ' . $dosen->prodi->nama_prodi,
            'dataPresensi' => $rekapData['rekap'],
            'totalPertemuan' => $rekapData['totalPertemuan'],
        ];

        $pdf = Pdf::loadView('dosen.export.rekap-dosen-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Rekap Kehadiran Dosen.pdf');
    }

    public function exportExcel(Request $request, RekapDosenService $service)
    {
        $dosen = Auth::user()->dosen;

        $rekapData = $service->getRekapDosen($dosen->id);

        $totalPertemuan = $rekapData['totalPertemuan'] ?? 16;

        $export = new class($dosen, $rekapData, $totalPertemuan) implements FromView {

            protected $dosen;
            protected $rekapData;
            protected $totalPertemuan;

            public function __construct($dosen, $rekapData, $totalPertemuan)
            {
                $this->dosen = $dosen;
                $this->rekapData = $rekapData;
                $this->totalPertemuan = $totalPertemuan;
            }

            public function view(): View
            {
                return view('dosen.export.rekap-dosen-excel', [
                    'nip' => $this->dosen->nip,
                    'nama' => $this->dosen->nama,
                    'prodi' => $this->dosen->prodi->jenjang . ' ' . $this->dosen->prodi->nama_prodi,
                    'dataPresensi' => $this->rekapData['rekap'],
                    'totalPertemuan' => $this->totalPertemuan,
                ]);
            }
        };
        return Excel::download($export, 'Rekap Kehadiran Dosen.xlsx');
    }

    public function getFilterRekap(Request $request, RekapDosenService $service)
    {
        $data['title'] = 'Rekap Dosen';
        $data['judul'] = 'Rekap Dosen';
        $data['dosen'] = Dosen::all();
        $data['prodi'] = Prodi::all();
        $data['dosenTerpilih'] = Auth::user()->dosen;
        $data['tahun'] = TahunAjaran::orderBy('tahun_awal')->get();
        $data['rekap'] = [];
        $data['totalPertemuan'] = 16;

        $hasil = $service->getRekap($data['dosenTerpilih']->id, $request->tahun_ajaran);
        $data['rekap'] = $hasil['rekap'];
        $data['totalPertemuan'] = $hasil['totalPertemuan'];

        return response()->json($data);
    }
}
