<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $title = "Profil";
        $user = $request->user()->load(['admin.provinsi', 'admin.kota', 'admin.kecamatan', 'admin.kelurahan']);

        return view('admin.profil', compact('title', 'user'));
    }

    public function update(Request $request){
        try {
            $admin = $request->user()->admin;

            $request->validate([
                'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($admin->foto && Storage::disk('public')->exists($admin->foto)) {
                    Storage::disk('public')->delete($admin->foto);
                }

                // Simpan foto baru
                $filename = 'admin/profile_' . $admin->id . '.' . $request->file('foto')->extension();
                $fotoPath = $request->file('foto')->storeAs('profiles', $filename, 'public');
                $admin->update(['foto' => $fotoPath]);
            }

            return redirect()->route('admin.profile.edit')->with([
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

    public function changePassword(){
        $title = "Ganti Password";
        return view('admin.changePassword', compact('title'));
    }

    public function validateField(Request $request)
    {
        $rules = (new UpdatePasswordRequest())->rules();
        $messages = (new UpdatePasswordRequest())->messages();
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
