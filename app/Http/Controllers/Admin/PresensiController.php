<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StorePresensi;
use App\Models\DetailPresensi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Pertemuan;
use App\Models\Prodi;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class PresensiController extends Controller
{
    public function index()
    {
        $title = 'Data Perkuliahan';
        $presensi = Presensi::with('dosen','pertemuan.prodi','ruangan','pertemuan.matkul')->orderByDesc('tgl_presensi')->orderBy('jam_awal')->get();
        // $pertemuan = Pertemuan::with('prodi','matkul','presensi')->get();
        // $presensi = Pertemuan::with(['prodi','matkul','presensi' => function ($q){
        //     $q->orderByDesc('tgl_presensi')->orderBy('jam_awal');
        // }])->get();
        return view('admin.presensi', compact('presensi','title'));
    }

    public function create()
    {
        $title = 'Tambah Data Perkuliahan';
        $prodi = Prodi::all();
        $ruangan = Ruangan::all();
        $matkul = Matkul::all();
        $dosen = Dosen::all();
        return view('admin.form-presensi', compact('title','prodi','ruangan','matkul','dosen'));
    }

    public function store(StorePresensi $request)
    {
        try {
            $tahunAjaranAktif = TahunAjaran::where('status', operator: true)->first();

            if (!$tahunAjaranAktif) {
                return back()->withErrors(['tahun_ajaran_id' => 'Tahun ajaran aktif tidak ditemukan.'])->withInput();
            };

            $result = DB::transaction(function () use ($request, $tahunAjaranAktif) {


                // $duplikat = Pertemuan::where('matkul_id', $request->matkul_id)
                //     ->where('pertemuan_ke', $request->pertemuan_ke)
                //     ->where('prodi_id', $request->prodi_id)
                //     ->where('semester', $request->semester)
                //     ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                //     ->exists();

                // if ($duplikat) {
                //     return back()->withErrors(['pertemuan_ke' => 'Pertemuan ke-' . $request->pertemuan_ke . ' sudah ada untuk matkul ini.'])->withInput();
                // }

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

        $pertemuan = Pertemuan::firstOrCreate([
            'pertemuan_ke' => $request['pertemuan_ke'],
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
            'dosen_id' => $request['dosen_id'],
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

            return redirect()->route('admin.presensi.index')->with([
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

    public function show(string $id)
    {
        $title = 'Detail Data Perkuliahan';
        $presensi = Presensi::with('dosen','pertemuan.prodi','ruangan','pertemuan.matkul','pertemuan.tahun')->findOrFail($id);
        $detail = DetailPresensi::with('mahasiswa')->where('presensi_id', $id)->get();
        return view('admin.info-presensi', compact('title','presensi','detail'));
    }

    public function update(StorePresensi $request, $id){
        try {
            $tahunAjaranAktif = TahunAjaran::where('status', operator: true)->first();

            if (!$tahunAjaranAktif) {
                return back()->withErrors(['tahun_ajaran_id' => 'Tahun ajaran aktif tidak ditemukan.'])->withInput();
            };


            $result = DB::transaction(function () use ($request, $tahunAjaranAktif, $id) {
            $presensi = Presensi::with('pertemuan')->findOrFail($id);

            $now = now();
            $presensiStart = \Carbon\Carbon::parse($presensi->tgl_presensi . ' ' . $presensi->jam_awal);

            if ($now->gte($presensiStart)) {
                return back()->withInput()->withErrors([
                    'jam_awal' => 'Perkuliahan tidak dapat diedit karena perkuliahan sedang berlangsung atau sudah selesai.'
                ]);
            }

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
            return back()->withInput()->withErrors(['ruangan_id' => 'Ruangan sedang dipakai pada waktu tersebut.'])->withInput();
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
            return back()->withInput()->withErrors(['dosen_id' => 'Dosen sedang mengajar pada waktu tersebut.'])->withInput();
        }

        $statusLama = $presensi->pertemuan->getOriginal('status');

        $presensi->pertemuan->update([
            'pertemuan_ke' => $request['pertemuan_ke'],
            'status' => $request['status'],
        ]);

        $statusBaru = $request->status;

        // Hapus jika dari aktif → libur/uts/uas
        if ($statusLama === 'aktif' && in_array($statusBaru, ['libur','uts','uas'])) {
            DetailPresensi::where('presensi_id', $presensi->id)->delete();
        }

        // Tambah jika dari libur/uts/uas → aktif
        if (in_array($statusLama, ['libur', 'uts', 'uas']) && $statusBaru === 'aktif') {
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
        }

        $presensi->update([
            'tgl_presensi' => $request['tgl_presensi'],
            'jam_awal' => $request['jam_awal'],
            'jam_akhir' => $request['jam_akhir'],
            'dosen_id' => $request['dosen_id'],
            'ruangan_id' => $request['ruangan_id'],
            'link_zoom' => $request['link_zoom'],
        ]);
        return true;
    });

        if ($result !== true) {
            return $result;
        }

            return redirect()->route('admin.presensi.index')->with([
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

    public function edit(string $id)
    {
        $title = 'Update Data Perkuliahan';
        $prodi = Prodi::all();
        $ruangan = Ruangan::all();
        $matkul = Matkul::all();
        $dosen = Dosen::all();
        $presensi = Presensi::with('dosen','pertemuan.prodi','ruangan','pertemuan.matkul','pertemuan.tahun')->findOrFail($id);
        return view('admin.form-presensi', compact('title','prodi','ruangan','matkul','dosen','presensi'));
    }

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

            return redirect()->route('admin.presensi.show',$request['presensi_id'])->with([
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

    public function destroy(string $id)
    {
        try {
        // jika data presensi yang ingin dihapus
            // $presensi = Presensi::findOrFail($id);
            // $presensi->delete();

            // $presensi = Presensi::findOrFail($id);
            // $presensi->delete();

        DB::transaction(function () use ($id) {
            $presensi = Presensi::findOrFail($id);
            $pertemuanId = $presensi->pertemuan_id;

            $jumlahPresensi = Presensi::where('pertemuan_id', $pertemuanId)->count();

                        // Jika hanya ada satu, artinya ini yang terakhir → hapus pertemuan langsung
            if ($jumlahPresensi === 1) {
                $presensi->delete();
                Pertemuan::find($pertemuanId)?->delete();
            } else {
                // Masih ada presensi lain → hanya hapus presensi ini saja
                $presensi->delete();
            }
        });

            // Hapus semua presensi di pertemuan ini
        //     Presensi::where('pertemuan_id', $id)->delete();

        //     // Setelah presensi dihapus, pastikan sudah benar-benar kosong
        //     if (Presensi::where('pertemuan_id', $id)->count() === 0) {
        //         Pertemuan::find($id)?->delete();
        //     }
        // });

            return redirect()->route('admin.presensi.index')->with([
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
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function getMatkulByProdi(Request $request)
    {
        $prodi = $request->query('prodi');
        $semester = $request->query('semester');

        $tahunAjaranAktif = TahunAjaran::where('status',  true)->first();

        $query = Matkul::query()->where('tahun_ajaran_id', $tahunAjaranAktif->id);

        if ($prodi) {
            $query->where('prodi_id', $prodi);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        $matkul = $query->get(['id', 'nama_matkul']);

        return response()->json($matkul);
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
