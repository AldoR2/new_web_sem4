<x-layout>
    @vite(['resources/js/pages/dosen/chart-dosen.js'])
    <div class="dark:text-white dark:bg-gray-700">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="mb-4">Hari ini:
        <span class="text-md text-gray-800 dark:text-gray-200">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </span>
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="flex flex-col justify-between rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 max-h-60 bg-white dark:bg-gray-800">

                <div class="bg-gradient-to-br from-sky-500 via-cyan-500 to-teal-400 text-white rounded-2xl p-6 py-12 mb-6 flex items-center gap-4 shadow-md">
                    <div>
                        <h2 class="text-2xl font-bold">
                        Selamat datang, <br>
                        <span class="font-extrabold">{{$user->nama}}</span> 👋
                        </h2>
                        <p class="text-sm mt-1">Semoga harimu menyenangkan dan produktif!</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 h-full flex flex-col">
                <h2 class="text-xl font-semibold text-gray-400 dark:text-gray-300 mb-4">Grafik Kehadiran Bulanan</h2>
                <div id="grafik-kehadiran" class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-md"></div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-5">
        <div class="w-[310px] md:w-3/4 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="mb-3 p-4 rounded-t-xl border-b-2 border-gray-300 dark:border-gray-700 flex justify-between items-center flex-wrap gap-2">
            <h1 class="text-gray-500 dark:text-gray-300 text-lg font-semibold">Daftar Mengajar Hari Ini</h1>
            </div>

            <div class="px-3 pb-3 overflow-x-auto max-h-[400px] overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="data-mengajar">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Mata Kuliah</th>
                    <th class="px-4 py-2 text-left">Jam Perkuliahan</th>
                    <th class="px-4 py-2 text-left">Program Studi</th>
                    <th class="px-4 py-2 text-left">Semester</th>
                    <th class="px-4 py-2 text-left">Ruangan</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-200">
                    @foreach ($presensiHariIni as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{$p->pertemuan->matkul->nama_matkul}}</td>
                        <td class="px-4 py-2">{{substr($p->jam_awal,0,5) .' - '. substr($p->jam_akhir,0,5)}}</td>
                        <td class="px-4 py-2">{{$p->pertemuan->prodi->nama_prodi ?? '-'}}</td>
                        <td class="px-4 py-2">{{$p->pertemuan->semester}}</td>
                        <td class="px-4 py-2">{{$p->ruangan->nama_ruangan ?? '-'}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>

              <div x-data="{ showAll: false }" class="w-[310px] md:w-1/4 bg-white dark:bg-gray-800 rounded-md shadow-xl">
            <div class="p-4 border-b-2 border-gray-300 dark:border-gray-600 flex justify-between items-center">
                <h1 class="text-gray-500 dark:text-white text-lg font-semibold tracking-wide">
                    Mahasiswa Tidak Hadir Hari Ini
                </h1>
            </div>

            <div class="p-4 space-y-4 text-sm text-gray-700 dark:text-gray-200 max-h-[430px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                @foreach ($tidakHadir as $index => $th)
                    <div x-show="showAll || {{ $index }} < 10" x-transition class="item-mahasiswa flex items-start gap-4 p-3 rounded-md border border-red-200 dark:border-gray-600 hover:shadow transition-all">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($th->mahasiswa->nama) }}&background=EF4444&color=fff" alt="{{ $th->mahasiswa->nama }}" class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <p class="nama font-semibold text-gray-800 dark:text-white">{{ $th->mahasiswa->nama }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-300 mb-1">
                                {{ $th->pertemuan->prodi->nama_prodi }} - Semester {{ $th->mahasiswa->semester }} • {{ $th->pertemuan->matkul->nama_matkul ?? '-' }}
                            </p>
                            <div class="flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-300 mb-1">
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 px-2 py-1 rounded-md">
                                    Ruangan: {{ $th->presensi->ruangan->nama_ruangan ?? '-' }}
                                </span>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-md">
                                    {{ $th->presensi->jam_awal }} - {{ $th->presensi->jam_akhir }}
                                </span>
                            </div>
                            @switch($th->status)
                                @case(0)
                                    <span class="inline-block text-xs font-medium text-red-700 bg-red-100 dark:bg-red-800 dark:text-red-300 px-2 py-0.5 rounded-md">Alpha</span>
                                    @break
                                @case(2)
                                    <span class="inline-block text-xs font-medium text-gray-700 bg-gray-100 dark:bg-gray-800 dark:text-gray-300 px-2 py-0.5 rounded-md">Izin</span>
                                    @break
                                @case(3)
                                    <span class="inline-block text-xs font-medium text-yellow-700 bg-yellow-100 dark:bg-yellow-800 dark:text-yellow-300 px-2 py-0.5 rounded-md">Sakit</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                @endforeach
            </div>

            @if (count($tidakHadir) > 10)
                <div class="p-4 text-center">
                    <button @click="showAll = !showAll" class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none">
                        <span x-show="!showAll">Lihat Semua</span>
                        <span x-show="showAll">Sembunyikan</span>
                    </button>
                </div>
            @endif
        </div>

    </div>
  </div>
</x-layout>

<script>
    $(document).ready(function () {
    table = $("#data-mengajar").DataTable({
        searching: false,
        paging: false,
        info: false,
        scrollX: true,
        autoWidth: false,
    });
});
    const chartData = @json($dosenMingguan ? [$dosenMingguan] : []);
</script>
