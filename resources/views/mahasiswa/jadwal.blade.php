<x-layout>
  @vite(['resources/js/pages/mahasiswa/data-jadwal.js'])
    <div class="h-full dark:bg-gray-700 dark:text-gray-100 transition">
        <x-slot:title>{{ $title }}</x-slot:title>

        <p class="text-gray-800 dark:text-gray-200">Lihat Jadwal Perkuliahan</p>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="flex flex-col w-full mb-4">
                <label for="tahun-ajaran" class="mb-1 font-semibold dark:text-gray-300">Tahun Ajaran:</label>
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
            <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                <table id="data-jadwal" class="min-w-full divide-y pt-1 divide-gray-200 dark:divide-gray-600 text-sm text-gray-700 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium">Hari</th>
                            <th class="px-6 py-3 text-left font-medium">Jam</th>
                            <th class="px-6 py-3 text-left font-medium">Durasi</th>
                            <th class="px-6 py-3 text-left font-medium">Mata Kuliah</th>
                            <th class="px-6 py-3 text-left font-medium">Dosen Koordinator</th>
                            <th class="px-6 py-3 text-left font-medium">Program Studi</th>
                            <th class="px-6 py-3 text-left font-medium">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($jadwal as $j)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4">{{$j->hari}}</td>
                                <td class="px-6 py-4">{{substr($j->jam,0,5)}}</td>
                                <td class="px-6 py-4">{{$j->durasi}} SKS</td>
                                <td class="px-6 py-4">{{$j->matkul->nama_matkul}}</td>
                                <td class="px-6 py-4">{{$j->dosen->nama}}</td>
                                <td class="px-6 py-4">{{$j->prodi->jenjang .' '. $j->prodi->nama_prodi}}</td>
                                <td class="px-6 py-4">{{$j->ruangan->nama_ruangan}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
