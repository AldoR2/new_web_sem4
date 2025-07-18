<x-layout>
    @vite(['resources/js/pages/admin/data-presensi.js'])
    <div class="h-full dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-800 dark:text-gray-200">Data Perkuliahan Hari ini</p>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                    <label for="filter-presensi" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter Data Perkuliahan:</label>
                    <select id="filter-presensi" name="prodi_id" class="bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-sm px-2 py-2">
                        <option value="" hidden selected>Pilih Program Studi</option>
                        <option value="today">Hari ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan ini</option>
                        <option value="all">Semua Periode</option>
                        {{-- <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="uts" {{ request('status') == 'uts' ? 'selected' : '' }}>UTS</option>
                        <option value="uas" {{ request('status') == 'uas' ? 'selected' : '' }}>UAS</option> --}}
                    </select>
                </div>
                <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0">
                    <label for="filter-status" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter Status Perkuliahan:</label>
                    <select id="filter-status" class="bg-white dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-sm px-2 py-2">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="uts">UTS</option>
                        <option value="uas">UAS</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="mb-10 flex">
                <a href="{{ route('admin.presensi.create') }}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-plus-square-fill mr-2"></i>
                        <span>Tambah</span>
                    </button>
                </a>
            </div>

            <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                <table id="data-presensi" class="text-sm w-full display nowrap pt-1">
                    <thead class="bg-gray-200 dark:bg-gray-500 text-gray-700 dark:text-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Tanggal</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Jam Perkuliahan</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Mata Kuliah</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Pertemuan Ke</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Dosen Pengajar</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Program Studi</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Semester</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2">Status Perkuliahan</th>
                            <th class="border-gray-300 dark:border-gray-700 px-4 py-2 !text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presensi as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2">{{ $p->tgl_presensi }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2">{{ substr($p->jam_awal, 0,5) . ' - ' . substr($p->jam_akhir, 0,5) }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2">{{ $p->pertemuan->matkul->nama_matkul }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2 text-center">{{ $p->pertemuan->pertemuan_ke ?? '-' }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2">{{ $p->dosen->nama }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2">{{ $p->pertemuan->prodi->nama_prodi }}</td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2 text-center">{{ $p->pertemuan->semester }}</td>
                                <td class="border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    @switch($p->pertemuan->status)
                                        @case('aktif')
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-green-500 rounded-full">Aktif</span>
                                        @break
                                        @case('uts')
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-full">UTS</span>
                                        @break
                                        @case('uas')
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-full">UAS</span>
                                        @break
                                        @case('libur')
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-full">Libur</span>
                                        @break
                                        @default
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-gray-500 rounded-full">-</span>
                                    @endswitch
                                </td>
                                <td class="border-gray-300 dark:border-gray-600 dark:text-white px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.presensi.edit', $p->id) }}">
                                            <button class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                                <i class="bi bi-pencil-square text-lg"></i>
                                            </button>
                                        </a>

                                        <a href="{{ route('admin.presensi.show', $p->id) }}">
                                            <button class="cursor-pointer px-2 py-1 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white rounded-md">
                                                <i class="bi bi-card-text text-lg"></i>
                                            </button>
                                        </a>

                                        <form action="{{ route('admin.presensi.destroy', $p->id) }}" method="POST" class="form-hapus inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-md">
                                                <i class="bi bi-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
