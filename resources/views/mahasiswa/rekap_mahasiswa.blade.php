<x-layout>
    @vite(['resources/js/pages/mahasiswa/rekap-mahasiswa.js'])

    <div class="h-full dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-800 dark:text-gray-200">Lihat Rekap Presensi Mahasiswa </p>

        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-900 rounded-sm shadow-xl border dark:border-gray-700">
            <div class="flex flex-col xl:flex-row mb-5">
                <div class="flex flex-col w-full mb-4 xl">
                    <label class="mb-1 font-semibold text-gray-700 dark:text-gray-200">Filter By Tahun Ajaran:</label>
                    <select id="tahun-ajaran" name="tahun_ajaran"
                        class="bg-white dark:bg-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded px-3 py-2">
                        <option value="" hidden selected>Pilih Tahun Ajaran</option>
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->tahun_awal .'/'. $t->tahun_akhir .' '.$t->keterangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-2 mb-5 flex gap-4">
                <a href="{{route('mahasiswa.export.mahasiswa.excel')}}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-green-700 hover:bg-green-800 active:bg-green-900 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-file-earmark-excel mr-2"></i>
                        <span>Export Excel</span>
                    </button>
                </a>

                <a href="{{route('mahasiswa.export.mahasiswa.pdf')}}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-filetype-pdf mr-2"></i>
                        <span>Export Pdf</span>
                    </button>
                </a>
            </div>

            <div x-data="{ hovering: false }" class="overflow-x-auto w-60 sm:w-150 md:w-240 xl:min-w-full pb-3">
                <table id="data-rekap-mahasiswa" class="text-sm text-left w-full pt-4 display nowrap text-gray-800 dark:text-gray-200">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Kode Mata Kuliah</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Mata Kuliah</th>
                            @for ($i = 1; $i <= 16; $i++)
                                <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center">{{ $i }}</th>
                            @endfor
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">%Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if (count($rekap))
                            @foreach ($rekap as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$loop->iteration}}</td>
                                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['kode_matkul'] ?? ''}}</td>
                                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['nama_matkul'] ?? ''}}</td>
                                    @for ($i = 1; $i <= $totalPertemuan; $i++)
                                        @php
                                            $tanggal = $item['tanggal_pertemuan'][$i] ?? null;
                                            $status = $item['pertemuan'][$i] ?? '';
                                            $dosen = $item['nama_dosen'][$i] ?? '';
                                            switch ($status) {
                                                case 'UTS': $bg = 'text-red-500'; break;
                                                case 'UAS': $bg = 'text-red-500'; break;
                                                case 'H': $bg = 'text-green-500'; break;
                                                case 'I': $bg = 'text-blue-500'; break;
                                                case 'S': $bg = 'text-yellow-500'; break;
                                                case 'A': $bg = 'text-red-500'; break;
                                                default: $bg = 'text-gray-400'; break;
                                            };
                                        @endphp
                                        <td class="dark:border-gray-600 px-4 py-2 font-semibold {{ $bg }}" title="{{$tanggal .' '. $dosen}}">{{ $status }}</td>
                                    @endfor
                                    <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['kehadiran']}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-gray-800 dark:text-gray-200">
                <h2 class="text-2xl font-semibold mb-5">Keterangan:</h2>
                <p class="mt-2 dark:text-white"><span class="text-green-500 font-bold">H  = Hadir</span></p>
                <p class="mt-2 dark:text-white"><span class="text-blue-500 font-bold">I  = Tidak masuk dengan Izin</span></p>
                <p class="mt-2 dark:text-white"><span class="text-yellow-500 font-bold">S  = Tidak masuk karena sakit</span></p>
                <p class="mt-2 dark:text-white"><span class="text-red-500 font-bold">A  = Tidak masuk tanpa keterangan</span></p>
                <p class="mt-2 dark:text-white"><span class="text-gray-500 font-bold">-  = Tidak terselenggara perkuliahan</span></p>
            </div>
        </div>
    </div>
</x-layout>
