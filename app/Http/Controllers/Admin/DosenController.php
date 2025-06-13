<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\DosenImport;
use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\StoreMasterDosen;
use Maatwebsite\Excel\Facades\Excel;


class DosenController extends Controller
{
    public function index()
    {
        $title = 'Data Dosen';
        $dosen = Dosen::with( 'prodi')->orderByDesc('id')->get();
        return view('admin.master_data.dosen',compact('title','dosen'));

    }

    public function create()
    {
        $prodi = Prodi::all();
        $title = 'Tambah Data';
        return view('admin.master_data.form-dosen', compact('prodi', 'title'));
    }

    public function store(StoreMasterDosen $request)
    {
        $request->merge([
            'nip' => trim($request->nip),
            'nama' => ucwords(trim($request->nama)),
            'tempat_lahir' => ucwords(trim($request->tempat_lahir)),
            'email' => strtolower(trim($request->email)),
            'no_telp' => trim($request->no_telp),
            'alamat' => ucwords(trim($request->alamat)),
        ]);

        try {

            DB::transaction(function () use ($request) {

                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'role' => 'dosen',
                    'password' => Hash::make('password123'),
                ]);

                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $filename = 'dosen/profile_' . $request->nip . '.' . $request->file('foto')->extension();
                    $fotoPath = $request->file('foto')->storeAs( 'profiles', $filename, 'public');
                }

                Dosen::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip,
                    'nama' => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'prodi_id' => $request->prodi_id,
                    'foto' => $fotoPath,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan_id' => $request->kecamatan_id,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);
            });

            return redirect()->route('admin.master-dosen.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Menambahkan Dosen', [
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
        try {
            $dosen = Dosen::with('prodi','provinsi','kota','kecamatan','kelurahan')->findOrFail($id);

            return response()->json($dosen);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function edit(string $id)
    {
        $prodi = Prodi::all();
        $dosen = Dosen::findOrFail($id);
        $title = 'Edit Data';
        return view('admin.master_data.form-dosen', compact('dosen','prodi', 'title'));
    }

    public function update(StoreMasterDosen $request, $id)
    {
        $request->merge([
            'nip' => trim($request->nip),
            'nama' => trim($request->nama),
            'tempat_lahir' => trim($request->tempat_lahir),
            'email' => trim($request->email),
            'no_telp' => trim($request->no_telp),
            'alamat' => ucwords(trim($request->alamat)),
            'password' => trim($request->new_password),
        ]);

        try {

            DB::transaction(function () use($request, $id) {

                $dosen = Dosen::findOrFail($id);
                $user = $dosen->user;

                if ($request->hasFile('foto')) {
                    if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                        Storage::disk('public')->delete($dosen->foto);
                    }

                    $filename = 'dosen/profile_' . $request->nip . '.' . $request->file('foto')->extension();
                    $fotoPath = $request->file('foto')->storeAs('profiles', $filename, 'public');
                    $dosen->foto = $fotoPath;
                }

                $dosen->update([
                    'nip' => $request->nip,
                    'nama' => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'foto' => $dosen->foto,
                    'prodi_id' => $request->prodi_id,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan_id' => $request->kecamatan_id,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);

                $userData =[
                    'name' => $request->nama,
                    'email' => $request->email,
                ];

                if ($request->filled('new_password')) {
                    $userData['password'] = Hash::make($request->new_password);
                }

                $user->update($userData);
            });

            return redirect()->route('admin.master-dosen.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Memperbarui Dosen', [
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
            $dosen = Dosen::findOrFail($id);
            $dosen->delete();

            return redirect()->route('admin.master-dosen.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Menghapus Dosen', [
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
        $id = $request->input('id');
        $rules = (new StoreMasterDosen())->rules($id);
        $messages = (new StoreMasterDosen())->messages();
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

    public function filter(Request $request)
    {
        $query = Dosen::query();

        if ($request->prodi_id) {
            $query->where('prodi_id', $request->prodi_id);
        }

        return response()->json($query->get(['foto','nip','nama', 'email']));
    }

    public function import(Request $request){

        try {
            $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(new DosenImport, $request->file('file'));

            return redirect()->route('admin.master-dosen.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Diimpor'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Import Data', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import data: ' . $e->getMessage()
            ]);
        }
    }


}
