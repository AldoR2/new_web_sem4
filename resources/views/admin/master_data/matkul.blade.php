<x-layout>
    @vite(['resources/js/pages/admin/data-matkul.js'])

    <div class="relative dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-700 dark:text-gray-300">Daftar Seluruh Mata Kuliah</p>

        <div x-data="{openImport: false}" class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">

            <div class="flex flex-col xl:flex-row">
                <!-- Program Studi -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
                    <label for="prodi" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Program Studi:</label>
                    <select id="prodi" name="prodi" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="" hidden selected>Pilih Program Studi</option>
                        @foreach ($prodi as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
                    <label for="semester" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Semester:</label>
                    <select id="semester" name="semester" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="" hidden selected>Pilih Semester</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{$i}}"> Semester {{$i}} </option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Ajaran -->
                <div class="flex flex-col w-full mb-4 xl:w-1/3">
                    <label for="tahun_ajaran" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Filter By Tahun Ajaran:</label>
                    <select id="tahun_ajaran" name="tahun_ajaran" class="dark:bg-gray-700 dark:text-white dark:border-gray-600">
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
            <div class="mt-2 mb-5 flex gap-4">
                <a href="{{route('admin.master-matkul.create')}}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-plus-square-fill mr-2"></i>
                        <span>Tambah</span>
                    </button>
                </a>
            </div>

            <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                <table id="data-matkul" class="text-sm w-full pt-1 display nowrap dark:text-white">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class=" dark:border-gray-600 px-4 py-2">No</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Nama Mata Kuliah</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Program Studi</th>
                            <th class=" dark:border-gray-600 px-4 py-2">SKS</th>
                            <th class=" dark:border-gray-600 px-4 py-2">Tahun Ajaran</th>
                            <th class=" dark:border-gray-600 px-4 py-2 !text-center">Semester</th>
                            <th class=" dark:border-gray-600 px-4 py-2 !text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matkul as $m )
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class=" dark:border-gray-600 px-4 py-2">{{$loop->iteration}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$m->nama_matkul}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$m->prodi->nama_prodi ?? ''}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$m->durasi_matkul}} SKS</td>
                                <td class=" dark:border-gray-600 px-4 py-2">{{$m->tahunAjaran->tahun_awal .'/'. $m->tahunAjaran->tahun_akhir .' '.$m->tahunAjaran->keterangan ?? ''}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2 text-center">{{$m->semester}}</td>
                                <td class=" dark:border-gray-600 px-4 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{route('admin.master-matkul.edit', $m->id)}}" class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        <form action="{{ route('admin.master-matkul.destroy', $m->id) }}" method="POST" class="form-hapus inline-block">
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
