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

        <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ public_path('images/stipress.png') }}" style="max-width: 50px; width: 100%;">

            {{-- <img src="{{ asset('images/stipress.png') }}" alt="Kop Surat STIKES" style="max-width: 700px; width: 100%;"> --}}
        </div>

    <h3 align="center">JADWAL DOSEN</h3>

    <table class="header-info">
        <tr><td>NIP</td><td>: {{ $nip }}</td></tr>
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
                <td class="border border-gray-300 px-4 py-2">{{ $j->prodi->nama_prodi }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->semester }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $j->ruangan->nama_ruangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <p style="margin-top: 20px;">Keterangan:</p>
    <p>M = Mengajar</p>
    <p>- = Tidak terselenggara perkuliahan</p> --}}

</body>
</html>
