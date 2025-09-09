<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Requests\Admin\StorePresensi;
use App\Http\Requests\Admin\UpdatePresensi;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Pertemuan;
use App\Models\Prodi;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Auth;
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
        $title = 'Tambah Data Perkuliahan';
        $prodi = Prodi::all();
        $ruangan = Ruangan::all();
        $dosen = Dosen::all();
        return view('dosen.form-presensi', compact('title','prodi','ruangan','dosen'));
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

                $mahasiswa = Mahasiswa::where('prodi_id', $request['prodi_id'])
                    ->where('semester', $request['semester'])->get();

                foreach ($request->inputs as $i => $value) {
                    $jamAwal   = $value['jam_awal'] ?? null;
                    $jamAkhir  = $value['jam_akhir'] ?? null;
                    $pertemuanKe = $value['pertemuan_ke'] ?? null;
                    $jenis       = $value['jenis'] ?? null;
                    $tglPresensi = $value['tgl_presensi'] ?? null;

                $data =([
                    'pertemuan_ke' => $pertemuanKe,
                    'prodi_id' => $request['prodi_id'],
                    'semester' => $request['semester'],
                    'matkul_id' => $request['matkul_id'],
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'status' => $request['status'],
                ]);

                if ($request->status === 'aktif') {
                    $data['jenis'] = $jenis;
                }

                $pertemuan = Pertemuan::create($data);

                $tahun = now()->format('y');
                $lastKode = Presensi::where('presensi_id', 'like', "TR{$tahun}%")->lockForUpdate()
                    ->orderByDesc('presensi_id')->first();

                $nextNumber = $lastKode ? (int)substr($lastKode->presensi_id, -5) + 1 : 1;
                $noTransaksi = 'TR' . $tahun . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

                $presensi = Presensi::create([
                    'presensi_id' => $noTransaksi,
                    'pertemuan_id' => $pertemuan->id,
                    'tgl_presensi' => $tglPresensi,
                    'jam_awal' => $jamAwal,
                    'jam_akhir' => $jamAkhir,
                    'dosen_id' => $dosen->id,
                    'ruangan_id' => $request['ruangan_id'],
                    'link_zoom' => $request['link_zoom'],
                ]);

                if ($request['status'] !== 'libur') {
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

    public function update(UpdatePresensi $request, $id){
        try {
            $dosen = Auth::user()->dosen;
            $tahunAjaranAktif = TahunAjaran::where('status',  true)->first();

            if (!$tahunAjaranAktif) {
                return back()->withErrors(['tahun_ajaran_id' => 'Tahun ajaran aktif tidak ditemukan.'])->withInput();
            };


            $result = DB::transaction(function () use ($request, $dosen, $id) {
                $presensi = Presensi::with('pertemuan')->findOrFail($id);
                $statusBaru = $request->status;

                if ($statusBaru !== 'libur') {
                    $data = ([
                        'pertemuan_ke' => $request->pertemuan_ke,
                        'status'       => $statusBaru,
                    ]);

                    if ($statusBaru === 'aktif') {
                        $data['jenis'] = $request->jenis;
                    }

                    $presensi->pertemuan->update($data);

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

                } else {
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
