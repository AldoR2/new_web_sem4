<x-layout>
    @vite(['resources/js/pages/dosen/data-jadwal.js'])
    <div class="h-full dark:bg-gray-700 dark:text-white">
    <x-slot:title>{{ $title }}</x-slot:title>
    <p>Lihat Jadwal Perkuliahan</p>
        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="flex flex-col md:flex-row">

                <div class="flex flex-col w-full mb-4 mr-0">
                <label for="" class="mb-1 font-semibold dark:text-gray-300">Filter Jadwal:</label>
                    <select id="tahun-ajaran" name="tahun_ajaran" class="p-2 mt-1 py-[10.5px] w-full border-2 border-gray-400 dark:border-gray-600 rounded-sm bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100">
                        <option value="" hidden selected>Pilih Tahun Ajaran</option>
                        @foreach ($tahun as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->tahun_awal .'/'. $t->tahun_akhir .' '. $t->keterangan}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="overflow-x-auto w-68 sm:w-150 md:w-full mt-3 pb-3">
                <table id="data-jadwal" class="text-sm text-left w-full pt-1 dark:text-white display nowrap">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white sticky top-0 z-10">
                        <tr class="text-center">
                        <th class="dark:border-gray-600 px-4 py-2">Hari</th>
                        <th class="dark:border-gray-600 px-4 py-2">Jam</th>
                        <th class="dark:border-gray-600 px-4 py-2">Durasi Perkuliahan</th>
                        <th class="dark:border-gray-600 px-4 py-2">Mata Kuliah</th>
                        <th class="dark:border-gray-600 px-4 py-2">Program Studi</th>
                        <th class="dark:border-gray-600 px-4 py-2">Semester</th>
                        <th class="dark:border-gray-600 px-4 py-2">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($jadwal as $j)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->hari}}</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{substr($j->jam,0,5)}}</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->durasi}} SKS</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->matkul->nama_matkul}}</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->prodi->nama_prodi}}</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->semester}}</td>
                                <td class="dark:border-gray-600 px-4 py-2">{{$j->ruangan->nama_ruangan}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
