<x-layout>
    @vite(['resources/js/pages/admin/data-jadwal.js'])
    <div class="h-full dark:text-white">
    <x-slot:title>{{ $title }}</x-slot:title>
    <p class="dark:text-gray-300">Seluruh Daftar Jadwal</p>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="flex flex-col xl:flex-row">
                <!-- Program Studi -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
                    <label for="prodi" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Program Studi:</label>
                    <select id="prodi" name="prodi" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="" hidden selected>Pilih Program Studi</option>
                        @foreach ($prodi as $p)
                            <option value="{{ $p->id }}">{{ $p->jenjang .' '. $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
                    <label for="dosen" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Dosen:</label>
                    <select id="dosen" name="dosen_id" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="" hidden selected>Pilih Dosen</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}">{{ $d->nama}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun Ajaran -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3">
                    <label for="tahun-ajaran" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Tahun Ajaran:</label>
                    <select id="tahun-ajaran" name="tahun_ajaran" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="" hidden selected>Pilih Tahun Ajaran</option>
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->tahun_awal .'/'. $t->tahun_akhir .' '. $t->keterangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto max-w-full mt-3 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">

            <div class="mb-10 flex">
                <a href="{{route('admin.master-jadwal.create')}}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-plus-square-fill mr-2"></i>
                        <span>Tambah</span>
                    </button>
                </a>
            </div>

            <div class="overflow-x-auto w-68 sm:w-150 md:w-full mt-3 pb-3">
                <table id="data-jadwal" class="text-sm text-left w-full pt-1 display nowrap">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white sticky top-0 z-10">
                        <tr>
                            <th class=" dark:border-gray-600 px-4 py-2">No</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Hari</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Jam Perkuliahan</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Durasi</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Dosen Koordinator</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Program Studi</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Tahun Ajaran</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Semester</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Mata Kuliah</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Ruangan</th>
                            <th class=" dark:border-gray-600 px-4 py-2 !text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="dark:text-white">
                        @foreach ($jadwal as $j)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class=" dark:border-gray-600 px-4 py-2">{{$loop->iteration}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->hari}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->jam}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->durasi .' SKS'}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->dosen->nama}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->prodi->jenjang .' '.$j->prodi->nama_prodi}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->tahun->tahun_awal .'/'.$j->tahun->tahun_akhir .' '. $j->tahun->keterangan }}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->semester}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->matkul->nama_matkul}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$j->ruangan->nama_ruangan}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{route('admin.master-jadwal.show', $j->id)}}">
                                            <button class="cursor-pointer px-2 py-1 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white rounded-md" title="Detail">
                                                <i class="bi bi-card-text text-lg"></i>
                                            </button>
                                        </a>

                                        <form action="{{ route('admin.master-jadwal.destroy', $j->id) }}" method="POST" class="form-hapus inline-block">
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
