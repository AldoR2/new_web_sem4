<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #dce6f1; }
        .header-info td { border: none; padding: 2px 1px; text-align: left; }
        .header-info td:first-child {width: 100px; white-space: nowrap;}
    </style>
</head>
<body>

    <table style="width: 90%; border-collapse: collapse; border: none; margin: 0 auto;">
        <tr>
            <td style="width: 100px; text-align: center; border: none;">
                <img src="{{ public_path('images/logo-dikbud.png') }}" alt="Logo STIPRESS" style="max-width: 120px;" />
            </td>
            <td style="text-align: center; border: none;">
                <div style="font-size: 22px; font-weight: bold;">
                    SEKOLAH TINGGI ILMU KESEHATAN
                </div>
                <div style="font-size: 22px; font-weight: bold;">
                    STIKES PANTI WALUYA MALANG
                </div>
                <div style="font-size: 14px;">
                    Jl. Yulius Usman No. 62, Kasin, Kec. Klojen, Kota Malang, Jawa Timur 65117
                </div>
                <div style="font-size: 14px;">
                    Telp: 0341-369003 | Email: info@stikespantiwaluya.ac.id | Website: www.stikespantiwaluya.ac.id
                </div>
            </td>
            <td style="width:100px; border: none;">
                <img src="{{ public_path('images/stikes(1).png') }}" alt="Logo STIPRESS" style="max-width: 80px;" />
            </td>
        </tr>
    </table>
    <hr style="border: 2px solid #000; margin: 15px 0;" />

    <h3 align="center">JADWAL MAHASISWA</h3>

    <table class="header-info">
        <tr><td>NIM</td><td>: {{ $nim }}</td></tr>
        <tr><td>Nama</td><td>: {{ $nama }}</td></tr>
        <tr><td>Tahun Ajaran</td><td>: {{ $tahun->tahun_awal. '/'. $tahun->tahun_akhir .' '. $tahun->keterangan ?? '-' }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Hari</th>
                <th class="border border-gray-300 px-4 py-2">Jam</th>
                <th class="border border-gray-300 px-4 py-2">Durasi</th>
                <th class="border border-gray-300 px-4 py-2">Mata Kuliah</th>
                <th class="border border-gray-300 px-4 py-2">Dosen Koordinator</th>
                <th class="border border-gray-300 px-4 py-2">Program Studi</th>
                <th class="border border-gray-300 px-4 py-2">Semester</th>
                <th class="border border-gray-300 px-4 py-2">Ruangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jadwal as $j)

            <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 px-4 py-2">{{ $j->hari }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ substr($j->jam,0,5) }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->durasi }} SKS</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->matkul->nama_matkul }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->dosen->nama  }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->prodi->nama_prodi }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->semester }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->ruangan->nama_ruangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
