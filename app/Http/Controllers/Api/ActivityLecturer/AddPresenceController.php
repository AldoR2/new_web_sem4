<?php

namespace App\Http\Controllers\Api\ActivityLecturer;

use App\Models\Dosen;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Services\FcmV1Service;
use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Mahasiswa;
use App\Models\Matkul;
use App\Models\Presensi;
use App\Models\Prodi;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddPresenceController extends Controller
{
    public function uploadPresence(Request $request)
    {
        try {
            $request->validate([
                'presensi_id' => 'required|string',
                'tgl_presensi' => 'required|date',
                'jam_awal' => 'required',
                'jam_akhir' => 'required',
                'dosen_id' => 'required|integer',
                'prodi_id' => 'required|integer',
                'semester' => 'required|integer',
                'matkul_id' => 'required|integer',
                'tahun_ajaran_id' => 'required|integer',
                'link_zoom' => 'required|string',
            ]);

            // 1. Simpan data presensi utama
            $presensi = Presensi::create([
                'presensi_id' => $request->presensi_id,
                'tgl_presensi' => $request->tgl_presensi,
                'jam_awal' => $request->jam_awal,
                'jam_akhir' => $request->jam_akhir,
                'dosen_id' => $request->dosen_id,
                'prodi_id' => $request->prodi_id,
                'semester' => $request->semester,
                'matkul_id' => $request->matkul_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'link_zoom' => $request->link_zoom,
            ]);

            // 2. Ambil mahasiswa berdasarkan prodi dan semester
            $mahasiswas = Mahasiswa::where('prodi_id', $request->prodi_id)
                ->where('semester', $request->semester)
                ->get();

            // 3. Simpan ke detail presensi
            foreach ($mahasiswas as $mahasiswa) {
                DetailPresensi::create([
                    'presensi_id' => $presensi->id,
                    'mahasiswa_id' => $mahasiswa->id,
                ]);
            }

            // Ambil data dosen & user
            $dosen = Dosen::with('user')->findOrFail($request->dosen_id);
            $user = $dosen->user;
            $matkul = Matkul::find($request->matkul_id);

            $waktu = Carbon::now()->locale('id')->timezone('Asia/Jakarta');
            $tanggal = $waktu->translatedFormat('d F Y');
            $jam = $waktu->format('H.i');

            // Simpan notifikasi
            Notification::create([
                'user_id' => $user->id,
                'title' => $presensi ? 'Presensi Berhasil Ditambahkan!' : 'Presensi Gagal Ditambahkan!',
                'message' => $presensi ? 'Presensi Anda berhasil direkam.' : 'Presensi Anda gagal direkam.',
                'type' => $presensi ? 'presensiBerhasil' : 'presensiGagal',
                'nama_user' => $dosen->nama,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'mata_kuliah' => $matkul?->nama_matkul ?? '-',
            ]);

            // 4. Kirim notifikasi ke mahasiswa
            $fcmService = new FcmV1Service();

            // Kirim notifikasi ke dosen
            $dosenUserId = $dosen->user_id;
            $dosenTokens = FcmToken::where('user_id', $dosenUserId)->pluck('token');

            foreach ($dosenTokens as $token) {
                $fcmService->send(
                    $token,
                    'Presensi Telah Ditambahkan',
                    'Presensi untuk kelas online sudah berhasil ditambahkan.'
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data presensi berhasil diunggah dan notifikasi telah dikirim.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function showMajors(Request $request)
    {
        $prodis = Prodi::select('id', 'nama_prodi')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data prodi berhasil ditampilkan',
            'data' => $prodis
        ]);
    }
    public function showMatkuls(Request $request)
    {
        $request->validate([
            'prodi_id' => 'required|integer',
            'semester' => 'required|integer',
        ]);

        $matkuls = Matkul::where('prodi_id', $request->prodi_id)
            ->where('semester', $request->semester)
            ->whereHas('tahunAjaran', function ($query) {
                $query->where('status', 1);
            })
            ->select('id as id_matkul', 'kode_matkul', 'nama_matkul')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data matkul berhasil ditampilkan',
            'data' => $matkuls
        ]);
    }
    public function showTahunAjarans(Request $request)
    {
        $tahunAjaran = TahunAjaran::where('status', 1)->select('id', 'tahun_awal', 'tahun_akhir', 'keterangan')->first();

        if ($tahunAjaran) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data tahun ajaran aktif berhasil ditampilkan',
                'data' => $tahunAjaran
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada tahun ajaran aktif yang ditampilkan'
            ]);
        }
    }
}
