<x-layout>
    <div class="dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="mb-3">Hari Ini:
            <span class="text-md">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </p>

        @php
            $mahasiswa = Auth::user()->mahasiswa;
            $foto = $mahasiswa && $mahasiswa->jenis_kelamin === 'P'
                ? asset('images/halo-cewe.png')
                : asset('images/halo.png');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="flex flex-col gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 space-y-6 border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl p-6 sm:p-10 py-10 flex flex-col sm:flex-row items-center sm:items-start gap-6 shadow-md">
                        <div class="text-center sm:text-left">
                            <h2 class="text-2xl sm:text-3xl font-bold leading-snug"> Selamat datang, <br>
                                <span class="font-extrabold text-white drop-shadow-md">{{Auth::user()->name ?? ''}}</span> 👋
                            </h2>
                            <p class="text-sm sm:text-base mt-2 text-white/90">Semoga harimu menyenangkan dan produktif!</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 flex flex-col">
                    <div class="mb-3 p-4 rounded-t-xl border-b-2 border-gray-300 dark:border-gray-700 flex justify-between items-center flex-wrap gap-2">
                        <h1 class="text-lg font-semibold">Jadwal Perkuliahan Hari ini</h1>
                    </div>

                    <div class="px-3 pb-3 overflow-x-auto max-h-[300px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600 text-sm" id="data-mengajar">
                            <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold">No</th>
                                    <th class="px-4 py-2 text-left font-semibold">Tanggal Pekuliahan</th>
                                    <th class="px-4 py-2 text-left font-semibold">Jam Perkuliahan</th>
                                    <th class="px-4 py-2 text-left font-semibold">Mata Kuliah</th>
                                    <th class="px-4 py-2 text-left font-semibold">Dosen Pengajar</th>
                                    <th class="px-4 py-2 text-left font-semibold">Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($presensiHariIni as $p)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{$p->tgl_presensi ?? ''}}</td>
                                        <td class="px-4 py-2">{{substr($p->jam_awal,0,5) .' - '. substr($p->jam_akhir,0,5) ?? ''}}</td>
                                        <td class="px-4 py-2">{{$p->pertemuan->matkul->nama_matkul ?? ''}}</td>
                                        <td class="px-4 py-2">{{$p->dosen->nama ?? ''}}</td>
                                        <td class="px-4 py-2">{{$p->ruangan->nama_ruangan ?? '-'}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div x-data="{
                    openModal: false,
                    originalPhoto: '{{ isset($biodata) && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('images/profil-kosong.png') }}',
                    photoPreview: null,
                    resetPreview() {
                        this.photoPreview = null;
                    }
                }" class="bg-white pb-20 dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">

                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-t-xl px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Biodata Mahasiswa
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-6">
                            <div class="flex flex-col items-center lg:items-start">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-lg ring-4 ring-blue-100 dark:ring-blue-900">
                                        <img :src="originalPhoto" alt="Foto Mahasiswa" class="w-full h-full object-cover">
                                    </div>

                                    <div @click="openModal = true" class="absolute inset-0 rounded-full bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-all duration-300">
                                        <div class="text-center text-white">
                                            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium">Edit Foto</span>
                                        </div>
                                    </div>
                                </div>

                                <button @click="openModal = true" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Ubah Foto
                                </button>
                            </div>

                            <div class="flex-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-4">
                                        <h3 class="text-lg font-semibold border-b-2 border-blue-200 dark:border-blue-800 pb-2">
                                            Informasi Pribadi
                                        </h3>

                                        <div class="space-y-3">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 block">Nama Lengkap</span>
                                                    <span class="font-medium">{{$biodata->nama}}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 block">NIM</span>
                                                    <span class="font-medium">{{$biodata->nim}}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Jenis Kelamin</span>
                                                    <span class="font-medium">{{$biodata->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Tempat, Tanggal Lahir</span>
                                                    <span class="font-medium">{{$biodata->tempat_lahir}}, {{$biodata->tgl_lahir}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <h3 class="text-lg font-semibold border-b-2 border-green-200 dark:border-green-800 pb-2">
                                            Informasi Akademik
                                        </h3>

                                        <div class="space-y-3">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Program Studi</span>
                                                    <span class="font-medium">{{$biodata->prodi->nama_prodi}}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Semester</span>
                                                    <span class="font-medium">{{$biodata->semester}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <h3 class="text-lg font-semibold border-b-2 border-purple-200 dark:border-purple-800 pb-2">
                                        Informasi Kontak
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Email</span>
                                                <span class="font-medium break-all">{{$biodata->email}}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">No. Telepon</span>
                                                <span class="font-medium">{{$biodata->no_telp}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Alamat Lengkap</span>
                                            <span class="font-medium">
                                                {{$biodata->alamat}}, {{$biodata->kelurahan->name}}, {{$biodata->kecamatan->name}}, {{$biodata->kota->name}}, {{$biodata->provinsi->name}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="openModal"
                        x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 backdrop-blur-sm">

                        <div @click.outside="resetPreview(); openModal = false"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-96 max-w-full mx-4">

                            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-t-xl px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Upload Foto Profil
                                </h3>
                            </div>

                            <form method="POST" action="{{route('mahasiswa.profil.update')}}" enctype="multipart/form-data" class="p-6">
                                @csrf
                                @method('put')

                                <div class="mb-6 text-center">
                                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-blue-500 shadow-lg">
                                        <img :src="photoPreview || originalPhoto"
                                            alt="Preview"
                                            class="w-full h-full object-cover">
                                    </div>
                                </div>

                                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-blue-800 dark:text-blue-200 text-center">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Format yang didukung: <span class="font-semibold">JPEG, JPG, PNG</span><br>
                                        Ukuran maksimal: <span class="font-semibold">2MB</span>
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <label for="foto" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-lg shadow-md cursor-pointer transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        Pilih Foto Baru
                                    </label>

                                    <input type="file" name="foto" id="foto" accept="image/jpeg,image/jpg,image/png" class="hidden"
                                        @change="
                                            const file = $event.target.files[0];
                                            if (file) {
                                                if (file.size > 2048000) {
                                                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                                                    $event.target.value = '';
                                                    return;
                                                }
                                                const reader = new FileReader();
                                                reader.onload = (e) => photoPreview = e.target.result;
                                                reader.readAsDataURL(file);
                                            }
                                        ">
                                </div>

                                <div class="flex gap-3">
                                    <button type="button" @click="resetPreview(); openModal = false" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                                        Batal
                                    </button>
                                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors duration-200 transform hover:scale-105">
                                        Simpan Foto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
</script>
