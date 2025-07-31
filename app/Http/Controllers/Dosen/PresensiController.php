<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Requests\Admin\StorePresensi;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Pertemuan;
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
        $dosen = Auth::user()->dosen;
        $title = 'Data Presensi';
        $presensi = Presensi::with('dosen','pertemuan')->orderByDesc('tgl_presensi')->orderBy('jam_awal')->where('dosen_id',$dosen->id)->get();
        return view('dosen.presensi', compact('presensi','title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Data Perkuliahan';
        $prodi = Prodi::all();
        $ruangan = Ruangan::all();
        $matkul = Matkul::all();
        $dosen = Dosen::all();
        return view('dosen.form-presensi', compact('title','prodi','ruangan','matkul','dosen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePresensi $request)
    {
        try {
            $tahunAjaranAktif = TahunAjaran::where('status',  true)->first();
            $dosen = Auth::user()->dosen;

            if (!$tahunAjaranAktif) {
                return back()->withErrors(['tahun_ajaran_id' => 'Tahun ajaran aktif tidak ditemukan.']);
            }

                $result =  DB::transaction(function () use ($request, $dosen, $tahunAjaranAktif) {

                    $conflictPertemuan = Pertemuan::where('prodi_id', $request['prodi_id'])
                    ->where('semester', $request['semester'])
                    ->where('matkul_id', $request['matkul_id'])
                    ->where('pertemuan_ke', $request['pertemuan_ke'])
                    ->exists();

                    if ($conflictPertemuan) {
                        return back()->withInput()->withErrors(['pertemuan_ke' => 'Pertemuan Ke '. $request->pertemuan_ke .' sudah ada untuk Mata Kuliah Tersebut']);
                    }

                    if ($request->status === 'aktif'){

                    $conflictRuangan = Presensi::where('tgl_presensi',$request['tgl_presensi'])
                    ->where('ruangan_id', $request['ruangan_id'])
                    ->where(function($query) use ($request){
                    $query->where(function ($q) use ($request) {
                        $q->where('jam_awal', '<=', $request['jam_awal'])
                        ->where('jam_akhir', '>', $request['jam_awal']);
                    })->orWhere(function ($q) use ($request) {
                        $q->where('jam_awal', '<', $request['jam_akhir'])
                        ->where('jam_akhir', '>=', $request['jam_akhir']);
                    })->orWhere(function ($q) use ($request) {
                        $q->where('jam_awal', '>=', $request['jam_awal'])
                        ->where('jam_akhir', '<=', $request['jam_akhir']);
                    });
                })->exists();

            if ($conflictRuangan) {
                return back()->withInput()->withErrors(['ruangan_id' => 'Ruangan sedang dipakai pada waktu tersebut.'])->withInput();
            }

            $conflictDosen = Presensi::where('tgl_presensi', $request['tgl_presensi'])
                ->where('dosen_id', $request->dosen_id)
                ->where(function ($query) use ($request){
                    $query->where(function ($q) use ($request){
                        $q->where('jam_awal', '<=', $request->jam_awal)
                        ->where('jam_akhir', '>', $request->jam_awal);
                    })->orWhere(function ($q) use ($request) {
                        $q->where('jam_awal', '<', $request->jam_akhir)
                        ->where('jam_akhir', '>=', $request->jam_akhir);
                    });
                })->exists();

            if ($conflictDosen) {
                return back()->withInput()->withErrors(['dosen_id' => 'Dosen sedang mengajar pada waktu tersebut.'])->withInput();
            }

            $conflictJadwal = Presensi::where('tgl_presensi', $request['tgl_presensi'])
                ->whereHas('pertemuan', function ($query) use ($request) {
                    $query->where('prodi_id', $request['prodi_id'])
                        ->where('semester', $request['semester']);
                })
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('jam_awal', '<=', $request['jam_awal'])
                        ->where('jam_akhir', '>', $request['jam_awal']);
                    })->orWhere(function ($q) use ($request) {
                        $q->where('jam_awal', '<', $request['jam_akhir'])
                        ->where('jam_akhir', '>=', $request['jam_akhir']);
                    })->orWhere(function ($q) use ($request) {
                        $q->where('jam_awal', '>=', $request['jam_awal'])
                        ->where('jam_akhir', '<=', $request['jam_akhir']);
                    });
                })->exists();

            if ($conflictJadwal) {
                return back()->withInput()->withErrors(['semester' => 'Jadwal bentrok untuk prodi dan semester yang dipilih.'])->withInput();
            }
        }

                if (in_array($request->status, ['uts', 'uas'])) {
                $conflictUjian = Pertemuan::where('prodi_id', $request['prodi_id'])
                    ->where('semester', $request['semester'])
                    ->where('matkul_id', $request['matkul_id'])
                    ->where('status', $request['status'])
                    ->exists();

                if ($conflictUjian) {
                    return back()->withInput()->withErrors([
                        'status' => 'Perkuliahan untuk ' . strtoupper($request->status) . ' sudah ada.'
                    ]);
                }
            }

            // if (in_array($request->status, ['uts', 'uas'])) {
            //     $sudahAda = Pertemuan::where('prodi_id', $request->prodi_id)
            //         ->where('semester', $request->semester)
            //         ->where('matkul_id', $request->matkul_id)
            //         ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            //         ->where('status', $request->status)
            //         ->exists();

            //     if ($sudahAda) {
            //         return back()->withInput()->withErrors([
            //             'status' => 'Pertemuan ' . strtoupper($request->status) . ' sudah pernah dibuat untuk matkul ini.'
            //         ]);
            //     }
            // }

            $mahasiswa = Mahasiswa::where('prodi_id', $request['prodi_id'])
                ->where('semester', $request['semester'])->get();

            if ($mahasiswa->isEmpty()) {
                return back()->withInput()->withErrors(['semester' => 'Tidak ada mahasiswa untuk prodi dan semester ini.']);
            }

            $pertemuan = Pertemuan::create([
                'pertemuan_ke' => $request['pertemuan_ke'],
                'jenis' => $request['jenis'],
                'prodi_id' => $request['prodi_id'],
                'semester' => $request['semester'],
                'matkul_id' => $request['matkul_id'],
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'status' => $request['status'],
            ]);

            $tahun = now()->format('y');
            $lastKode = Presensi::where('presensi_id', 'like', "TR{$tahun}%")
                ->orderByDesc('presensi_id')->first();

            $nextNumber = $lastKode ? (int)substr($lastKode->presensi_id, -5) + 1 : 1;
            $noTransaksi = 'TR' . $tahun . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $presensi = Presensi::create([
                'presensi_id' => $noTransaksi,
                'pertemuan_id' => $pertemuan->id,
                'tgl_presensi' => $request['tgl_presensi'],
                'jam_awal' => $request['jam_awal'],
                'jam_akhir' => $request['jam_akhir'],
                'dosen_id' => $dosen->id,
                'ruangan_id' => $request['ruangan_id'],
                'link_zoom' => $request['link_zoom'],
            ]);

            if ($request['status'] === 'aktif') {
                foreach ($mahasiswa as $mhs) {
                    DetailPresensi::create([
                        'presensi_id' => $presensi->id,
                        'mahasiswa_id' => $mhs->id,
                        'waktu_presensi' => null,
                        'status' => 0,
                        'alasan' => null,
                        'bukti' => null,
                    ]);
                }
            }
            return true;
        });

            if ($result !== true) {
                return $result;
            }

        return redirect()->route('dosen.presensi.index')->with([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan'
        ]);

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Presensi', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = 'Data Perkuliahan';
        $presensi = Presensi::with('dosen','pertemuan.prodi','ruangan','pertemuan.matkul','pertemuan.tahun')->findOrFail($id);
        $detail = DetailPresensi::with('mahasiswa')->where('presensi_id', $id)->get();
        return view('dosen.info-presensi', compact('title','presensi','detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Update Data Perkuliahan';
        $prodi = Prodi::all();
        $ruangan = Ruangan::all();
        $matkul = Matkul::all();
        $dosen = Dosen::all();
        $presensi = Presensi::with('dosen','pertemuan.prodi','ruangan','pertemuan.matkul','pertemuan.tahun')->findOrFail($id);
        return view('dosen.form-presensi', compact('title','prodi','ruangan','matkul','dosen','presensi'));
    }

    public function update(StorePresensi $request, $id){
        try {
            $dosen = Auth::user()->dosen;
            $tahunAjaranAktif = TahunAjaran::where('status',  true)->first();

            if (!$tahunAjaranAktif) {
                return back()->withErrors(['tahun_ajaran_id' => 'Tahun ajaran aktif tidak ditemukan.'])->withInput();
            };


            $result = DB::transaction(function () use ($request, $dosen, $id) {
                $presensi = Presensi::with('pertemuan')->findOrFail($id);
                $statusLama = $presensi->pertemuan->getOriginal('status');
                $statusBaru = $request->status;

                $conflictPertemuan = Pertemuan::where('prodi_id', $request['prodi_id'])
                ->where('semester', $request['semester'])
                ->where('matkul_id', $request['matkul_id'])
                ->where('pertemuan_ke', $request['pertemuan_ke'])
                ->where('id', '!=', $presensi->pertemuan->id)
                ->exists();

                if ($conflictPertemuan) {
                    return back()->withInput()->withErrors(['pertemuan_ke' => 'Pekuliahan untuk pertemuan tersebut sudah ada.']);
                }

                if (in_array($request->status, ['uts', 'uas'])) {
                    $conflictUjian = Pertemuan::where('prodi_id', $request['prodi_id'])
                        ->where('semester', $request['semester'])
                        ->where('matkul_id', $request['matkul_id'])
                        ->where('status', $request['status'])
                        ->where('id', '!=', $presensi->pertemuan->id)
                        ->exists();

                    if ($conflictUjian) {
                        return back()->withInput()->withErrors([
                            'status' => 'Perkuliahan untuk ' . strtoupper($request->status) . ' sudah ada.'
                        ]);
                    }
                }

                if ($statusLama === 'aktif' && $presensi->jam_awal) {

                    $now = now();
                    $presensiStart = Carbon::parse($presensi->tgl_presensi . ' ' . $presensi->jam_awal);

                    if ($now->gte($presensiStart)) {
                        return back()->withInput()->withErrors([
                            'tgl_presensi' => 'Perkuliahan tidak dapat diedit karena perkuliahan sedang berlangsung atau sudah selesai.'
                        ]);
                    }
                }

                if ($statusLama === 'aktif' && $statusBaru === 'aktif') {

                    $conflictRuangan = Presensi::where('tgl_presensi',$request['tgl_presensi'])
                    ->where('ruangan_id', $request['ruangan_id'])
                    ->where('id', '!=', $presensi->id)
                    ->where(function($query) use ($request){
                        $query->where(function ($q) use ($request) {
                            $q->where('jam_awal', '<=', $request['jam_awal'])
                            ->where('jam_akhir', '>', $request['jam_awal']);
                        })->orWhere(function ($q) use ($request) {
                            $q->where('jam_awal', '<', $request['jam_akhir'])
                            ->where('jam_akhir', '>=', $request['jam_akhir']);
                        })->orWhere(function ($q) use ($request) {
                            $q->where('jam_awal', '>=', $request['jam_awal'])
                            ->where('jam_akhir', '<=', $request['jam_akhir']);
                        });
                    })->exists();

                    if ($conflictRuangan) {
                        return back()->withInput()->withErrors(['ruangan_id' => 'Ruangan sedang dipakai pada waktu tersebut.']);
                    }

                    $conflictDosen = Presensi::where('tgl_presensi', $request['tgl_presensi'])
                        ->where('dosen_id', $request->dosen_id)
                        ->where('id', '!=', $presensi->id)
                        ->where(function ($query) use ($request){
                            $query->where(function ($q) use ($request){
                                $q->where('jam_awal', '<=', $request->jam_awal)
                                ->where('jam_akhir', '>', $request->jam_awal);
                            })->orWhere(function ($q) use ($request) {
                                $q->where('jam_awal', '<', $request->jam_akhir)
                                ->where('jam_akhir', '>=', $request->jam_akhir);
                            });
                        })->exists();

                    if ($conflictDosen) {
                        return back()->withInput()->withErrors(['dosen_id' => 'Dosen sedang mengajar pada waktu tersebut.']);
                    }
                    $presensi->pertemuan->update([
                        'pertemuan_ke' => $request->pertemuan_ke,
                        'status'       => $statusBaru,
                        'jenis'        => $request->jenis
                    ]);

                    $presensi->update([
                        'tgl_presensi' => $request->tgl_presensi,
                        'jam_awal'     => $request->jam_awal,
                        'jam_akhir'    => $request->jam_akhir,
                        'dosen_id'     => $dosen->id,
                        'ruangan_id'   => $request->ruangan_id,
                        'link_zoom'    => $request->link_zoom,
                    ]);

                }elseif ($statusLama !== 'aktif' && $statusBaru === 'aktif') {
                    $presensi->pertemuan->update([
                        'pertemuan_ke' => $request->pertemuan_ke,
                        'status'       => $statusBaru,
                        'jenis'        => $request->jenis
                    ]);

                    $presensi->update([
                        'tgl_presensi' => $request->tgl_presensi,
                        'jam_awal'     => $request->jam_awal,
                        'jam_akhir'    => $request->jam_akhir,
                        'dosen_id'     => $dosen->id,
                        'ruangan_id'   => $request->ruangan_id,
                        'link_zoom'    => $request->link_zoom,
                    ]);

                    DetailPresensi::where('presensi_id', $presensi->id)->delete();

                    $mahasiswa = Mahasiswa::where('prodi_id', $presensi->pertemuan->prodi_id)
                    ->where('semester', $presensi->pertemuan->semester)
                    ->get();

                    foreach ($mahasiswa as $mhs) {
                        DetailPresensi::create([
                            'presensi_id' => $presensi->id,
                            'mahasiswa_id' => $mhs->id,
                            'waktu_presensi' => null,
                            'status' => 0,
                            'alasan' => null,
                            'bukti' => null,
                        ]);
                    }

                }elseif (in_array($statusBaru, ['uts', 'uas'])) {
                    $presensi->pertemuan->update([
                        'pertemuan_ke' => $request->pertemuan_ke,
                        'status'       => $statusBaru,
                    ]);

                    $presensi->update([
                        'tgl_presensi' => $request->tgl_presensi,
                        'jam_awal'     => $request->jam_awal,
                        'jam_akhir'    => $request->jam_akhir,
                        'dosen_id'     => $dosen->id,
                        'ruangan_id'   => $request->ruangan_id,
                        'link_zoom'    => $request->link_zoom,
                    ]);

                    if ($statusLama === 'aktif') {
                        DetailPresensi::where('presensi_id', $presensi->id)->delete();
                    }

                }elseif($statusBaru === 'libur') {
                    $presensi->pertemuan->update([
                        'pertemuan_ke' => $request->pertemuan_ke,
                        'status'       => $statusBaru,
                        'jenis'        => null
                    ]);

                    $presensi->update([
                        'tgl_presensi' => $request->tgl_presensi,
                        'jam_awal'     => null,
                        'jam_akhir'    => null,
                        'ruangan_id'   => null,
                        'link_zoom'    => $request->link_zoom,
                    ]);
                    DetailPresensi::where('presensi_id', $presensi->id)->delete();
                }

                return true;
            });

            if ($result !== true) {
                return $result;
            }

            return redirect()->route('dosen.presensi.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Presensi', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat update data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDetailPresensi(Request $request)
    {
        try {
            DetailPresensi::where('mahasiswa_id', $request['mahasiswa_id'])
                ->where('presensi_id', $request['presensi_id'])
                ->update([
                    'status' => $request['status'],
                    'waktu_presensi' => $request['status'] == 1 ? now() : null,
                    'alasan' => $request['alasan'],
                ]);

            return redirect()->route('dosen.presensi.show',$request['presensi_id'])->with([
                'status' => 'success',
                'message' => 'Data Berhasil Diubah'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal mengubah Presensi', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $presensi = Presensi::with('pertemuan')->findOrFail($id);
            $presensi->pertemuan->delete();

            return redirect()->route('dosen.presensi.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Hapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Hapus Presensi', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

        public function validateField(Request $request)
    {
        $rules = (new StorePresensi())->rules();
        $messages = (new StorePresensi())->messages();
        $field = $request->input('field');
        $value = $request->input('value');

        $validator = Validator::make([$field => $value], [
            $field => $rules[$field] ?? '',
        ],$messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first($field)], 422);
        }

        return response()->json(['success' => true]);
    }

}
