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


    <h3 align="center">REKAP MATA KULIAH</h3>

    <table class="header-info">
        <tr><td>ProgramStudi</td><td>: {{ $prodi->nama_prodi ?? '' }}</td></tr>
        <tr><td>Semester</td><td>: {{ $semester }}</td></tr>
        <tr><td>Mata Kuliah</td><td>: {{ $matkul->nama_matkul }}</td></tr>
    </table>

    <table>
        <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Pertemuan Ke</th>
                    <th class="border border-gray-300 px-4 py-2">Tanggal Perkuliahan</th>
                    <th class="border border-gray-300 px-4 py-2">Dosen Pengajar</th>
                    <th class="border border-gray-300 px-4 py-2">Mahasiswa Hadir</th>
                    <th class="border border-gray-300 px-4 py-2">Total Mahasiswa</th>
                    <th class="border border-gray-300 px-4 py-2">Metode Perkuliahan</th>
                </tr>
            </thead>
            <tbody class="text-center">
                    @foreach ($rekap as $index => $item)

                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-2">{{$item['pertemuan_ke'] ?? ''}}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ implode(', ', (array) $item['tanggal']) }}</td>
                    {{-- <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['tanggal'] ?? ''}}</td> --}}
                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['dosen']}}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['jumlah_hadir']}}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['totalMahasiswa']}}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ implode(', ', (array) $item['metode']) }}</td>
                </tr>
                @endforeach
            </tbody>
    </table>
</body>
</html>
