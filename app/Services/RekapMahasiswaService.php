<?php
namespace App\Services;

use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\DetailPresensi;

class RekapMahasiswaService
{
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
                        'status' => $detail->status,
                    ];
                });
            });
        })->groupBy('nim');

        foreach ($groupMahasiswa as $nim => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('pertemuan_ke')->values();

            foreach ($sorted as $i => $record) {
                $ke = $record['pertemuan_ke'];
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

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
            }

         // Lengkapi pertemuan jika belum 16
        $defaultPertemuan = 16;
        for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
            if (!isset($pertemuan[$i])) {
                $pertemuan[$i] = '-';
            }
        }

        $total = array_sum($statusCount);

            // $total = array_sum($statusCount);
            // $maxPertemuan = max($maxPertemuan, count($pertemuan));
            // $defaultPertemuan = 16;

            // for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
            //     if (!isset($pertemuan[$i])) {
            //         $pertemuan[$i] = '-';
            //     }
            // }

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
    public function getRekap($mahasiswaId)
    {
        $presensis = Pertemuan::with(['presensi.dosen', 'matkul', 'prodi', 'tahun','presensi.detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId);
        }])
            ->whereHas('detailPresensi',function ($q) use ($mahasiswaId){
                $q->where('mahasiswa_id', $mahasiswaId);
            })
            ->orderBy('tgl_presensi')
            ->get();

        $rekap = [];
        $maxPertemuan = 0;

        $groupMahasiswa = $presensis->flatMap(function($presensi){
            return $presensi->detailPresensi->map(function($detail) use ($presensi){
                return [
                    'nim' => $detail->mahasiswa->nim,
                    'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
                    'semester' => $detail->mahasiswa->semester ?? '-',
                    'nama_prodi' => $presensi->prodi->nama_prodi ?? '-',
                    'kode_matkul' => $presensi->matkul->kode_matkul ?? '-',
                    'nama_matkul' => $presensi->matkul->nama_matkul ?? '-',
                    'nama_dosen' => $presensi->dosen->nama ?? '-',
                    'tgl_presensi' => $presensi->tgl_presensi,
                    'status' => $detail->status,
                ];
            });
        })->groupBy('matkul_id');

        foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('tgl_presensi')->values();

            foreach ($sorted as $i => $record) {
                $ke = $i + 1;
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

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
            }

            $total = array_sum($statusCount);
            $maxPertemuan = max($maxPertemuan, count($pertemuan));
            $defaultPertemuan = 16;

            for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
                if (!isset($pertemuan[$i])) {
                    $pertemuan[$i] = '-';
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
            'rekap' => $rekap,
            'totalPertemuan' => max(16, $maxPertemuan),
        ];
    }

    public function getFilterRekap($mahasiswaId, $tahunId)
    {
        $presensis = Presensi::with(['dosen', 'matkul', 'prodi', 'tahunAjaran','detailPresensi' => function ($q) use ($mahasiswaId){
            $q->where('mahasiswa_id',$mahasiswaId);
        }])
            ->whereHas('detailPresensi',function ($q) use ($mahasiswaId){
                $q->where('mahasiswa_id', $mahasiswaId);
            })
            ->where('tahun_ajaran_id', $tahunId)
            ->orderBy('tgl_presensi')
            ->get();

        $rekap = [];
        $maxPertemuan = 0;

        $groupMahasiswa = $presensis->flatMap(function($presensi){
            return $presensi->detailPresensi->map(function($detail) use ($presensi){
                return [
                    'nim' => $detail->mahasiswa->nim,
                    'nama_mahasiswa' => $detail->mahasiswa->nama ?? '-',
                    'semester' => $detail->mahasiswa->semester ?? '-',
                    'nama_prodi' => $presensi->prodi->nama_prodi ?? '-',
                    'kode_matkul' => $presensi->matkul->kode_matkul ?? '-',
                    'nama_matkul' => $presensi->matkul->nama_matkul ?? '-',
                    'nama_dosen' => $presensi->dosen->nama ?? '-',
                    'tgl_presensi' => $presensi->tgl_presensi,
                    'status' => $detail->status,
                ];
            });
        })->groupBy('matkul_id');

        foreach ($groupMahasiswa as $matkul => $records) {
            $pertemuan = [];
            $statusCount = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $tanggalPertemuan = [];
            $dosenPengajar = [];

            $sorted = $records->sortBy('tgl_presensi')->values();

            foreach ($sorted as $i => $record) {
                $ke = $i + 1;
                $tanggalPertemuan[$ke] = $record['tgl_presensi'];
                $dosenPengajar[$ke] = $record['nama_dosen'];

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
            }

            $total = array_sum($statusCount);
            $maxPertemuan = max($maxPertemuan, count($pertemuan));
            $defaultPertemuan = 16;

            for ($i = 1; $i <= max($defaultPertemuan, $maxPertemuan); $i++) {
                if (!isset($pertemuan[$i])) {
                    $pertemuan[$i] = '-';
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
            'totalPertemuan' => max(16, $maxPertemuan),
        ];
    }
}
