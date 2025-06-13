<?php
namespace App\Services;

use App\Models\Pertemuan;
use App\Models\Presensi;

class RekapDosenService
{


    public function getRekap($dosenId, $tahunAjaranId)
{
    $defaultPertemuan = 16;

        $pertemuans = Pertemuan::with([
        'presensi' => function ($q) use ($dosenId) {
            $q->where('dosen_id', $dosenId);
        },
        'matkul', 'prodi', 'tahun','presensi.dosen'
    ])
    ->where('tahun_ajaran_id', $tahunAjaranId)
    ->whereHas('presensi', function ($q) use ($dosenId) {
        $q->where('dosen_id', $dosenId);
    })
    ->orderBy('pertemuan_ke')
    ->get();

    $rekap = [];
    $maxPertemuan = $pertemuans->count();

    foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
        $first = $grouped->first();

        $hadir = [];
        foreach ($grouped as $p) {
            // $hadir[$p->pertemuan_ke] = 'M';
            $jenis = strtolower($p->status); // pastikan kolom ini ada

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
                    $hadir[$p->pertemuan_ke] = 'M'; // Dosen hadir
                    break;
            }
        }

        $tanggal_pertemuan = [];
        for ($i = 1; $i <= $defaultPertemuan; $i++) {
            $tanggal_pertemuan[] = $hadir[$i] ?? '-';
        }

        $rekap[] = [
            'kode_matkul' => $first->matkul->kode_matkul,
            'nama_matkul' => $first->matkul->nama_matkul,
            'nama_prodi' => $first->prodi->nama_prodi,
            'semester' => $first->semester,
            'nama_dosen' => optional($first->presensi->first())->dosen->nama ?? '-',
            'total_pertemuan' => count($grouped),
            'status_pertemuan' => $tanggal_pertemuan,
        ];
    }

    return [
        'rekap' => $rekap,
        'totalPertemuan' => $defaultPertemuan, $maxPertemuan
    ];
}





    // public function getRekap($dosenId, $tahunAjaranId)
    // {
    //     $pertemuans = Pertemuan::with(['presensi' => function ($q) use ($dosenId) {
    //         $q->where('dosen_id', $dosenId);
    //     }, 'matkul', 'prodi', 'tahun'])
    //     ->where('tahun_ajaran_id', $tahunAjaranId)
    //     ->orderBy('pertemuan_ke')
    //     ->get();

    //     $rekap = [];
    //     $maxPertemuan = $pertemuans->count();
    //     $defaultPertemuan = 16;

    //     foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
    //         $tanggal = [];

    //         $sorted = $grouped->sortBy('pertemuan_ke')->values();

    //         foreach ($sorted as $record) {
    //             $tanggal[] = $record['pertemuan_ke'];
    //         }

    //     // Simpan jumlah max pertemuan tertinggi
    //     if (count($tanggal) > $maxPertemuan) {
    //         $maxPertemuan = count($tanggal);
    //     }

    //     // Pastikan selalu 16 kolom (atau lebih jika ada pertemuan tambahan)
    //     $tanggal = array_pad($tanggal, max($defaultPertemuan, count($tanggal)), null);

    //     $first = $grouped->first();


    //         // $tanggal = $grouped->pluck('pertemuan_ke')->sort()->values();
    //         $rekap[] = [
    //             'kode_matkul' => $grouped->first()->matkul->kode_matkul,
    //             'nama_matkul' => $grouped->first()->matkul->nama_matkul,
    //             'nama_prodi' => $grouped->first()->prodi->nama_prodi,
    //             'semester' => $grouped->first()->semester,
    //             'nama_dosen' => optional($grouped->first()->presensi->first())->dosen->nama ?? '-',
    //             'total_pertemuan' => count(array_filter($tanggal)),
    //             'tanggal_pertemuan' => $tanggal,
    //         ];

    //         // if ($tanggal->count() > $maxPertemuan) {
    //         //     $maxPertemuan = $tanggal->count();
    //         // }
    //     }

    //     return [
    //         'rekap' => $rekap,
    //         'totalPertemuan' => max(16, $maxPertemuan),
    //     ];
    // }

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
        $maxPertemuan = $pertemuans->count();


        foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
            $first = $grouped->first();

            $hadir = [];
            foreach ($grouped as $p) {
                // $hadir[$p->pertemuan_ke] = 'M';
                $jenis = strtolower($p->status); // pastikan kolom ini ada

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
                        $hadir[$p->pertemuan_ke] = 'M'; // Dosen hadir
                        break;
                }
            }

            $tanggal_pertemuan = [];
            for ($i = 1; $i <= $defaultPertemuan; $i++) {
                $tanggal_pertemuan[] = $hadir[$i] ?? '-';
            }

            $rekap[] = [
                'kode_matkul' => $first->matkul->kode_matkul,
                'nama_matkul' => $first->matkul->nama_matkul,
                'nama_prodi' => $first->prodi->nama_prodi,
                'semester' => $first->semester,
                'nama_dosen' => optional($first->presensi->first())->dosen->nama ?? '-',
                'total_pertemuan' => count($grouped),
                'status_pertemuan' => $tanggal_pertemuan,
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $defaultPertemuan, $maxPertemuan
        ];
    }

    public function getFilterRekapDosen($dosenId, $prodiId, $tahunAjaranId)
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
        ->where('prodi_id', $prodiId)
        ->where('tahun_ajaran_id', $tahunAjaranId)
        ->orderBy('pertemuan_ke')
        ->get();

        // $presensis = Presensi::with(['dosen', 'matkul', 'prodi', 'tahunAjaran'])
        //     ->where('dosen_id', $dosenId)
        //     ->where('prodi_id', $prodiId)
        //     ->where('tahun_ajaran_id', $tahunAjaranId)
        //     ->orderBy('tgl_presensi')
        //     ->get();

        $rekap = [];
        $maxPertemuan = $pertemuans->count();

        foreach ($pertemuans->groupBy('matkul_id') as $matkulId => $grouped) {
            $first = $grouped->first();

            $hadir = [];
            foreach ($grouped as $p) {
                // $hadir[$p->pertemuan_ke] = 'M';
                $jenis = strtolower($p->status); // pastikan kolom ini ada

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
                        $hadir[$p->pertemuan_ke] = 'M'; // Dosen hadir
                        break;
                }
            }

            $tanggal_pertemuan = [];
            for ($i = 1; $i <= $defaultPertemuan; $i++) {
                $tanggal_pertemuan[] = $hadir[$i] ?? '-';
            }

            $rekap[] = [
                'kode_matkul' => $first->matkul->kode_matkul,
                'nama_matkul' => $first->matkul->nama_matkul,
                'nama_prodi' => $first->prodi->nama_prodi,
                'semester' => $first->semester,
                'nama_dosen' => optional($first->presensi->first())->dosen->nama ?? '-',
                'total_pertemuan' => count($grouped),
                'status_pertemuan' => $tanggal_pertemuan,
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $defaultPertemuan, $maxPertemuan
        ];
    }
}
