<?php
namespace App\Services;

use App\Models\Pertemuan;
use App\Models\Presensi;

class RekapDosenService
{

    public function getRekap($dosenId, $tahunAjaranId)
    {
        $pertemuans = Pertemuan::with([
            'presensi' => function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            },
            'matkul', 'prodi', 'tahun','presensi.dosen'
        ])->where('tahun_ajaran_id', $tahunAjaranId)
        ->whereHas('presensi', function ($q) use ($dosenId) {
            $q->where('dosen_id', $dosenId);
        })
        ->orderBy('pertemuan_ke')
        ->get();

        $rekap = [];
        $defaultPertemuan = 16;
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);

        foreach ($pertemuans->groupBy('matkul_id') as $grouped) {
            $first = $grouped->first();

            $hadir = [];
            foreach ($grouped as $p) {
                $jenis = strtolower($p->status);

                switch ($jenis) {
                    case 'libur':
                        $hadir[$p->pertemuan_ke] = '-';
                        break;
                    case 'uts':
                        $hadir[$p->pertemuan_ke] = 'UTS';
                        break;
                    case 'uas':
                        $hadir[$p->pertemuan_ke] = 'UAS';
                        break;
                    case 'aktif':
                        $hadir[$p->pertemuan_ke] = 'M';
                        break;
                    default:
                        $hadir[$p->pertemuan_ke] = '-';
                        break;
                }
            }

            $tanggal_pertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $tanggal_pertemuan[] = $hadir[$i] ?? '-';
            }

            $totalAktif = $grouped->filter(function ($p) {
                return strtolower($p->status) === 'aktif';
            })->count();

            $persentaseAktif = $totalPertemuan > 0 ? round(($totalAktif / $totalPertemuan) * 100, 2) . '%' : '0%';

            $rekap[] = [
                'kode_matkul' => $first->matkul->kode_matkul,
                'nama_matkul' => $first->matkul->nama_matkul,
                'nama_prodi' => $first->prodi->nama_prodi,
                'semester' => $first->semester,
                'nama_dosen' => optional($first->presensi->first())->dosen->nama ?? '-',
                // 'total_pertemuan' => $persentaseAktif,
                'total_pertemuan' => $totalAktif,
                'status_pertemuan' => $tanggal_pertemuan,
            ];
        }

    return [
        'rekap' => $rekap,
        'totalPertemuan' => $totalPertemuan
    ];
}

        public function getRekapDosen($dosenId)
    {
        $defaultPertemuan = 16;

        $pertemuans = Pertemuan::with([
            'presensi' => function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            },
            'matkul', 'prodi', 'tahun','presensi.dosen'
        ])
        ->whereHas('presensi', function ($q) use ($dosenId) {
            $q->where('dosen_id', $dosenId);
        })
        ->orderBy('pertemuan_ke')
        ->get();

        $rekap = [];
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);


        foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
            $first = $grouped->first();

            $hadir = [];
            foreach ($grouped as $p) {
                $jenis = strtolower($p->status);

                switch ($jenis) {
                    case 'libur':
                        $hadir[$p->pertemuan_ke] = '-';
                        break;
                    case 'uts':
                        $hadir[$p->pertemuan_ke] = 'UTS';
                        break;
                    case 'uas':
                        $hadir[$p->pertemuan_ke] = 'UAS';
                        break;
                    case 'aktif':
                    default:
                        $hadir[$p->pertemuan_ke] = 'M';
                        break;
                }
            }

            $tanggal_pertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $tanggal_pertemuan[] = $hadir[$i] ?? '-';
            }

            $totalAktif = $grouped->filter(function ($p) {
                return strtolower($p->status) === 'aktif';
            })->count();

            $rekap[] = [
                'kode_matkul' => $first->matkul->kode_matkul,
                'nama_matkul' => $first->matkul->nama_matkul,
                'nama_prodi' => $first->prodi->nama_prodi,
                'semester' => $first->semester,
                'nama_dosen' => $first->presensi?->dosen->nama ?? '-',
                'total_pertemuan' => $totalAktif,
                'status_pertemuan' => $tanggal_pertemuan,
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $totalPertemuan
        ];
    }

    public function getFilterRekapDosen($dosenId, $prodiId, $tahunAjaranId)
    {
        $pertemuans = Pertemuan::with([
            'presensi' => function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            },
            'matkul', 'prodi', 'tahun','presensi.dosen'
        ])
        ->whereHas('presensi', function ($q) use ($dosenId) {
            $q->where('dosen_id', $dosenId);
        })
        ->where('prodi_id', $prodiId)
        ->where('tahun_ajaran_id', $tahunAjaranId)
        ->orderBy('pertemuan_ke')
        ->get();

        $rekap = [];
        $defaultPertemuan = 16;
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);

        foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
            $first = $grouped->first();

            $hadir = [];
            foreach ($grouped as $p) {
                // $hadir[$p->pertemuan_ke] = 'M';
                $jenis = strtolower($p->status);

                switch ($jenis) {
                    case 'libur':
                        $hadir[$p->pertemuan_ke] = '-';
                        break;
                    case 'uts':
                        $hadir[$p->pertemuan_ke] = 'UTS';
                        break;
                    case 'uas':
                        $hadir[$p->pertemuan_ke] = 'UAS';
                        break;
                    case 'aktif':
                    default:
                        $hadir[$p->pertemuan_ke] = 'M';
                        break;
                }
            }

            $tanggal_pertemuan = [];
            for ($i = 1; $i <= $totalPertemuan; $i++) {
                $tanggal_pertemuan[] = $hadir[$i] ?? '-';
            }

            $totalAktif = $grouped->filter(function ($p) {
                return strtolower($p->status) === 'aktif';
            })->count();

            $rekap[] = [
                'kode_matkul' => $first->matkul->kode_matkul,
                'nama_matkul' => $first->matkul->nama_matkul,
                'nama_prodi' => $first->prodi->nama_prodi,
                'semester' => $first->semester,
                'nama_dosen' => $first->presensi?->dosen->nama ?? '-',
                'total_pertemuan' => $totalAktif,
                'status_pertemuan' => $tanggal_pertemuan,
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $totalPertemuan
        ];
    }
}
