<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #dce6f1; }
        .header-info td { border: none; padding: 1px 1px; text-align: left; }
        .header-info td:first-child {width: 100px; white-space: nowrap;}
    </style>
</head>
<body>

            <div style="text-align: center; margin-bottom: 10px;">
                <img src="{{ public_path('images/stipress.png') }}" style="max-width: 50px; width: 100%;">

                {{-- <img src="{{ asset('images/stipress.png') }}" alt="Kop Surat STIKES" style="max-width: 700px; width: 100%;"> --}}
            </div>

    <h3 align="center">REKAP KEHADIRAN MAHASISWA</h3>

    <table class="header-info">
        <tr><td>Nim</td><td>: {{ $nim }}</td></tr>
        <tr><td>Nama</td><td>: {{ $nama }}</td></tr>
        <tr><td>Program Studi</td><td>: {{ $prodi }}</td></tr>
        <tr><td>Semester</td><td>: {{ reset($rekap)['semester'] ?? '-' }}</td></tr>
    </table>

    <table>
        <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Kode Mata Kuliah</th>
                    <th class="border border-gray-300 px-4 py-2">Nama Mata Kuliah</th>
                    @for ($i = 1; $i <= 16; $i++)
                        <th class="border border-gray-300 px-4 py-2 text-center">{{ $i }}</th>
                    @endfor
                    <th class="border border-gray-300 px-4 py-2">%Kehadiran</th>
                </tr>
            </thead>
            <tbody class="text-center">
                    @foreach ($rekap as $index => $item)

                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2">{{$loop->iteration}}</td>
                    <td class="border border-gray-300 px-4 py-2">{{$item['kode_matkul'] ?? ''}}</td>
                    <td class="border border-gray-300 px-4 py-2">{{$item['nama_matkul'] ?? ''}}</td>
                    @for ($i = 1; $i <= $totalPertemuan; $i++)
                        @php
                            $tanggal = $item['tanggal_pertemuan'][$i] ?? null;
                            $status = $item['pertemuan'][$i] ?? '';
                            $dosen = $item['nama_dosen'][$i] ?? '';
                            switch ($status) {
                                case 'H':
                                    $bg = 'bg-green-500 text-white';
                                    break;
                                case 'I':
                                    $bg = 'bg-yellow-500 text-white';
                                    break;
                                case 'S':
                                    $bg = 'bg-blue-500 text-white';
                                    break;
                                case 'A':
                                    $bg = 'bg-red-600 text-white';
                                    break;
                                default:
                                    $bg = 'bg-gray-400 text-white';
                                    break;
                            };
                        @endphp
                            <td class="border px-4 py-2 font-semibold {{ $bg }}" title="{{$tanggal .' '. $dosen}}">{{ $status }}</td>
                    @endfor
                    <td class="border border-gray-300 px-4 py-2">{{$item['kehadiran']}}</td>
                </tr>
                @endforeach
            </tbody>
    </table>

    <p style="margin-top: 20px;">Keterangan:</p>
    <p>H = Hadir</p>
    <p>I = Izin</p>
    <p>S = Sakit</p>
    <p>A = Alpha</p>
    <p>- = Tidak ada perkuliahan</p>

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

    <p style="margin-top: 20px;">: M = Mengajar, - = Tidak ada perkuliahan</p>

</body>
</html> --}}

