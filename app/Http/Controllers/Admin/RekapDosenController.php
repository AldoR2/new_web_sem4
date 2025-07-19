<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use App\Services\RekapDosenService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Facades\Excel;

class RekapDosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Rekap Dosen';
        $judul = 'Rekap Dosen';
        $dosen = Dosen::all();
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        $dosenTerpilih = $request->dosen ? Dosen::find($request->dosen) : null;
        $tahunTerpilih = $request->tahun_ajaran ? TahunAjaran::find($request->tahun_ajaran) : null;
        $rekap = [];
        $totalPertemuan = 16;
        return view('admin.rekap-dosen', compact('title','judul','dosen','dosenTerpilih','tahunTerpilih','tahun','rekap','totalPertemuan'));
    }

    public function exportPdf(Request $request, RekapDosenService $service)
    {
        try {
            $dosen = Dosen::findOrFail($request->dosen);
            $data = [
                'nip' => $dosen->nip,
                'nama' => $dosen->nama,
                'rekap' => [],
                'totalPertemuan' => 16,
            ];

            if ($request->isMethod('post')) {

            $hasil = $service->getRekap($request->dosen, $request->tahun_ajaran);
                $data['dataPresensi'] = $hasil['rekap'];
                $data['totalPertemuan'] = $hasil['totalPertemuan'];
            }

            $pdf = Pdf::loadView('admin.export.rekap-dosen-pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download('Rekap Kehadiran Dosen.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function exportExcel(Request $request, RekapDosenService $service)
    {

        $dosen = $request->dosen;
        $tahunAjaran = $request->tahun_ajaran;

        $rekapData = $service->getRekap($dosen, $tahunAjaran);

        $export = new class($rekapData, $dosen, $tahunAjaran) implements FromView {
            protected  $rekapData, $dosenId, $tahunId;

            public function __construct($rekapData, $dosenId, $tahunId)
            {
                $this->rekapData = $rekapData;
                $this->dosenId = $dosenId;
                $this->tahunId = $tahunId;
            }

            public function view(): View
            {
                return view('admin.export.rekap-dosen-excel', [
                    'nip' => Dosen::find($this->dosenId)?->nip ?? '-',
                    'nama' => Dosen::find($this->dosenId)?->nama ?? '-',
                    'dataPresensi' => $this->rekapData['rekap'],
                    'totalPertemuan' => $this->rekapData['totalPertemuan'],
                ]);
            }
        };

        return Excel::download($export, 'Rekap Kehadiran Dosen.xlsx');
    }

    public function rekapDosen(Request $request, RekapDosenService $service)
    {
        $data['title'] = 'Rekap Dosen';
        $data['judul'] = 'Rekap Dosen';
        $data['dosen'] = Dosen::all();
        $data['prodi'] = Prodi::all();
        $data['dosenTerpilih'] = Dosen::findOrFail($request->dosen);
        $data['tahunTerpilih'] = TahunAjaran::findOrFail($request->tahun_ajaran);
        $data['tahun'] = TahunAjaran::orderBy('tahun_awal')->get();
        $data['rekap'] = [];
        $data['totalPertemuan'] = 16;

        if ($request->isMethod('post')) {
            $hasil = $service->getRekap($request->dosen, $request->tahun_ajaran);
            $data['rekap'] = $hasil['rekap'];
            $data['totalPertemuan'] = $hasil['totalPertemuan'];
        }

        return view('admin.rekap-dosen', $data);
    }
}
