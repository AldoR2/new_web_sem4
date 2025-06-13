<x-layout>
        {{-- @vite(['resources/js/components/dashboard.js']) --}}

  <x-slot:title>{{ $title }}</x-slot:title>
  <p class="mb-3 text-gray-600 dark:text-gray-300">Hari Ini: <span class="text-md text-gray-800 dark:text-white">
    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
  </span></p>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- KOLOM KIRI -->
    <div class="lg:col-span-2 flex flex-col gap-6">

      <!-- Greetings -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 space-y-6 border border-gray-200 dark:border-gray-700">
        <div class="bg-gradient-to-br from-sky-500 via-cyan-500 to-teal-400
         dark:bg-gradient-to-br dark:from-indigo-900 dark:via-blue-800 dark:to-teal-900
         text-white rounded-2xl p-6 sm:p-10 py-10 flex flex-col sm:flex-row items-center sm:items-start gap-6 shadow-md">
          <img src="{{ asset('images/halo.png') }}" alt="Halo Image" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-full border-4 border-white shadow-md">
          <div class="text-center sm:text-left">
            <h2 class="text-2xl sm:text-3xl font-bold leading-snug">
              Selamat datang, <br>
              <span class="font-extrabold text-white drop-shadow-md">{{Auth::user()->name ?? ''}}</span> 👋
            </h2>
            <p class="text-sm sm:text-base mt-2 text-white/90">Semoga harimu menyenangkan dan produktif!</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 flex flex-col">
        <!-- Header -->
        <div class="mb-4 px-4 py-3 rounded-t-xl border-b border-gray-300 dark:border-gray-600 flex justify-between items-center flex-wrap gap-2 bg-gray-50 dark:bg-gray-700">
          <h1 class="text-gray-700 dark:text-gray-100 text-lg font-bold">Jadwal Perkuliahan Hari Ini</h1>
        </div>

        <!-- Tabel Scrollable -->
        <div class="px-3 pb-3 overflow-x-auto max-h-[300px] overflow-y-auto">
          <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600 text-sm" id="data-mengajar">
            <thead class="sticky top-0 z-10 text-gray-700 dark:text-gray-100 bg-gray-100 dark:bg-gray-700">
              <tr>
                <th class="px-4 py-2 text-left font-semibold">No</th>
                <th class="px-4 py-2 text-left font-semibold">Tanggal Pekuliahan</th>
                <th class="px-4 py-2 text-left font-semibold">Jam Perkuliahan</th>
                <th class="px-4 py-2 text-left font-semibold">Mata Kuliah</th>
                <th class="px-4 py-2 text-left font-semibold">Dosen Pengajar</th>
                <th class="px-4 py-2 text-left font-semibold">Ruangan</th>
              </tr>
            </thead>
            <tbody class="text-gray-800 dark:text-gray-200">
              @foreach ($presensiHariIni as $p)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                <td class="px-4 py-2">{{$p->tgl_presensi ?? ''}}</td>
                <td class="px-4 py-2">{{substr($p->jam_awal,0,5) .' - '. substr($p->jam_akhir,0,5)}}</td>
                <td class="px-4 py-2">{{$p->matkul->nama_matkul ?? ''}}</td>
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

      <div x-data="{ openModal: false, photoPreview: '{{ asset('images/profil.jpg') }}' }"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 h-full">

      <h2 class="text-lg font-semibold text-gray-600 dark:text-gray-100 mb-6 border-b border-gray-200 dark:border-gray-600 pb-3">
        Biodata Mahasiswa
      </h2>

      <div class="flex flex-col items-center text-sm text-gray-700 dark:text-gray-300 space-y-4">
        <div class="relative group">
          <img src="{{ isset($biodata) && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('images/profil-kosong.png') }}" alt="Foto Mahasiswa"
              class="w-28 h-28 rounded-full object-cover border-4 border-gray-600 shadow-md">

          <div @click="openModal = true"
              class="absolute inset-0 rounded-full bg-black/50 dark:bg-gray-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition">
            <span class="text-white text-xs font-semibold">Edit Foto</span>
          </div>
        </div>

        <div class="w-full space-y-3">
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Nama:</span><span class="text-right">{{$biodata->nama}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">NIM:</span><span class="text-right">{{$biodata->nim}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Program Studi:</span><span class="text-right">{{$biodata->prodi->jenjang .' '. $biodata->prodi->nama_prodi}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Semester:</span><span class="text-right">{{$biodata->semester}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Email:</span><span class="text-right">{{$biodata->email}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">No. Telepon:</span><span class="text-right">{{$biodata->no_telp}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Jenis Kelamin:</span><span class="text-right">{{$biodata->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}}</span></div>
                <div class="flex justify-between"><span class="font-medium text-gray-500 dark:text-gray-400">Tempat Tanggal Lahir:</span><span class="text-right">{{$biodata->tempat_lahir .' '. $biodata->tgl_lahir}}</span></div>
                <div class="flex justify-between gap-1"><span class="font-medium text-gray-500 dark:text-gray-400">Alamat:</span><span class="text-right">{{$biodata->provinsi->name .', '. $biodata->kota->name .', '. $biodata->kecamatan->name .', '. $biodata->kelurahan->name .', '. $biodata->alamat}}</span></div>
        </div>
      </div>

      <div x-show="openModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 backdrop-blur-sm">

      <div @click.outside="openModal = false"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-96 max-w-full p-6">

        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 text-center">Upload Foto Baru</h3>

        <form method="POST" action="{{route('mahasiswa.profil.update')}}" enctype="multipart/form-data">
            @csrf
            @method('put')

          <!-- Preview Foto -->
          <template x-if="photoPreview">
            <div class="mb-4">
              <img src="{{ isset($biodata) && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('images/profil-kosong.png') }}" id="previewImage"
                  class="w-32 h-32 mx-auto rounded-full object-cover border-2 border-sky-500 shadow">
            </div>
          </template>

          <p class="text-gray-600 dark:text-gray-300 text-sm text-center mb-4">
            Format file yang didukung: <span class="font-medium">JPEG, JPG, PNG</span>
          </p>

          <!-- Tombol Upload dan Hapus -->
          <div class="flex justify-center gap-3 mb-6">
            <label for="foto"
                  class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow cursor-pointer transition">
              Unggah Foto
            </label>

            <input type="file" name="foto" id="foto" accept="image/*" class="hidden"
                  @change="
                    const file = $event.target.files[0];
                    if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => photoPreview = e.target.result;
                      reader.readAsDataURL(file);
                    }
                  ">
          </div>

          <!-- Tombol Aksi -->
          <div class="flex justify-end mt-10 gap-3">
            <button type="button"
                    @click="openModal = false"
                    class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 hover:bg-gray-400 dark:hover:bg-gray-600">
              Batal
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm rounded bg-green-600 hover:bg-green-700 text-white">
              Submit
            </button>
          </div>
        </form>
      </div>
    </div>

    </div>
    </div>
</x-layout>

<script>
    $(document).ready(function () {
    table = $("#data-mengajar").DataTable({
        searching: false, // Aktifkan pencarian
        paging: false, // Aktifkan pagination
        info: false, // Menampilkan informasi tabel
        scrollX: true, // Aktifkan scroll horizontal
        autoWidth: false, // Hindari ukuran otomatis
    });
});
</script>
