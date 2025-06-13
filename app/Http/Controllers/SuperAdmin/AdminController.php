<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Imports\AdminImport;
use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\StoreMasterAdmin;
use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Validation\ValidationException;



class AdminController extends Controller
{
    public function index()
    {
        $title = 'Data Admin';
        $admin = Admin::with( ['provinsi','kota','kecamatan','kelurahan'])->orderByDesc('id')->get();
        return view('superadmin.admin',compact('title','admin'));
    }

    public function create()
    {
        return view('superadmin.form-admin',['title' =>'Tambah Data']);

    }

    public function store(StoreMasterAdmin $request)
    {
        $request->merge([
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
                    'role' => 'admin',
                    'password' => Hash::make('password123'),
                ]);

                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $filename = 'admin/profile_' . $user->id . '.' . $request->file('foto')->extension();
                    $fotoPath = $request->file('foto')->storeAs( 'profiles',$filename,'public');
                }

                Admin::create([
                    'user_id' => $user->id,
                    'nama' => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'foto' => $fotoPath,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan_id' => $request->kecamatan_id,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);
            });


            return redirect()->route('superadmin.master-admin.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Admin', [
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
            $admin = Admin::with('provinsi','kota','kecamatan','kelurahan')->findOrFail($id);

            return response()->json( $admin);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function edit(string $id)
    {
        $title = 'Edit Data Admin';
        $admin = Admin::findOrFail($id);
        return view('superadmin.form-admin', compact('title', 'admin'));
    }

    public function update(StoreMasterAdmin $request, $id)
    {
        $request->merge([
            'nama' => ucwords(trim($request->nama)),
            'tempat_lahir' => ucwords(trim($request->tempat_lahir)),
            'email' => strtolower(trim($request->email)),
            'no_telp' => trim($request->no_telp),
            'alamat' => ucwords(trim($request->alamat)),
            'password' => trim($request->new_password),
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
            $admin = Admin::findOrFail($id);
            $user = $admin->user;

            if ($request->hasFile('foto')) {
                // Hapus foto lama kalau ada
                if ($admin->foto && Storage::disk('public')->exists($admin->foto)) {
                    Storage::disk('public')->delete($admin->foto);
                }

                $filename = 'admin/profile_' . $user->id . '.' . $request->file('foto')->extension();
                $fotoPath = $request->file('foto')->storeAs('profiles',$filename, 'public');
                $admin->foto = $fotoPath;
            }

            $admin->update([
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'foto' => $admin->foto,
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


            return redirect()->route('superadmin.master-admin.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal Perbarui Admin', [
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
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect()->route('superadmin.master-admin.index')->with([
            'status' => 'success',
            'message' => 'Data Berhasil Dihapus'
        ]);
    }

    public function validateField(Request $request)
    {
        $id = $request->input('id');
        $rules = (new StoreMasterAdmin())->rules($id);
        $messages = (new StoreMasterAdmin())->messages();
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

    public function import(Request $request){
        try {
            $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(new AdminImport, $request->file('file'));

            return redirect()->route('superadmin.master-admin.index')->with([
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
