<x-layout>
    <div class="h-full dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="dark:text-white">Informasi Jadwal</p>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white rounded-sm shadow-xl
                    dark:bg-gray-800 dark:text-gray-200">
            <h1 class="mb-2 text-2xl font-semibold text-gray-700 dark:text-white">Dosen Pengajar</h1>

            <div class="overflow-x-auto w-68 sm:w-150 md:w-full mt-3 pb-3">
                <table id="tbl-pres" class="text-sm text-left w-full pt-2">
                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Hari</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Jam Perkuliahan</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Durasi</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Mata Kuliah</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Dosen</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Program Studi</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Semester</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Ruangan</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Tahun Ajaran</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->hari ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->jam }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->durasi . ' SKS' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->matkul->nama_matkul ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->dosen->nama ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->prodi->nama_prodi ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->semester ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->ruangan->nama_ruangan ?? '-' }}</td>
                            <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $jadwal->tahun->tahun_awal . '/' . $jadwal->tahun->tahun_akhir . ' ' . $jadwal->tahun->keterangan ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h1 class="mb-2 mt-6 text-2xl font-semibold text-gray-700 dark:text-white">Mahasiswa</h1>

            <div class="overflow-x-auto w-68 sm:w-150 md:w-full mt-3 pb-3">
                <table id="tbl-pres" class="text-sm text-left w-full pt-2">
                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">No</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Nim</th>
                            <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Nama</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($detail as $dp)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $dp->mahasiswa->nim }}</td>
                                <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $dp->mahasiswa->nama }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
