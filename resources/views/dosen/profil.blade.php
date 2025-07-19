<x-layout>
  <div>
      <x-slot:title>{{ $title }}</x-slot:title>
      <p class="text-gray-800 dark:text-gray-100">Ubah Profil Dosen Di Sini</p>

      <div class="w-full flex flex-col md:flex-row gap-5 mt-5">
          <div class="bg-white dark:bg-gray-800 shadow-md h-full w-full pb-5 md:basis-1/2">
              <form action="{{ route('dosen.profile.update') }}" enctype="multipart/form-data" method="POST" class="form-validasi">
                  @csrf
                  @method('patch')
                  <div class="flex items-center p-4 border-b-2 border-gray-200 dark:border-gray-600">
                      <i class="bi bi-person-circle mr-3 text-gray-800 dark:text-gray-200"></i>
                      <h2 class="font-bold text-gray-800 dark:text-gray-100">Foto Profile</h2>
                  </div>
                  <div class="flex flex-col justify-center items-center p-4">
                      <img src="{{ isset($user->dosen) && $user->dosen->foto ? asset('storage/' . $user->dosen->foto) : asset('images/profil-kosong.png') }}" id="previewImage" class="w-40 h-40 mb-4 hover:border-2 border-white hover:border-gray-300 active:border-gray-400 rounded-full object-cover" alt="Preview Foto">
                      <p class="mb-3 text-gray-500 dark:text-gray-300">Format tersedia hanya file JPEG, JPG, atau PNG</p>
                      <input type="file" name="foto" id="foto" accept="image/*" class="hidden">
                      <label for="foto" class="flex items-center px-5 py-2.5 text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 rounded-sm font-semibold cursor-pointer">Upload New Image</label>
                  </div>
                  <div class="px-8 py-4 flex justify-end">
                      <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Simpan</button>
                  </div>
              </form>
          </div>

          <div class="bg-white dark:bg-gray-800 shadow-md w-full md:basis-3/4 pb-5">
              <div class="flex items-center p-4 border-b-2 border-gray-200 dark:border-gray-600">
                  <i class="bi bi-person-fill mr-3 text-gray-800 dark:text-gray-200"></i>
                  <h2 class="font-bold text-gray-800 dark:text-gray-100">Detail Profile</h2>
              </div>
              <div class="px-8 py-4">
                  <div class="flex flex-col md:flex-row mb-4">
                      <div class="flex flex-col w-full">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Nama Lengkap:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ old('nama', $user->dosen->nama ?? '') }}">
                      </div>
                  </div>

                  <div class="flex flex-col md:flex-row">
                      <div class="flex flex-col w-full mb-4 mr-0 md:mr-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">NIP:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="209393472384709">
                      </div>
                      <div class="flex flex-col w-full mb-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Agama:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->agama ?? '' }}">
                      </div>
                  </div>

                  <div class="flex flex-col md:flex-row">
                      <div class="flex flex-col w-full mb-4 mr-0 md:mr-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Tempat Tanggal Lahir:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->tempat_lahir . ', ' . $user->dosen->tgl_lahir }}">
                      </div>
                      <div class="flex flex-col w-full mb-4">
                            <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Jenis Kelamin:</label>
                            <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 text-gray-700 rounded-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ $user->dosen->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}">
                      </div>
                  </div>

                  <div class="flex flex-col md:flex-row">
                      <div class="flex flex-col w-full mb-4 mr-0 md:mr-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Email:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->email}}">
                      </div>
                      <div class="flex flex-col w-full mb-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Telepon:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->no_telp }}">
                      </div>
                  </div>

                  <div class="flex flex-col md:flex-row">
                      <div class="flex flex-col w-full mb-4 mr-0 md:mr-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Program Studi:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->prodi->nama_prodi }}">
                      </div>
                      <div class="flex flex-col w-full mb-4">
                          <label class="mb-1 font-semibold text-gray-600 dark:text-gray-300">Alamat:</label>
                          <input type="text" disabled class="p-2 border-2 border-gray-700 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 rounded-sm" value="{{ $user->dosen->provinsi->name . ', ' . $user->dosen->kota->name . ', ' . $user->dosen->kecamatan->name . ', ' . $user->dosen->kelurahan->name .', '. $user->dosen->alamat }}">
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</x-layout>
