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

class MahasiswaController extends Controller
{
    public function jadwal()
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

        // $rekapData = $service->getRekapDosen($dosen->id);
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
            // 'tahun_ajaran' => $tahunAjaran ? "{$tahunAjaran->tahun_awal}/{$tahunAjaran->tahun_akhir}" : '-',
            'jadwal' => $jadwal,
            // 'totalPertemuan' => $rekapData['totalPertemuan'],
        ];

        $pdf = Pdf::loadView('mahasiswa.export.jadwal-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download('Jadwal Mahasiswa.pdf');
    }

    public function exportJadwalExcel(Request $request,)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $tahunId = $request->query('tahun_ajaran');
        $tahunAjaran = $tahunId ? TahunAjaran::find($tahunId) : TahunAjaran::where('status', true)->first();

        // $rekapData = $service->getRekapDosen($dosen->id);
        $jadwal = Jadwal::with(['prodi','dosen','ruangan','tahun','matkul','detailJadwal' => function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        }])->whereHas('detailJadwal', function ($q) use ($mahasiswa){
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->when($tahunAjaran, function ($q) use ($tahunAjaran){
            $q->where('tahun_ajaran_id', $tahunAjaran->id);
        })->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam')->get();
        // $totalPertemuan = $rekapData['totalPertemuan'] ?? 16;

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

    public function rekap(Request $request, RekapMahasiswaService $service){
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

        public function updateProfil(Request $request)
    {
        try {
            $mahasiswa = $request->user()->mahasiswa;

            $request->validate([
                'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                    Storage::disk('public')->delete($mahasiswa->foto);
                }

                // Simpan foto baru
                $filename = 'mahasiswa/profile_' . $mahasiswa->id . '.' . $request->file('foto')->extension();
                $fotoPath = $request->file('foto')->storeAs('profiles', $filename, 'public');
                $mahasiswa->update(['foto' => $fotoPath]);
            }

            return redirect()->route('mahasiswa.dashboard')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Perbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Perbarui Profile', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ]);
        }
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

    public function prosesPresensi(Request $request){
        $rfid = strtoupper($request->query('rfid'));
        $mahasiswa = Mahasiswa::where('rfid', $rfid)->first();

        if (!$mahasiswa) {
            return response()->json(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        $now = Carbon::now();

        try {
            $presensi = Presensi::whereDate('tgl_presensi', Carbon::today())
                ->where(function ($q) {
                    $q->whereNull('link_zoom')->orWhere('link_zoom','');
                })
                ->whereTime('jam_awal', '<=', $now)
                ->whereTime('jam_akhir', '>=', $now)
                ->first();

            if (!$presensi) {
                return response()->json(['status' => 'error', 'message' => 'Tidak ada presensi aktif saat ini'], 404);
            }

            $tglPresensi = $presensi->tgl_presensi;
            $jamAwal = $presensi->jam_awal;
            $jamAkhir = $presensi->jam_akhir;

            $timeMulai = Carbon::parse("$tglPresensi $jamAwal");
            $timeBerakhir = Carbon::parse("$tglPresensi $jamAkhir");

            if ($now->lt($timeMulai)) {
                return response()->json(['status' => 'error', 'message' => 'Absensi belum dimulai']);
            } elseif ($now->gt($timeBerakhir)) {
                return response()->json(['status' => 'error', 'message' => 'Absensi sudah kadaluarsa']);
            }

            DetailPresensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('presensi_id', $presensi->id)
                ->update([
                    'waktu_presensi' => now(),
                    'status' => 1,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Presensi berhasil']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal update presensi'], 404);
            }
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
