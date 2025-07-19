<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #dce6f1; }
        .header-info td { border: none; padding: 2px 4px; text-align: left; }
    </style>
</head>
<body>

    <h3 align="center">REKAP KEHADIRAN DOSEN</h3>

    <table class="header-info">
        <tr><td>NIP</td><td>: {{ $nip }}</td></tr>
        <tr><td>Nama</td><td>: {{ $nama }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Program Studi</th>
                <th class="border border-gray-300 px-4 py-2">Semester</th>
                <th class="border border-gray-300 px-4 py-2">Mata Kuliah</th>
                @for ($i = 1; $i <= $totalPertemuan; $i++)
                    <th class="border border-gray-300 px-4 py-2 text-center">{{ $i }}</th>
                @endfor
                <th class="border border-gray-300 px-4 py-2">%hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataPresensi as $index => $item)

            <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 px-4 py-2">{{$loop->iteration }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['nama_prodi'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['semester'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['nama_matkul'] }}</td>
                @for ($i = 0; $i < $totalPertemuan; $i++)
                    @php
                        $status = $item['status_pertemuan'][$i] ?? null;
                        $bg = match($status) {
                            'M' => 'text-green-500',
                            '-' => 'text-gray-500',
                            'UTS' => 'text-red-500',
                            'UAS' => 'text-red-500'
                        };
                    @endphp
                    <td class="border px-4 py-2 font-semibold {{ $bg }}">{{ $status }}</td>
                @endfor
                <td class="border border-gray-300 px-4 py-2">{{$item['total_pertemuan']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;">Keterangan:</p>
    <p>M = Mengajar</p>
    <p>- = Tidak terselenggara perkuliahan</p>

</body>
</html>

{{-- <!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #dce6f1; }
        .header-info td { border: none; padding: 2px 4px; text-align: left; }
        .tanggal-pertemuan { margin-top: 20px; font-size: 10px; }
        .tanggal-pertemuan span {
            display: inline-block;
            margin-right: 10px;
            padding: 3px 6px;
            background-color: #dce6f1;
            border: 1px solid #000;
            border-radius: 3px;
        }
        .footer-info { margin-top: 10px; font-size: 11px; }
    </style>
</head>
<body>

    <h3 align="center">REKAP KEHADIRAN DOSEN</h3>

    <table class="header-info">
        <tr><td>NIP</td><td>: {{ $nip }}</td></tr>
        <tr><td>Nama</td><td>: {{ $nama }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Program Studi</th>
                <th class="border border-gray-300 px-4 py-2">Semester</th>
                <th class="border border-gray-300 px-4 py-2">Mata Kuliah</th>
                @for ($i = 1; $i <= $totalPertemuan; $i++)
                    <th class="border border-gray-300 px-4 py-2 text-center">{{ $i }}</th>
                @endfor
                <th class="border border-gray-300 px-4 py-2">%hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataPresensi as $index => $item)

            <tr class="hover:bg-gray-50">
                <td class="border border-gray-300 px-4 py-2">{{$loop->iteration }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['nama_prodi'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['semester'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item['nama_matkul'] }}</td>
                @for ($i = 0; $i < $totalPertemuan; $i++)
                    @php
                        $tanggal = $item['tanggal_pertemuan'][$i] ?? null;
                        $status = $tanggal ? 'M' : '-';
                        $bg = match($status) {
                            'M' => 'bg-green-500 text-white',
                            '-' => 'bg-gray-500 text-white',
                        };
                    @endphp
                    <td class="border px-4 py-2 font-semibold {{ $bg }}" title="{{$tanggal}}">{{ $status }}</td>
                @endfor
                <td class="border border-gray-300 px-4 py-2">{{$item['total_pertemuan']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="tanggal-pertemuan">
        <strong>Tanggal Pertemuan:</strong>
        @for ($i = 0; $i < $totalPertemuan; $i++)
            @php
                $tgl = $dataPresensi[0]['tanggal_pertemuan'][$i] ?? '-';
            @endphp
            <span title="Pertemuan ke-{{ $i+1 }}">{{ $tgl }}</span>
        @endfor
    </div>

    <div class="footer-info">
        <strong>Total Pertemuan:</strong> {{ $totalPertemuan }}
    </div>

    <p style="margin-top: 20px;">Keterangan: M = Mengajar, - = Tidak ada perkuliahan</p>

</body>
</html> --}}

