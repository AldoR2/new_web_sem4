<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Jadwal;
use App\Models\KalenderAkademik;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function countByMahasiswa($mahasiswaId)
    {
        // 1. Ambil data mahasiswa
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

        // 2. Ambil tahun ajaran yang aktif
        $tahunAjaranAktif = TahunAjaran::where('status', 1)->first();

        // 3. Hitung total presensi unik per matkul
        $presensiIds = DetailPresensi::where('mahasiswa_id', $mahasiswaId)->pluck('presensi_id');
        $matkulUnik = Presensi::whereIn('id', $presensiIds)
            ->distinct('matkul_id')
            ->count('matkul_id');

        // 4. Hitung jumlah jadwal sesuai prodi dan semester dari mahasiswa, pada tahun ajaran aktif
        $jumlahJadwal = Jadwal::where('prodi_id', $mahasiswa->prodi_id)
            ->where('semester', $mahasiswa->semester)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->count();

        // 5. Hitung jumlah data kalender akademik
        $jumlahKalender = KalenderAkademik::count();

        // 7. Presensi yang sedang berlangsung
        $presensiBerlangsung = Presensi::where('tgl_presensi', now()->toDateString())
            ->whereTime('jam_awal', '<=', now()->format('H:i:s'))
            ->whereTime('jam_akhir', '>=', now()->format('H:i:s'))
            ->whereHas('detailPresensi', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                    ->where('status', 0); // Tambahkan filter status = 0
            })
            ->count();

        // 8. Link Zoom yang tersedia hari ini
        $linkZoomHariIni = Presensi::where('tgl_presensi', now()->toDateString())
            ->whereNotNull('link_zoom')
            ->whereHas('detailPresensi', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId);
            })
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Jumlah item berhasil ditampilkan',
            'data' => [
                'total_kehadiran' => $matkulUnik,
                'presensi_berlangsung' => $presensiBerlangsung,
                'jumlah_jadwal_aktif' => $jumlahJadwal,
                'jumlah_kalender_akademik' => $jumlahKalender,
                'jumlah_presensi_online' => $linkZoomHariIni,
            ]
        ]);
    }

    public function countByDosen($dosenId)
    {
        $today = Carbon::today();

        // 1. Jumlah seluruh mahasiswa
        $jumlahMahasiswa = Mahasiswa::count();

        // 2. Presensi hari ini dengan link_zoom
        $presensiHariIniDenganZoom = Presensi::whereDate('tgl_presensi', $today)
            ->where('dosen_id', $dosenId)
            ->whereNotNull('link_zoom')
            ->count();

        // 3. Jadwal aktif yang diajar oleh dosen
        $tahunAjaranAktif = TahunAjaran::where('status', 1)->first();
        $jumlahJadwalAktif = Jadwal::where('dosen_id', $dosenId)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id ?? 0)
            ->count();

        // 4. Jumlah kalender akademik
        $jumlahKalenderAkademik = KalenderAkademik::count();

        return response()->json([
            'status' => 'success',
            'message' => 'Jumlah item berhasil',
            'data' => [
                'jumlah_mahasiswa' => $jumlahMahasiswa,
                'presensi_hari_ini' => $presensiHariIniDenganZoom,
                'jumlah_jadwal_aktif' => $jumlahJadwalAktif,
                'jumlah_kalender_akademik' => $jumlahKalenderAkademik,
                'perkuliahan_online_hari_ini' => $presensiHariIniDenganZoom,
            ]
        ]);
    }
}
