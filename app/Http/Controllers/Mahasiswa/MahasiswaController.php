<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function updateProfil(Request $request)
    {
        try {
            $mahasiswa = $request->user()->mahasiswa;

            $request->validate([
                'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                    Storage::disk('public')->delete($mahasiswa->foto);
                }

                $filename = 'mahasiswa/profile_' . $mahasiswa->id . '.' . $request->file('foto')->extension();
                $fotoPath = $request->file('foto')->storeAs('profiles', $filename, 'public');
                $mahasiswa->update(['foto' => $fotoPath]);
            }

            return redirect()->route('mahasiswa.dashboard')->with([
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

    public function prosesPresensi(Request $request){
        $rfid = strtoupper($request->query('rfid'));
        $mahasiswa = Mahasiswa::where('rfid', $rfid)->first();

        if (!$mahasiswa) {
            return response()->json(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan'], 404);
        }
        $now = Carbon::now();

        try {
            $presensi = Presensi::whereDate('tgl_presensi', Carbon::today())
                ->where(function ($q) {
                    $q->whereNull('link_zoom')->orWhere('link_zoom','');
                })
                ->whereTime('jam_awal', '<=', $now)
                ->whereTime('jam_akhir', '>=', $now)
                ->first();

            if (!$presensi) {
                return response()->json(['status' => 'error', 'message' => 'Tidak ada presensi aktif saat ini'], 404);
            }

            $tglPresensi = $presensi->tgl_presensi;
            $jamAwal = $presensi->jam_awal;
            $jamAkhir = $presensi->jam_akhir;

            $timeMulai = Carbon::parse("$tglPresensi $jamAwal");
            $timeBerakhir = Carbon::parse("$tglPresensi $jamAkhir");

            if ($now->lt($timeMulai)) {
                return response()->json(['status' => 'error', 'message' => 'Absensi belum dimulai']);
            } elseif ($now->gt($timeBerakhir)) {
                return response()->json(['status' => 'error', 'message' => 'Absensi sudah kadaluarsa']);
            }

            DetailPresensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('presensi_id', $presensi->id)
                ->update([
                    'waktu_presensi' => now(),
                    'status' => 1,
                ]);

            return response()->json(['status' => 'success', 'message' => 'Presensi berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal update presensi'], 404);
        }
    }

}
