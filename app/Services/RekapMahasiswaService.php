<?php
namespace App\Services;

use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\DetailPresensi;
use Auth;

class RekapMahasiswaService
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

    public function getRekapMahasiswa($prodiId, $semester, $matkulId)
    {
        $pertemuans = Pertemuan::with(['presensi.detailPresensi.mahasiswa','presensi.dosen', 'matkul', 'prodi', 'tahun'])
            ->where('prodi_id', $prodiId)
            ->where('semester', $semester)
            ->where('matkul_id', $matkulId)
            ->orderBy('pertemuan_ke')
            ->get();

        $rekap = [];
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $defaultPertemuan = 16;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);
        $statusPertemuanMap = $pertemuans->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));



        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            $presensi = $pertemuan->presensi;
            if (!$presensi) return collect();
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
        })->groupBy('nim');

        foreach ($groupMahasiswa as $nim => $records) {

            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']);
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

            for ($i = 1; $i <= $totalPertemuan; $i++) {
                if (!isset($pertemuan[$i])) {
                    $jenis = $statusPertemuanMap[$i] ?? null;

                    $pertemuan[$i] = match($jenis) {
                        'uts'   => 'UTS',
                        'uas'   => 'UAS',
                        'libur' => '-',
                        default => '-'
                    };
                }
            }

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
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $totalPertemuan,
        ];
    }
    public function getRekap()
    {

        $mahasiswaId = Auth::user()->mahasiswa;

        $pertemuans = Pertemuan::with(['presensi.dosen', 'matkul', 'prodi', 'tahun','presensi.detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId->id);
        }])
            ->where('prodi_id', $mahasiswaId->prodi_id)
            ->where('semester', $mahasiswaId->semester)
            ->orderBy('pertemuan_ke')
            ->get();

            $rekap = [];
            $defaultPertemuan = 16;
            $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
            $totalPertemuan = max($defaultPertemuan, $maxPertemuan);

        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            $presensi = $pertemuan->presensi;
            if (!$presensi) return collect();
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
        })->groupBy('matkul_id');

        foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();
            $statusPertemuanMap = $pertemuans->where('matkul_id', $matkul)->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));


            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']);

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

            for ($i = 1; $i <= $totalPertemuan; $i++) {
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
            ];
        }

        return [
            'rekap' => $rekap,
            'totalPertemuan' => $totalPertemuan,
        ];
    }

    public function getFilterRekap($tahunId)
    {

        $mahasiswaId = Auth::user()->mahasiswa;

        $pertemuans = Pertemuan::with(['presensi.dosen', 'matkul', 'prodi', 'tahun','presensi.detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId->id);
        }])->where('prodi_id', $mahasiswaId->prodi_id)
            ->where('semester', $mahasiswaId->semester)
            ->where('tahun_ajaran_id', $tahunId)
            ->orderBy('pertemuan_ke')
            ->get();

        $rekap = [];
        $defaultPertemuan = 16;
        $maxPertemuan = $pertemuans->max('pertemuan_ke') ?? 0;
        $totalPertemuan = max($defaultPertemuan, $maxPertemuan);

        $groupMahasiswa = $pertemuans->flatMap(function($pertemuan){
            $presensi = $pertemuan->presensi;
            if (!$presensi) return collect();
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
        })->groupBy('matkul_id');

       foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];
            $statusPertemuanMap = $pertemuans->where('matkul_id', $matkul)->pluck('status', 'pertemuan_ke')->map(fn($s) => strtolower($s));

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

                $jenis = strtolower($record['status_pertemuan']);

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

            for ($i = 1; $i <= $totalPertemuan; $i++) {
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
            'totalPertemuan' => $totalPertemuan,
        ];
    }
}
