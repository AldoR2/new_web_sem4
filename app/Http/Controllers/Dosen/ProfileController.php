<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $title = "Profil";
        $user = $request->user()->load(['dosen.provinsi', 'dosen.kota', 'dosen.kecamatan', 'dosen.kelurahan']);

        return view('dosen.profil', compact('title', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $dosen = $request->user()->dosen;

            $request->validate([
                'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                    Storage::disk('public')->delete($dosen->foto);
                }

                // Simpan foto baru
                $filename = 'dosen/profile_' . $dosen->id . '.' . $request->file('foto')->extension();
                $fotoPath = $request->file('foto')->storeAs('profiles', $filename, 'public');
                $dosen->update(['foto' => $fotoPath]);
            }

            return redirect()->route('dosen.profile.edit')->with([
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

}
