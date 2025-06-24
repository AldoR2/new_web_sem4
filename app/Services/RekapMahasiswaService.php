<?php
namespace App\Services;

use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\DetailPresensi;
use Auth;
use Illuminate\Support\Facades\Log;

class RekapMahasiswaService
{

//     public function getRekapMahasiswa($prodiId, $semester, $matkulId)
// {
//     $defaultPertemuan = 16;

//     // Ambil semua pertemuan, termasuk presensi & detail
//     $pertemuans = Pertemuan::with([
//         'presensi.detailPresensi.mahasiswa',
//         'presensi.dosen',
//         'matkul', 'prodi', 'tahun'
//     ])
//     ->where('prodi_id', $prodiId)
//     ->where('semester', $semester)
//     ->where('matkul_id', $matkulId)
//     ->orderBy('pertemuan_ke')
//     ->get()
//     ->keyBy('pertemuan_ke');

//     // Ambil semua mahasiswa yang pernah ikut presensi
//     $groupMahasiswa = collect();

//     foreach ($pertemuans as $ke => $pertemuan) {
//         foreach ($pertemuan->presensi as $presensi) {
//             foreach ($presensi->detailPresensi as $detail) {
//                 $groupMahasiswa[$detail->mahasiswa->nim][] = [
//                     'ke' => $ke,
//                     'status_pertemuan' => strtolower($pertemuan->status),
//                     'status' => $detail->status,
//                     'tgl_presensi' => $presensi->tgl_presensi,
//                     'nama_dosen' => $presensi->dosen->nama ?? '-',
//                     'mahasiswa' => $detail->mahasiswa,
//                     'matkul' => $pertemuan->matkul,
//                     'pertemuan_ke' => $pertemuan->pertemuan_ke,

//                     'prodi' => $pertemuan->prodi,
//                 ];
//             }
//         }
//     }

//     // Bangun rekap per mahasiswa
//     $rekap = [];

//     foreach ($groupMahasiswa as $nim => $presensis) {
//         $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
//         $pertemuanStatus = [];
//         $tanggalPertemuan = [];
//         $dosenPengajar = [];

//         // Ambil mahasiswa, matkul, prodi dari entri pertama
//         $first = $presensis[0];
//         $mahasiswa = $first['mahasiswa'];
//         $matkul = $first['matkul'];
//         $prodi = $first['prodi'];

//         // Inisialisasi semua pertemuan
//         for ($i = 1; $i <= $defaultPertemuan; $i++) {
//             $p = $pertemuans->get($i);
//             $jenis = strtolower($p->status ?? 'aktif');

//             $tanggalPertemuan[$i] = optional($p->presensi->first())->tgl_presensi ?? null;
//             $dosenPengajar[$i] = optional($p->presensi->first())->dosen->nama ?? '-';

//             if ($jenis === 'uts') {
//                 $pertemuanStatus[$i] = 'UTS';
//             } elseif ($jenis === 'uas') {
//                 $pertemuanStatus[$i] = 'UAS';
//             } elseif ($jenis === 'libur') {
//                 $pertemuanStatus[$i] = '-';
//             } else {
//                 $pertemuanStatus[$i] = 'A'; // default tidak hadir
//             }
//         }

//         // Override status pertemuan aktif dengan detail presensi
//         foreach ($presensis as $p) {

//             if ($p['status_pertemuan'] !== 'aktif') continue;

//             switch ($p['status']) {
//                 case 1: $pertemuanStatus[$p['pertemuan_ke']] = 'H'; $statusCount['hadir']++; break;
//                 case 2: $pertemuanStatus[$p['pertemuan_ke']] = 'I'; $statusCount['izin']++; break;
//                 case 3: $pertemuanStatus[$p['pertemuan_ke']] = 'S'; $statusCount['sakit']++; break;
//                 default: $pertemuanStatus[$p['pertemuan_ke']] = 'A'; $statusCount['alpha']++; break;
//             }
//         }

//         $total = array_sum($statusCount);

//         $rekap[$nim] = [
//             'nim' => $nim,
//             'nama_mahasiswa' => $mahasiswa->nama,
//             'semester' => $mahasiswa->semester,
//             'nama_prodi' => $prodi->nama_prodi ?? '-',
//             'kode_matkul' => $matkul->kode_matkul ?? '-',
//             'nama_matkul' => $matkul->nama_matkul ?? '-',
//             'nama_dosen' => $dosenPengajar,
//             'pertemuan' => $pertemuanStatus,
//             'tanggal_pertemuan' => $tanggalPertemuan,
//             'hadir' => $statusCount['hadir'],
//             'izin' => $statusCount['izin'],
//             'sakit' => $statusCount['sakit'],
//             'alpha' => $statusCount['alpha'],
//             'kehadiran' => $total > 0 ? round(($statusCount['hadir'] / $total) * 100) . '%' : '0%',
//             'izin_persentase' => $total > 0 ? round(($statusCount['izin'] / $total) * 100) . '%' : '0%',
//             'sakit_persentase' => $total > 0 ? round(($statusCount['sakit'] / $total) * 100) . '%' : '0%',
//             'alpha_persentase' => $total > 0 ? round(($statusCount['alpha'] / $total) * 100) . '%' : '0%',
//         ];
//     }

//     return [
//         'rekap' => $rekap,
//         'totalPertemuan' => $defaultPertemuan,
//     ];
// }







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







    public function getRekapMahasiswa($prodiId, $semester, $matkulId)
    {
        $pertemuans = Pertemuan::with(['presensi.detailPresensi.mahasiswa','presensi.dosen', 'matkul', 'prodi', 'tahun'])
            ->where('prodi_id', $prodiId)
            ->where('semester', $semester)
            ->where('matkul_id', $matkulId)
            ->orderBy('pertemuan_ke')
            ->get();

        $rekap = [];
        $maxPertemuan = $pertemuans->count();
        // $status_pertemuan = $this->getStatusPertemuan($grouped, $defaultPertemuan);
        $statusPertemuanMap = $pertemuans->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));



        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            return $pertemuan->presensi->flatMap(function ($presensi) use ($pertemuan) {
                return $presensi->detailPresensi->map(function($detail) use ($presensi,$pertemuan){
                    return [
                        'nim' => $detail->mahasiswa->nim,
                        'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
                        'semester' => $detail->mahasiswa->semester ?? '-',
                        'nama_prodi' => $pertemuan->prodi->nama_prodi ?? '-',
                        'kode_matkul' => $pertemuan->matkul->kode_matkul ?? '-',
                        'nama_matkul' => $pertemuan->matkul->nama_matkul ?? '-',
                        'nama_dosen' => $presensi->dosen->nama ?? '-',
                        'tgl_presensi' => $presensi->tgl_presensi,
                        'pertemuan_ke' => $pertemuan->pertemuan_ke,
                        'status_pertemuan' => $pertemuan->status,
                        'status' => $detail->status,
                    ];
                });
            });
        })->groupBy('nim');




// $groupMahasiswa = $pertemuans->flatMap(function($pertemuan) {
//     return $pertemuan->presensi->flatMap(function ($presensi) use ($pertemuan) {
//         if ($presensi->detailPresensi->isEmpty()) {
//             // Tambahkan baris kosong (non-mahasiswa) untuk tetap masuk ke rekap
//             return [[
//                 'nim' => '00000000', // atau nilai dummy
//                 'nama_mahasiswa' => '-',
//                 'semester' => '-',
//                 'nama_prodi' => $pertemuan->prodi->nama_prodi ?? '-',
//                 'kode_matkul' => $pertemuan->matkul->kode_matkul ?? '-',
//                 'nama_matkul' => $pertemuan->matkul->nama_matkul ?? '-',
//                 'nama_dosen' => $presensi->dosen->nama ?? '-',
//                 'tgl_presensi' => $presensi->tgl_presensi,
//                 'pertemuan_ke' => $pertemuan->pertemuan_ke,
//                 'status_pertemuan' => $pertemuan->status,
//                 'status' => null,
//             ]];
//         }

//         // Jika ada mahasiswa
//         return $presensi->detailPresensi->map(function($detail) use ($presensi,$pertemuan){
//             return [
//                 'nim' => $detail->mahasiswa->nim,
//                 'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
//                 'semester' => $detail->mahasiswa->semester ?? '-',
//                 'nama_prodi' => $pertemuan->prodi->nama_prodi ?? '-',
//                 'kode_matkul' => $pertemuan->matkul->kode_matkul ?? '-',
//                 'nama_matkul' => $pertemuan->matkul->nama_matkul ?? '-',
//                 'nama_dosen' => $presensi->dosen->nama ?? '-',
//                 'tgl_presensi' => $presensi->tgl_presensi,
//                 'pertemuan_ke' => $pertemuan->pertemuan_ke,
//                 'status_pertemuan' => $pertemuan->status,
//                 'status' => $detail->status,
//             ];
//         });
//     });
// })->groupBy('nim');






        foreach ($groupMahasiswa as $nim => $records) {
                // if ($nim === '00000000') continue; // abaikan entri dummy

            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']); // pastikan kolom ini ada
                    // $jenis = $statusPertemuanMap[$ke] ?? 'aktif';


                // $jenis = strtolower($pertemuans->firstWhere('pertemuan_ke', $ke)->status ?? 'aktif'); // pastikan kolom ini ada


                switch ($jenis) {
                    case 'libur':
                        $pertemuan[$ke] = '-';
                        break;
                    case 'uts':
                        $pertemuan[$ke] = 'UTS';
                        break;
                    case 'uas':
                        $pertemuan[$ke] = 'UAS';
                        break;
                    case 'aktif':
                    default:

                    switch ($record['status']) {
                        case 1:
                            $pertemuan[$ke] = 'H'; $statusCount['hadir']++; break;
                        case 2:
                            $pertemuan[$ke] = 'I'; $statusCount['izin']++; break;
                        case 3:
                            $pertemuan[$ke] = 'S'; $statusCount['sakit']++; break;
                        default:
                            $pertemuan[$ke] = 'A'; $statusCount['alpha']++; break;
                    }
                    break;
                }
            }

         // Lengkapi pertemuan jika belum 16
        $defaultPertemuan = 16;

            for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
                if (!isset($pertemuan[$i])) {
                    $jenis = $statusPertemuanMap[$i] ?? null;
                    if ($jenis === 'uts') {
                        $pertemuan[$i] = 'UTS';
                    } elseif ($jenis === 'uas') {
                        $pertemuan[$i] = 'UAS';
                    } elseif ($jenis === 'libur') {
                        $pertemuan[$i] = '-';
                    } else {
                        $pertemuan[$i] = '-';
                    }
                }
            }

        // for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
        //     if (!isset($pertemuan[$i])) {
        //         $pertemuan[$i] = '-';
        //     }
        // }

        $total = array_sum($statusCount);

            $rekap[$nim] = [
                'nim' => $nim,
                'nama_mahasiswa' => $records->first()['nama_mahasiswa'],
                'semester' => $records->first()['semester'],
                'nama_prodi' => $records->first()['nama_prodi'],
                'kode_matkul' => $records->first()['kode_matkul'],
                'nama_matkul' => $records->first()['nama_matkul'],
                'nama_dosen' => $dosenPengajar,
                'pertemuan' => $pertemuan,
                'tanggal_pertemuan' => $tanggalPertemuan,
                'hadir' => $statusCount['hadir'],
                'izin' => $statusCount['izin'],
                'sakit' => $statusCount['sakit'],
                'alpha' => $statusCount['alpha'],
                'kehadiran' => $total > 0 ? round(($statusCount['hadir'] / $total) * 100) . '%' : '0%',
                'izin_persentase' => $total > 0 ? round(($statusCount['izin'] / $total) * 100) . '%' : '0%',
                'sakit_persentase' => $total > 0 ? round(($statusCount['sakit'] / $total) * 100) . '%' : '0%',
                'alpha_persentase' => $total > 0 ? round(($statusCount['alpha'] / $total) * 100) . '%' : '0%',
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => max(16, $maxPertemuan),
        ];
    }
    public function getRekap()
    {

        // $mahasiswa = Auth::user()->mahasiswa;
        $mahasiswaId = Auth::user()->mahasiswa;
        // dd($mahasiswaId);

        $pertemuans = Pertemuan::with(['presensi.dosen', 'matkul', 'prodi', 'tahun','presensi.detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId->id);
        }])
            // ->whereHas('presensi.detailPresensi',function ($q) use ($mahasiswaId){
            //     $q->where('mahasiswa_id', $mahasiswaId->id);
            // })
            ->where('prodi_id', $mahasiswaId->prodi_id)
            ->where('semester', $mahasiswaId->semester)
            ->orderBy('pertemuan_ke')
            ->get();

            $rekap = [];
            $maxPertemuan = $pertemuans->count();
            $statusPertemuanMap = $pertemuans->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));

        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            return $pertemuan->presensi->flatMap(function ($presensi) use ($pertemuan) {
                return $presensi->detailPresensi->map(function($detail) use ($presensi,$pertemuan){
                    return [
                        'nim' => $detail->mahasiswa->nim,
                        'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
                        'semester' => $detail->mahasiswa->semester ?? '-',
                        'nama_prodi' => $pertemuan->prodi->nama_prodi ?? '-',
                        'matkul_id' => $pertemuan->matkul_id ?? '-',
                        'kode_matkul' => $pertemuan->matkul->kode_matkul ?? '-',
                        'nama_matkul' => $pertemuan->matkul->nama_matkul ?? '-',
                        'nama_dosen' => $presensi->dosen->nama ?? '-',
                        'tgl_presensi' => $presensi->tgl_presensi,
                        'pertemuan_ke' => $pertemuan->pertemuan_ke,
                        'status_pertemuan' => $pertemuan->status,
                        'status' => $detail->status,
                    ];
                });
            });
        })->groupBy('matkul_id');

        foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']); // pastikaan kolom ini ada
                    // $jenis = $statusPertemuanMap[$ke] ?? 'aktif';


                // $jenis = strtolower($pertemuans->firstWhere('pertemuan_ke', $ke)->status ?? 'aktif'); // pastikan kolom ini ada


                switch ($jenis) {
                    case 'libur':
                        $pertemuan[$ke] = '-';
                        break;
                    case 'uts':
                        $pertemuan[$ke] = 'UTS';
                        break;
                    case 'uas':
                        $pertemuan[$ke] = 'UAS';
                        break;
                    case 'aktif':
                    default:

                    switch ($record['status']) {
                        case 1:
                            $pertemuan[$ke] = 'H'; $statusCount['hadir']++; break;
                        case 2:
                            $pertemuan[$ke] = 'I'; $statusCount['izin']++; break;
                        case 3:
                            $pertemuan[$ke] = 'S'; $statusCount['sakit']++; break;
                        default:
                            $pertemuan[$ke] = 'A'; $statusCount['alpha']++; break;
                    }
                    break;
                }
            }

            $total = array_sum($statusCount);
            $defaultPertemuan = 16;

            for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
                if (!isset($pertemuan[$i])) {
                    $jenis = $statusPertemuanMap[$i] ?? null;
                    if ($jenis === 'uts') {
                        $pertemuan[$i] = 'UTS';
                    } elseif ($jenis === 'uas') {
                        $pertemuan[$i] = 'UAS';
                    } elseif ($jenis === 'libur') {
                        $pertemuan[$i] = '-';
                    } else {
                        $pertemuan[$i] = '-';
                    }
                }
            }

            // for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
            //     if (!isset($pertemuan[$i])) {
            //         $pertemuan[$i] = '-';
            //     }
            // }

            $rekap[$matkul] = [
                'nim' => $records->first()['nim'],
                'nama_mahasiswa' => $records->first()['nama_mahasiswa'],
                'semester' => $records->first()['semester'],
                'nama_prodi' => $records->first()['nama_prodi'],
                'kode_matkul' => $records->first()['kode_matkul'],
                'nama_matkul' => $records->first()['nama_matkul'],
                'nama_dosen' => $dosenPengajar,
                'pertemuan' => $pertemuan,
                'tanggal_pertemuan' => $tanggalPertemuan,
                'hadir' => $statusCount['hadir'],
                'izin' => $statusCount['izin'],
                'sakit' => $statusCount['sakit'],
                'alpha' => $statusCount['alpha'],
                'kehadiran' => $total > 0 ? round(($statusCount['hadir'] / $total) * 100) . '%' : '0%',
                'izin_persentase' => $total > 0 ? round(($statusCount['izin'] / $total) * 100) . '%' : '0%',
                'sakit_persentase' => $total > 0 ? round(($statusCount['sakit'] / $total) * 100) . '%' : '0%',
                'alpha_persentase' => $total > 0 ? round(($statusCount['alpha'] / $total) * 100) . '%' : '0%',
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => max(16, $maxPertemuan),
        ];
    }

    public function getFilterRekap($tahunId)
    {

        $mahasiswaId = Auth::user()->mahasiswa;
                // dd($mahasiswaId);

        $pertemuans = Pertemuan::with(['presensi.dosen', 'matkul', 'prodi', 'tahun','presensi.detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId->id);
        }])
            // ->whereHas('presensi.detailPresensi',function ($q) use ($mahasiswaId){
            //     $q->where('mahasiswa_id', $mahasiswaId->id);
            // })
            // ->where('prodi_id', $mahasiswaId->prodi_id)
            // ->where('semester', $mahasiswaId->semester)
            ->where('prodi_id', $mahasiswaId->prodi_id)
            ->where('semester', $mahasiswaId->semester)
            ->where('tahun_ajaran_id', $tahunId)
            ->orderBy('pertemuan_ke')
            ->get();

        // $presensis = Presensi::with(['dosen', 'matkul', 'prodi', 'tahunAjaran','detailPresensi' => function ($q) use ($mahasiswaId){
        //     $q->where('mahasiswa_id',$mahasiswaId);
        // }])
        //     ->whereHas('detailPresensi',function ($q) use ($mahasiswaId){
        //         $q->where('mahasiswa_id', $mahasiswaId);
        //     })
        //     ->where('tahun_ajaran_id', $tahunId)
        //     ->orderBy('tgl_presensi')
        //     ->get();

        $rekap = [];
        $maxPertemuan = $pertemuans->count();
        $statusPertemuanMap = $pertemuans->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));

        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            return $pertemuan->presensi->flatMap(function ($presensi) use ($pertemuan) {
                return $presensi->detailPresensi->map(function($detail) use ($presensi,$pertemuan){
        // $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
        //     return $pertemuan->presensi->flatMap(function ($presensi) use ($pertemuan) {
        //         return $presensi->detailPresensi->map(function($detail) use ($presensi,$pertemuan){
                    return [
                        'nim' => $detail->mahasiswa->nim,
                        'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
                        'semester' => $detail->mahasiswa->semester ?? '-',
                        'nama_prodi' => $pertemuan->prodi->nama_prodi ?? '-',
                        'matkul_id' => $pertemuan->matkul_id ?? '-',
                        'kode_matkul' => $pertemuan->matkul->kode_matkul ?? '-',
                        'nama_matkul' => $pertemuan->matkul->nama_matkul ?? '-',
                        'nama_dosen' => $presensi->dosen->nama ?? '-',
                        'tgl_presensi' => $presensi->tgl_presensi,
                        'pertemuan_ke' => $pertemuan->pertemuan_ke,
                        'status_pertemuan' => $pertemuan->status,
                        'status' => $detail->status,
                    ];
                });
            });
        })->groupBy('matkul_id');

       foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']); // pastikaan kolom ini ada
                    // $jenis = $statusPertemuanMap[$ke] ?? 'aktif';


                // $jenis = strtolower($pertemuans->firstWhere('pertemuan_ke', $ke)->status ?? 'aktif'); // pastikan kolom ini ada


                switch ($jenis) {
                    case 'libur':
                        $pertemuan[$ke] = '-';
                        break;
                    case 'uts':
                        $pertemuan[$ke] = 'UTS';
                        break;
                    case 'uas':
                        $pertemuan[$ke] = 'UAS';
                        break;
                    case 'aktif':
                    default:

                    switch ($record['status']) {
                        case 1:
                            $pertemuan[$ke] = 'H'; $statusCount['hadir']++; break;
                        case 2:
                            $pertemuan[$ke] = 'I'; $statusCount['izin']++; break;
                        case 3:
                            $pertemuan[$ke] = 'S'; $statusCount['sakit']++; break;
                        default:
                            $pertemuan[$ke] = 'A'; $statusCount['alpha']++; break;
                    }
                    break;
                }
            }

            $total = array_sum($statusCount);
            $defaultPertemuan = 16;

            for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
                if (!isset($pertemuan[$i])) {
                    $jenis = $statusPertemuanMap[$i] ?? null;
                    if ($jenis === 'uts') {
                        $pertemuan[$i] = 'UTS';
                    } elseif ($jenis === 'uas') {
                        $pertemuan[$i] = 'UAS';
                    } elseif ($jenis === 'libur') {
                        $pertemuan[$i] = '-';
                    } else {
                        $pertemuan[$i] = '-';
                    }
                }
            }

            // for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
            //     if (!isset($pertemuan[$i])) {
            //         $pertemuan[$i] = '-';
            //     }
            // }

            $rekap[$matkul] = [
                'nim' => $records->first()['nim'],
                'nama_mahasiswa' => $records->first()['nama_mahasiswa'],
                'semester' => $records->first()['semester'],
                'nama_prodi' => $records->first()['nama_prodi'],
                'kode_matkul' => $records->first()['kode_matkul'],
                'nama_matkul' => $records->first()['nama_matkul'],
                'nama_dosen' => $dosenPengajar,
                'pertemuan' => $pertemuan,
                'tanggal_pertemuan' => $tanggalPertemuan,
                'hadir' => $statusCount['hadir'],
                'izin' => $statusCount['izin'],
                'sakit' => $statusCount['sakit'],
                'alpha' => $statusCount['alpha'],
                'kehadiran' => $total > 0 ? round(($statusCount['hadir'] / $total) * 100) . '%' : '0%',
                'izin_persentase' => $total > 0 ? round(($statusCount['izin'] / $total) * 100) . '%' : '0%',
                'sakit_persentase' => $total > 0 ? round(($statusCount['sakit'] / $total) * 100) . '%' : '0%',
                'alpha_persentase' => $total > 0 ? round(($statusCount['alpha'] / $total) * 100) . '%' : '0%',
            ];
        }

        return [
            'rekap' => array_values($rekap),
            'totalPertemuan' => max(16, $maxPertemuan),
        ];
    }
}
