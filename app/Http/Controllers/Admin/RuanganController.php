<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreMasterRuangan;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class RuanganController extends Controller
{
    public function index()
    {
        $title = 'Data Ruangan';
        $ruangan = Ruangan::orderByDesc('id')->get();
        return view('admin.master_data.ruangan', compact('title', 'ruangan'));
    }

    public function create()
    {
        $title = 'Data Ruangan';
        return view('admin.master_data.form-ruangan', compact('title'));
    }

    public function store(StoreMasterRuangan $request)
    {
        $request->merge([
            'nama_ruangan' =>ucwords(trim($request->nama_ruangan)),
        ]);

        try {
            Ruangan::create($request->only([ 'nama_ruangan']));

            return redirect()->route('admin.master-ruangan.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Di Tambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Tambah Data', [
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
        $title = 'Edit Ruangan';
        $ruangan = Ruangan::findOrFail($id);
        return view('admin.master_data.form-ruangan', compact('title', 'ruangan'));
    }

    public function update(StoreMasterRuangan $request, string $id)
    {
        $request->merge([
            'nama_ruangan' =>ucwords(trim($request->nama_ruangan)),
        ]);

        try {
            $ruangan = Ruangan::findOrFail($id);

            $ruangan->update($request->only(['nama_ruangan']));

            return redirect()->route('admin.master-ruangan.index')->with([
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

            $ruangan = Ruangan::findOrFail($id);
            $ruangan->delete();
            return redirect()->route('admin.master-ruangan.index')->with([
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
                'message' => 'Terjadi kesalahan saat Hapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function validateField(Request $request)
    {
        {
            $id = $request->input('id');
            $rules = (new StoreMasterRuangan())->rules($id);
            $messages = (new StoreMasterRuangan())->messages();
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
}
