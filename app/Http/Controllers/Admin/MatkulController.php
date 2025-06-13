<?php

namespace App\Http\Controllers\Admin;

use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\StoreMasterMatkul;
use Illuminate\Support\Facades\Validator;


class MatkulController extends Controller
{
    public function index()
    {
        $title = 'Data Mata Kuliah';
        $prodi = Prodi::all();
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        $matkul = Matkul::with( ['prodi', 'tahunAjaran'])->orderByDesc('id')->get();

        return view('admin.master_data.matkul', compact('title','prodi','tahun', 'matkul'));
    }

    public function create()
    {
        $prodi = Prodi::all();
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        $title = 'Tambah Data';
        return view('admin.master_data.form-matkul', compact('prodi','tahun', 'title'));
    }

    public function store(StoreMasterMatkul $request)
    {
        $request->merge([
            'nama_matkul' => ucwords(trim($request->nama_matkul)),
            'durasi_matkul' => trim($request->durasi_matkul),
        ]);

        try {
            DB::transaction(function () use ($request) {
                $kodeMatkul = $this->generateKodeMatkul($request->prodi_id);

                Matkul::create([
                    'kode_matkul' => $kodeMatkul,
                    'nama_matkul' => $request->nama_matkul,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'semester' => $request->semester,
                    'durasi_matkul' => $request->durasi_matkul,
                    'prodi_id' => $request->prodi_id,
                ]);
            });

            return redirect()->route('admin.master-matkul.index')->with([
                'status' => 'success',
                'message' => 'Mata Kuliah Berhasil Ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Data', [
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
        //
    }

    public function edit(string $id)
    {
        $title = 'Update Data';
        $matkul = Matkul::findOrFail($id);
        $prodi = Prodi::all();
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        return view('admin.master_data.form-matkul', compact('matkul','prodi','tahun', 'title'));
    }

    public function update(StoreMasterMatkul $request, $id)
    {
        $request->merge([
            'nama_matkul' => ucwords(trim($request->nama_matkul)),
            'durasi_matkul' => trim($request->durasi_matkul),
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $matkul = Matkul::findOrFail($id);
                $matkul->update($request->only(['nama_matkul', 'tahun_ajaran_id', 'semester', 'durasi_matkul','prodi_id']));
            });

            return redirect()->route('admin.master-matkul.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Perbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Perbarui Data', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $matkul = Matkul::findOrFail($id);
            $matkul->delete();

            return redirect()->route('admin.master-matkul.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Hapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Hapus Data', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    private function generateKodeMatkul($id)
    {
        $prodi = Prodi::findOrFail($id);

        $kodeProdi = $prodi->kode_prodi;
        $tahunSekarang = now()->format('y');

        $lastKode = Matkul::where('kode_matkul', 'like', $kodeProdi . $tahunSekarang . '%')
            ->orderBy('kode_matkul', 'desc')
            ->first();

        if ($lastKode) {
            $lastNumber = (int)substr($lastKode->kode_matkul, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $kodeProdi . $tahunSekarang . $newNumber;
    }

    public function getFilterMatkul(Request $request){
        $prodi = $request->query('prodi');
        $semester = $request->query('semester');
        $tahun = $request->query('tahun_ajaran');

        $query = Matkul::query()->with('prodi','tahunAjaran');

        if ($prodi) {
            $query->where('prodi_id', $prodi);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($tahun) {
            $query->where('tahun_ajaran_id', $tahun);
        }

        $matkul = $query->get();

        return response()->json($matkul);
    }

    public function validateField(Request $request)
    {
        $rules = (new StoreMasterMatkul())->rules();
        $messages = (new StoreMasterMatkul())->messages();
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
