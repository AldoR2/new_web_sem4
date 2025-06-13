<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\StoreMasterTahun;
use Illuminate\Support\Facades\Validator;


class TahunAjaranController extends Controller
{
    public function index()
    {
        $title = 'Data Tahun Ajaran';
        $tahun = TahunAjaran::orderBy('tahun_awal')->get();
        return view('admin.master_data.tahunAjaran',compact('title','tahun'));
    }

    public function create()
    {
        return view('admin.master_data.form-tahunAjaran',['title' =>'Tambah Data']);
    }

    public function store(StoreMasterTahun $request)
    {

        $request->merge([
            'tahun_awal' => trim($request->tahun_awal),
            'tahun_akhir' => trim($request->tahun_akhir),
        ]);

        if ($request->tahun_awal >= $request->tahun_akhir) {
            return redirect()->back()->withInput()->withErrors([
                'tahun_awal' => 'Tahun Awal harus lebih kecil dari Tahun Akhir.',
            ]);
        }

        try {
            TahunAjaran::create($request->only(['tahun_awal', 'tahun_akhir', 'keterangan']));

            return redirect()->route('admin.master-tahun.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Tambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal tambah Tahun Ajaran', [
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
        $title = 'Edit Tahun Ajaran';
        $tahun = TahunAjaran::findOrFail($id);
        return view('admin.master_data.form-tahunAjaran', compact('title', 'tahun'));
    }

    public function update(StoreMasterTahun $request, $id)
    {
        $request->merge([
            'tahun_awal' => trim($request->tahun_awal),
            'tahun_akhir' => trim($request->tahun_akhir),
        ]);

        if ($request->tahun_awal >= $request->tahun_akhir) {
            return redirect()->back()->withInput()->withErrors([
                'tahun_awal' => 'Tahun Awal harus lebih kecil dari Tahun Akhir.',
            ]);
        }

        try {
            $tahun = TahunAjaran::findOrFail($id);

            if ($tahun->status == 1 && $request->status == 0) {
                $tahunAktif = TahunAjaran::where('status', 1)->count();

                if ($tahunAktif <= 1) {
                    return redirect()->back()->withInput()->withErrors([
                        'tahun_awal' => 'Tidak bisa menonaktifkan. Minimal harus ada 1 Tahun Ajaran yang aktif.',
                    ]);
                }
            }

            if ($request->status == 1) {
                TahunAjaran::where('status', 1)->update(['status' => 0]);
            }

            $tahun->update($request->only(['tahun_awal', 'tahun_akhir', 'keterangan', 'status']));

            return redirect()->route('admin.master-tahun.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Perbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Perbarui Tahun Ajaran', [
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
            $tahun = TahunAjaran::findOrFail($id);
            $tahun->delete();

            return redirect()->route('admin.master-tahun.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal Menghapus Data', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function validateField(Request $request)
    {
        $rules = (new StoreMasterTahun())->rules();
        $messages = (new StoreMasterTahun())->messages();
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
