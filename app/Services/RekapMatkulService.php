<?php
namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\DetailPresensi;
use Auth;
use Illuminate\Support\Facades\Log;

class RekapMatkulService
{
    private function getStatusPertemuan($pertemuans, $defaultPertemuan = 16)
    {
        $status = [];
        foreach ($pertemuans as $p) {
            $jenis = strtolower($p->status);
            switch ($jenis) {
                case 'libur':
                    $status[$p->pertemuan_ke] = '-';
                    break;
                case 'uts':
                    $status[$p->pertemuan_ke] = 'UTS';
                    break;
                case 'uas':
                    $status[$p->pertemuan_ke] = 'UAS';
                    break;
                default:
                    $status[$p->pertemuan_ke] = 'M';
                    break;
            }
        }

        for ($i = 1; $i <= $defaultPertemuan; $i++) {
            if (!isset($status[$i])) {
                $status[$i] = '-';
            }
        }

        return $status;
    }

    public function getRekapMatkul($prodiId, $semester, $matkulId)
    {
        $pertemuans = Pertemuan::with(['presensi.detailPresensi.mahasiswa','presensi.dosen', 'matkul', 'prodi', 'tahun'])
            ->where('prodi_id', $prodiId)
            ->where('semester', $semester)
            ->where('matkul_id', $matkulId)
            ->orderBy('pertemuan_ke')
            ->get();

        $rekap = [];
        $defaultPertemuan = 16;
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);
        $totalMahasiswa = Mahasiswa::where('prodi_id', $prodiId)->where('semester', $semester)->count();

        for ($i = 1; $i <= $totalPertemuan; $i++) {
            $pertemuan = $pertemuans->firstWhere('pertemuan_ke', $i);
            $presensi = $pertemuan?->presensi;

            $rekap[] = [
                'pertemuan_ke' => $i,
                'tanggal' => $presensi?->tgl_presensi ?? '-',
                'totalMahasiswa' => $totalMahasiswa,
                'metode' => $presensi ? ($presensi->link_zoom ? 'Daring' : 'Luring') : '-' ,
                'dosen' => $presensi?->dosen?->nama ?? '-',
                'jumlah_hadir' => $presensi?->detailPresensi ? $presensi->detailPresensi->where('status', 1)->unique('mahasiswa_id')->count() : 0,
            ];
        }

        $firstPertemuan = $pertemuans->first();

        return [
            'matkul'        => $firstPertemuan?->matkul?->nama_matkul ?? '-',
            'tahun_ajaran'  => $firstPertemuan?->tahun?->tahun ?? '-',
            'rekap'         => $rekap,
        ];
    }
}
