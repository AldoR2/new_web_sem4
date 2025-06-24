<x-layout>
    @vite(['resources/js/pages/admin/rekap-mahasiswa.js'])
<div class="h-full dark:bg-gray-700">
<x-slot:title>{{ $title }}</x-slot:title>
<p class="dark:text-white">Lihat Rekap Presensi Mahasiswa </p>
<div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
   <form action="{{route(Auth::user()->role . '.rekap-mahasiswa.filter')}}" method="post">
       @csrf

   <div class="flex flex-col xl:flex-row">
       @if (Auth::user()->role === 'admin')
       <!-- Program Studi -->
       <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
           <label for="prodi" class="mb-1 font-semibold dark:text-white">Pilih Program Studi:</label>
           <select id="prodi" name="prodi" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 border" required>
               <option value="" hidden selected>Pilih Program Studi</option>
               @foreach ($prodi as $p)
                   <option value="{{ $p->id }}">
                       {{ $p->jenjang .' '. $p->nama_prodi }}
                   </option>
               @endforeach
           </select>
       </div>

       <!-- Semester -->
       <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
           <label for="semester" class="mb-1 font-semibold dark:text-white">Pilih Semester:</label>
           <select id="semester" name="semester" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 border" required>
               <option value="" hidden selected>Pilih Semester</option>
               @for ($i = 1; $i <= 8; $i++)
               <option value="{{$i}}"> Semester {{$i}} </option>
               @endfor
           </select>
       </div>
       @endif

   @if (Auth::user()->role === 'dosen')
       <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
           <label for="prodi-dosen" class="mb-1 font-semibold dark:text-white">Pilih Program Studi:</label>
           <select id="prodi-dosen" name="prodi" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 border" required>
               <option value="" hidden selected>Pilih Program Studi</option>
               @foreach ($prodi as $p)
                   <option value="{{ $p->id }}">
                       {{ $p->jenjang .' '. $p->nama_prodi }}
                   </option>
               @endforeach
           </select>
       </div>

       <div class="flex flex-col w-full mb-4 xl:w-1/3 mr-0 md:mr-4">
           <label for="semester-dosen" class="mb-1 font-semibold dark:text-white">Pilih Semester:</label>
           <select id="semester-dosen" name="semester" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 border" required>
               <option value="" hidden selected>Pilih Semester</option>
               @for ($i = 1; $i <= 8; $i++)
               <option value="{{$i}}"> Semester {{$i}} </option>
               @endfor
           </select>
       </div>
   @endif

       <div class="flex flex-col w-full mb-4 xl:w-1/3">
           <label for="matkul" class="mb-1 font-semibold dark:text-white">Pilih Mata Kuliah:</label>
           <select id="matkul" name="matkul" class="dark:bg-gray-700 dark:text-white dark:border-gray-600 border" required>
           </select>
       </div>
   </div>

   <div class="w-full flex justify-end mb-6">
       <a href="{{route(Auth::user()->role .'.rekap-mahasiswa.index')}}" class="px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">Reset</a>
       <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Cari</button>
   </div>
</form>

@if ($prodiTerpilih && $matkulTerpilih && $semesterTerpilih)
 <div class="mt-4 flex gap-4">

   <form action="{{route(Auth::user()->role . '.export.mahasiswa.excel')}}" method="post">
       @csrf
       <input type="hidden" name="prodi" value="{{ request('prodi') }}">
       <input type="hidden" name="semester" value="{{ request('semester') }}">
       <input type="hidden" name="matkul" value="{{ request('matkul') }}">

       <button class="flex items-center px-4 py-2.5 text-white bg-green-700 hover:bg-green-800 active:bg-green-900 rounded-sm font-semibold cursor-pointer">
           <i class="bi bi-file-earmark-excel mr-2"></i>
           <span>Export Excel</span>
       </button>
   </form>

   <form action="{{ route(Auth::user()->role .'.export.mahasiswa.pdf') }}" method="POST">
       @csrf
       <input type="hidden" name="prodi" value="{{ request('prodi') }}">
       <input type="hidden" name="semester" value="{{ request('semester') }}">
       <input type="hidden" name="matkul" value="{{ request('matkul') }}">

       <button type="submit" class="flex items-center px-4 py-2.5 text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-sm font-semibold cursor-pointer">
           <i class="bi bi-filetype-pdf mr-2"></i>
           <span>Export PDF</span>
       </button>
   </form>

 </div>

       <div class="flex flex-col gap-3 mt-3">
           <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
               <span class="w-20 font-semibold dark:text-white">Program Studi:</span>
               <span class="border border-gray-100 dark:border-gray-600 bg-gray-150 dark:bg-gray-700 rounded px-3 py-2 w-full dark:text-white">{{$prodiTerpilih->nama_prodi ?? ''}}</span>
           </div>

           <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
               <span class="w-20 font-semibold dark:text-white">Semester:</span>
               <span class="border border-gray-100 dark:border-gray-600 bg-gray-150 dark:bg-gray-700 rounded px-3 py-2 w-full dark:text-white">{{$semesterTerpilih ?? ''}}</span>
           </div>

           <div class="flex flex-col md:flex-row items-start md:items-center gap-2">
               <span class="w-20 font-semibold dark:text-white">Mata Kuliah:</span>
               <span class="border border-gray-100 dark:border-gray-600 bg-gray-150 dark:bg-gray-700 rounded px-3 py-2 w-full dark:text-white">{{$matkulTerpilih->nama_matkul ?? ''}}</span>
           </div>
       </div>
@endif

 <div x-data="{ hovering: false }" class="overflow-x-auto w-60 sm:w-150 md:w-240 xl:min-w-full pb-3">
   <table id="data-rekap-mahasiswa" class="text-sm text-left w-full pt-4 dark:text-white display nowrap">
       <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100 sticky top-0 z-10">
           <tr>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nim</th>
               <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nama</th>
               @for ($i = 1; $i <= $totalPertemuan; $i++)
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
               <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['nim'] ?? ''}}</td>
               <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['nama_mahasiswa'] ?? ''}}</td>
               @for ($i = 1; $i <= $totalPertemuan; $i++)
                   @php
                       $tanggal = $item['tanggal_pertemuan'][$i] ?? null;
                       $status = $item['pertemuan'][$i] ?? '';
                       $dosen = $item['nama_dosen'][$i] ?? '';
                       switch ($status) {
                           case 'H':
                               $bg = 'text-green-500';
                               break;
                           case 'I':
                               $bg = 'text-blue-500';
                               break;
                           case 'S':
                               $bg = 'text-yellow-500';
                               break;
                           case 'A':
                               $bg = 'text-red-500';
                               break;
                           case 'UTS':
                               $bg = 'text-red-500';
                               break;
                           case 'UAS':
                               $bg = 'text-red-500';
                               break;
                           default:
                               $bg = 'text-gra-500';
                               break;
                       };
                   @endphp
                       <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 font-semibold {{ $bg }}" title="{{$tanggal .' '. $dosen}}">{{ $status }}</td>
               @endfor
               <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$item['kehadiran']}}</td>
           </tr>
           @endforeach
       </tbody>
       @endif
   </table>
 </div>
 <div class="mt-6">
   <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-5">Keterangan:</h2>
        <p class="mt-2 dark:text-white"><span class="text-green-500 font-bold p-1">H  = Hadir</span></p>
        <p class="mt-2 dark:text-white"><span class="text-blue-500 font-bold py-1 px-2">I  = Izin</span></p>
        <p class="mt-2 dark:text-white"><span class="text-yellow-500 font-bold py-1 px-2">S  = Sakit</span></p>
        <p class="mt-2 dark:text-white"><span class="text-red-500 font-bold p-1">A  = Alpha</span></p>
        <p class="mt-2 dark:text-white"><span class="text-gray-500 font-bold p-1">-  = Tidak terselenggara perkuliahan</span></p>
 </div>
</div>
</div>
</x-layout>
