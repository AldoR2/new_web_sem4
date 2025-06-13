<x-layout>
         @vite(['resources/js/pages/dosen/rekap-dosen.js'])
  <div class="h-full dark:text-white">
    <x-slot:title>{{ $title }}</x-slot:title>
    <p>Lihat Rekap Presensi Dosen </p>
    <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
        <form action="{{route('admin.rekap-dosen.filter')}}" method="POST">
            @csrf
      <div class="flex flex-col md:flex-row">
            <div class="flex flex-col w-full mb-4 mr-0">
                <label for="tahun-ajaran" class="mb-1 font-semibold">Pilih Tahun Ajaran:</label>
                <select id="tahun-ajaran" name="tahun_ajaran" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 cursor-pointer" >
                    <option value="" hidden selected>Pilih Tahun Ajaran</option>
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->tahun_awal .'/'. $t->tahun_akhir .' '. $t->keterangan}}
                            </option>
                        @endforeach
                </select>
            </div>
        </div>

        </form>

      <div class="mt-2 mb-5 flex gap-4">
        <a href="{{route('dosen.export.dosen.excel')}}">
          <button class="flex items-center px-4 py-2.5 text-white bg-green-700 hover:bg-green-800 active:bg-green-900 rounded-sm font-semibold cursor-pointer">
            <i class="bi bi-file-earmark-excel mr-2"></i>
            <span>Export Excel</span>
          </button>
        </a>

        <a href="{{route('dosen.export.dosen.pdf')}}">
            <button class="flex items-center px-4 py-2.5 text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-sm font-semibold cursor-pointer">
            <i class="bi bi-filetype-pdf mr-2"></i>
            <span>Export Pdf</span>
            </button>
        </a>
      </div>

      <div x-data="{ hovering: false }" class="overflow-x-auto w-60 sm:w-150 md:w-240 xl:min-w-full mt-1 pb-3">
        <table id="data-rekap-dosen" class="table-auto text-sm text-left w-full pt-4 display nowrap dark:text-white dark:bg-gray-800">
            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10 dark:bg-gray-700 dark:text-gray-100">
                <tr>
                    <th @mouseenter="hovering = true" @mouseleave="hovering = false"
                        :class="hovering ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-100'"
                        class="border border-gray-300 px-4 py-2 dark:border-gray-600">No</th>
                    <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Program Studi</th>
                    <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Semester</th>
                    <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">Mata Kuliah</th>
                    @for ($i = 1; $i <= $totalPertemuan; $i++)
                        <th class="border border-gray-300 px-4 py-2 text-center dark:border-gray-600">{{ $i }}</th>
                    @endfor
                    <th class="border border-gray-300 px-4 py-2 dark:border-gray-600">%Mengajar</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @if (count($rekap))
                    @foreach ($rekap as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{$loop->iteration }}</td>
                        <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $item['nama_prodi'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $item['semester'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{ $item['nama_matkul'] }}</td>
                        @for ($i = 0; $i < $totalPertemuan; $i++)
                            @php
                                $status = $item['status_pertemuan'][$i] ?? null;
                                // $status = $tanggal ? 'M' : '-';
                                $bg = match($status) {
                                    'M' => 'text-green-500',
                                    '-' => 'text-gray-500',
                                    'UTS' => 'text-red-500',
                                    'UAS' => 'text-red-500',
                                };
                            @endphp
                            <td class="border px-4 py-2 font-semibold {{ $bg }} dark:border-gray-600" title="{{$item['nama_dosen']}}">{{ $status }}</td>
                        @endfor
                        <td class="border border-gray-300 px-4 py-2 dark:border-gray-600">{{$item['total_pertemuan']}}</td>
                    </tr>
                    @endforeach
                </tbody>
                @endif
        </table>

      </div>
      <div class="mt-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-5">Keterangan:</h2>
        <p class="mt-2"><span class="text-green-500 text-lg font-bold p-1">M</span> = Mengajar</p>
        <p class="mt-2"><span class="text-gray-500 font-bold text-xl p-1">-</span> = Tidak Terselenggara Perkuliahan</p>
      </div>

    </div>
</x-layout>

<div class="hidden">
    bg-green-500 bg-gray-500 text-white
</div>
