<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\StoreMasterMahasiswa;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index()
    {
        $title = 'Data Mahasiswa';
        $prodi = Prodi::all();
        $mahasiswa = Mahasiswa::with( ['prodi', 'tahun','provinsi','kota','kecamatan','kelurahan'])->orderByDesc('id')->get();

        return view('admin.master_data.mahasiswa',compact('title','prodi','mahasiswa'));
    }
    public function create()
    {
        $title = 'Tambah Data';
        $prodi = Prodi::all();
        return view('admin.master_data.form-mahasiswa', compact('title','prodi'));
    }

    public function store(StoreMasterMahasiswa $request)
    {
        $request->merge([
            'nim' => strtoupper(trim($request->nim)),
            'rfid' => trim($request->rfid),
            'nama' => ucwords(trim($request->nama)),
            'tempat_lahir' => ucwords(trim($request->tempat_lahir)),
            'email' => strtolower(trim($request->email)),
            'no_telp' => trim($request->no_telp),
            'alamat' => ucwords(trim($request->alamat)),
            'tahun_masuk' => trim($request->tahun_masuk),
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->nama,
                    'nim' => $request->nim,
                    'role' => 'mahasiswa',
                    'password' => Hash::make($request->nim),
                ]);

                $fotoPath = null;
                if ($request->hasFile('foto')) {
                    $filename = 'mahasiswa/profile_' . $request->nim . '.' . $request->file('foto')->extension();
                    $fotoPath = $request->file('foto')->storeAs( 'profiles', $filename, 'public');
                }

                $tahunAjaranAktif = TahunAjaran::where('status', true)->first();

                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'nama' => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'prodi_id' => $request->prodi_id,
                    'tahun_masuk' => $request->tahun_masuk,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'semester' => $request->semester,
                    'foto' => $fotoPath,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan_id' => $request->kecamatan_id,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);
            });

            return redirect()->route('admin.master-mahasiswa.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Mahasiswa', [
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
            $mahasiswa = Mahasiswa::with( ['prodi', 'tahun','provinsi','kota','kecamatan','kelurahan'])->findOrFail($id);
            return response()->json($mahasiswa);

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
        $mahasiswa = Mahasiswa::findOrFail($id);
        $title = 'Edit Data';
        return view('admin.master_data.form-mahasiswa', compact('mahasiswa','prodi', 'title'));
    }

    public function update(StoreMasterMahasiswa $request, $id)
    {
        $request->merge([
            'nim' => strtoupper(trim($request->nim)),
            'rfid' => trim($request->rfid),
            'nama' => ucwords(trim($request->nama)),
            'tempat_lahir' => ucwords(trim($request->tempat_lahir)),
            'email' => strtolower(trim($request->email)),
            'no_telp' => trim($request->no_telp),
            'alamat' => ucwords(trim($request->alamat)),
            'tahun_masuk' => trim($request->tahun_masuk),
            'password' => trim($request->new_password),
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $mahasiswa = Mahasiswa::findOrFail($id);
                $user = $mahasiswa->user;

                if ($request->hasFile('foto')) {
                    if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                        Storage::disk('public')->delete($mahasiswa->foto);
                    }

                    // Simpan foto baru
                    $filename = 'mahasiswa/profile_' . $request->nim . '.' . $request->file('foto')->extension();
                    $fotoPath = $request->file('foto')->storeAs('profiles',$filename, 'public');
                    $mahasiswa->foto = $fotoPath;
                }

                $tahunAjaranAktif = TahunAjaran::where('status', true)->first();

                $mahasiswa->update([
                    'nim' => $request->nim,
                    'rfid' => $request->rfid,
                    'nama' => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'prodi_id' => $request->prodi_id,
                    'tahun_masuk' => $request->tahun_masuk,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'semester' => $request->semester,
                    'foto' => $mahasiswa->foto,
                    'provinsi_id' => $request->provinsi_id,
                    'kota_id' => $request->kota_id,
                    'kecamatan_id' => $request->kecamatan_id,
                    'kelurahan_id' => $request->kelurahan_id,
                ]);

                $userData =[
                    'name' => $request->nama,
                    'nim' => $request->nim,
                ];

                if ($request->filled('new_password')) {
                    $userData['password'] = Hash::make($request->new_password);
                }

                $user->update($userData);
            });

            return redirect()->route('admin.master-mahasiswa.index')->with([
                'status' => 'success',
                'message' => 'Data Berhasil Diperbarui'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui Mahasiswa', [
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
            $mahasiswa = Mahasiswa::findOrFail($id);
            $mahasiswa->delete();

            return redirect()->route('admin.master-mahasiswa.index')->with([
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

    public function getFilterMahasiswa(Request $request){
        $prodi = $request->query('prodi');
        $semester = $request->query('semester');

        $query = Mahasiswa::query()->with('prodi');

        if ($prodi) {
            $query->where('prodi_id', $prodi);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        $mahasiswa = $query->get();

        return response()->json($mahasiswa);
    }

    public function validateField(Request $request)
    {
        $id = $request->input('id');
        $rules = (new StoreMasterMahasiswa())->rules($id);
        $messages = (new StoreMasterMahasiswa())->messages();
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

        Excel::import(new MahasiswaImport, $request->file('file'));

            return redirect()->route('admin.master-mahasiswa.index')->with([
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
