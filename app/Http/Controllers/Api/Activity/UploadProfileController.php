<?php

namespace App\Http\Controllers\Api\activity;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadProfileController extends Controller
{
    public function uploadProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:dosen,mahasiswa',
            'dosen_id' => 'required_if:role,dosen|nullable|exists:dosens,id',
            'mahasiswa_id' => 'required_if:role,mahasiswa|nullable|exists:mahasiswas,id',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ], [
            'file.mimes' => 'File harus berupa JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran maksimum file adalah 2 MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $role = $request->role;
        $id = $role === 'dosen' ? $request->dosen_id : $request->mahasiswa_id;
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $filename = "profile_{$id}.{$ext}";

        // Hapus SEMUA file lama (gunakan path Storage)
        $directory = "profiles/{$role}"; // Simpan di storage/app/public/profiles/
        $oldFiles = Storage::files($directory); // Otomatis merujuk ke storage/app/public/

        foreach ($oldFiles as $oldFile) {
            if (preg_match("/^profile_{$id}\.(jpg|jpeg|png)$/i", basename($oldFile))) {
                Storage::delete($oldFile); // Hapus dari storage
            }
        }

        // Simpan file
        $storedPath = $file->storeAs("profiles/{$role}", $filename, 'public');

        // Simpan ke DB hanya path relatif tanpa /storage
        $fotoPath = $storedPath;

        if ($role === 'dosen') {
            Dosen::where('id', $id)->update(['foto' => $fotoPath]);
        } else {
            Mahasiswa::where('id', $id)->update(['foto' => $fotoPath]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Gambar berhasil diunggah',
            'data' => ['foto' => $fotoPath]
        ]);
    }
}
