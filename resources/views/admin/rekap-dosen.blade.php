<x-layout>
    @vite(['resources/js/pages/admin/rekap-dosen.js'])
<div class="h-full dark:bg-gray-700 dark:text-gray-100">
<x-slot:title>{{ $title }}</x-slot:title>
<p class="text-lg">Lihat Rekap Presensi Dosen</p>

<div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
   <form action="{{route('admin.rekap-dosen.filter')}}" method="POST">
       @csrf
 <div class="flex flex-col md:flex-row">
       <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
           <label for="dosen" class="mb-1 font-semibold">Pilih Dosen:</label>
           <select id="dosen" name="dosen" class="bg-white dark:bg-gray-700 dark:text-white border dark:border-gray-600 rounded px-3 py-2" required>
               <option value="" hidden selected>Pilih Program Studi</option>
               @foreach ($dosen as $d)
                   <option value="{{ $d->id }}">{{ $d->nama }}</option>
               @endforeach
           </select>
       </div>

       <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0">
           <label for="tahun-ajaran" class="mb-1 font-semibold">Pilih Tahun Ajaran:</label>
           <select id="tahun-ajaran" name="tahun_ajaran" class="bg-white dark:bg-gray-700 dark:text-white border dark:border-gray-600 rounded px-3 py-2 w-full" required>
               <option value="" hidden selected>Pilih Tahun Ajaran</option>
               @foreach ($tahun as $t)
                   <option value="{{ $t->id }}">
                       {{ $t->tahun_awal .'/'. $t->tahun_akhir .' '. $t->keterangan}}
                   </option>
               @endforeach
           </select>
       </div>
   </div>

   <div class="w-full flex justify-end mb-6">
       <a href="{{route('admin.rekap-dosen.index')}}" class="px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">Reset</a>
       <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Cari</button>
   </div>
   </form>

@if ($dosenTerpilih && $tahunTerpilih)
   <div class="mt-2 mb-5 flex gap-4">
       <form action="{{route('admin.export.dosen.excel')}}" method="POST">
           @csrf
           <input type="hidden" name="dosen" value="{{ request('dosen') }}">
           <input type="hidden" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}">

           <button class="flex items-center px-4 py-2.5 text-white bg-green-700 hover:bg-green-800 active:bg-green-900 rounded-sm font-semibold cursor-pointer">
               <i class="bi bi-file-earmark-excel mr-2"></i>
               <span>Export Excel</span>
           </button>
       </form>

       <form action="{{ route('admin.export.dosen.pdf') }}" method="POST">
           @csrf
           <input type="hidden" name="dosen" value="{{ request('dosen') }}">
           <input type="hidden" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}">

           <button type="submit" class="flex items-center px-4 py-2.5 text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-sm font-semibold cursor-pointer">
               <i class="bi bi-filetype-pdf mr-2"></i>
               <span>Export PDF</span>
           </button>
       </form>
   </div>

   <div class="flex flex-col gap-3 mt-3">
       <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
           <span for="nip" class="w-20 font-semibold">NIP:</span>
           <span class="border border-gray-100 dark:border-gray-600 bg-gray-150 dark:bg-gray-700 rounded px-3 py-2 w-full">{{$dosenTerpilih->nip ?? ''}}</span>
       </div>

       <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
           <span for="nama" class="w-20 font-semibold">Nama:</span>
           <span class="border border-gray-100 dark:border-gray-600 bg-gray-150 dark:bg-gray-700 rounded px-3 py-2 w-full">{{$dosenTerpilih->nama ?? ''}}</span>
       </div>
   </div>
@endif

<div x-data="{ hovering: false }" class="overflow-x-auto w-60 sm:w-150 md:w-240 xl:min-w-full mt-1 pb-3">
   <table id="data-rekap-dosen" class="text-sm text-left w-full pt-4 display nowrap">
       <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white sticky top-0 z-10">
           <tr>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Program Studi</th>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Semester</th>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Mata Kuliah</th>
               @for ($i = 1; $i <= $totalPertemuan; $i++)
                   <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center">{{ $i }}</th>
               @endfor
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Hadir</th>
           </tr>
       </thead>
       <tbody class="text-center dark:text-white">
           @if (count($rekap))
               @foreach ($rekap as $index => $item)
               <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                   <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$loop->iteration }}</td>
                   <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $item['nama_prodi'] }}</td>
                   <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $item['semester'] }}</td>
                   <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $item['nama_matkul'] }}</td>
                   @for ($i = 0; $i < $totalPertemuan; $i++)
                       @php
                           $status = $item['status_pertemuan'][$i] ?? null;
                           $bg = match($status) {
                                'M' => 'text-green-500',
                                '-' => 'text-gray-500',
                                'UTS' => 'text-red-500',
                                'UAS' => 'text-red-500'
                           };
                       @endphp
                       <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 font-semibold {{ $bg }}" title="{{ $item['nama_dosen'] }}">{{ $status }}</td>
                   @endfor
                   <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $item['total_pertemuan'] }}</td>
               </tr>
               @endforeach
           @endif
       </tbody>
   </table>
</div>

<div class="mt-6">
   <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-5">Keterangan:</h2>
   <p class="mt-2"><span class="text-green-500 font-bold p-1">M</span> = Mengajar</p>
   <p class="mt-2"><span class="text-gray-500 font-bold p-1 ">-</span> = Tidak Terselenggara Perkuliahan</p>
</div>

</div>
</x-layout>
