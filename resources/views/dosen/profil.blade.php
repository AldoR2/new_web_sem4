<x-layout>
    <div x-data="{
        photoPreview: null,
        originalPhoto: '{{ isset($user->dosen) && $user->dosen->foto ? asset('storage/' . $user->dosen->foto) : asset('images/profil-kosong.png') }}'
    }">
        <div class="mb-9">
            <x-slot:title>{{ $title }}</x-slot:title>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <form action="{{ route('dosen.profile.update') }}" enctype="multipart/form-data" method="POST" class="form-validasi">
                        @csrf
                        @method('patch')

                        <div class="bg-indigo-600 px-6 py-4">
                            <h2 class="font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Foto Profil
                            </h2>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col items-center">
                                <div class="relative group mb-6">
                                    <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-xl ring-4 ring-green-100 dark:ring-green-900">
                                        <img :src="photoPreview || originalPhoto" id="previewImage" class="w-full h-full object-cover" alt="Preview Foto">
                                    </div>
                                </div>

                                <div class="w-full mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center text-blue-800 dark:text-blue-200">
                                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-medium">Format: JPEG, JPG, PNG</p>
                                            <p class="text-xs opacity-80">Ukuran maksimal: 2MB</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mb-6">
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

                                    <label for="foto" class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-lg shadow-lg cursor-pointer transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        Pilih Foto Baru
                                    </label>
                                </div>

                                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white rounded-lg font-semibold shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Foto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 px-6 py-4">
                        <h2 class="font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informasi Detail
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 pb-2 border-b-2 border-blue-200 dark:border-blue-800 flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                Informasi Pribadi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Nama Lengkap
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               value="{{ old('nama', $user->dosen->nama ?? '') }}">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                        </svg>
                                        NIP
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="209393472384709">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Tempat, Tanggal Lahir
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->tempat_lahir . ', ' . $user->dosen->tgl_lahir }}">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Jenis Kelamin
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}">
                                    </div>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Agama
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->agama ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 pb-2 border-b-2 border-green-200 dark:border-green-800 flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                Informasi Kontak
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Email
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->email}}">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        Telepon
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->no_telp }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 pb-2 border-b-2 border-purple-200 dark:border-purple-800 flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                Informasi Akademik
                            </h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Program Studi
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->dosen->prodi->nama_prodi }}">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Alamat Lengkap
                                    </label>
                                    <div class="relative">
                                        <textarea disabled rows="3" class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg resize-none">{{ $user->dosen->alamat }}, {{ $user->dosen->kelurahan->name }}, {{ $user->dosen->kecamatan->name }}, {{ $user->dosen->kota->name }}, {{ $user->dosen->provinsi->name }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
