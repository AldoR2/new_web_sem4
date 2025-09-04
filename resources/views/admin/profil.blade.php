<x-layout>
    <div x-data="{
        photoPreview: null,
        originalPhoto: '{{ isset($user->admin) && $user->admin->foto ? asset('storage/' . $user->admin->foto) : asset('images/profil-kosong.png') }}'
    }">
        <div class="mb-9">
            <x-slot:title>{{ $title }}</x-slot:title>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <form action="{{route('admin.profile.update')}}" enctype="multipart/form-data" method="POST" class="form-validasi">
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
                                    <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-xl ring-4 ring-blue-100 dark:ring-blue-900">
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
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4">
                        <h2 class="font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informasi Detail
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 pb-2 border-b-2 border-red-200 dark:border-red-800 flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                Informasi Pribadi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Nama Lengkap
                                    </label>
                                    <div class="relative">
                                        <input type="text" disabled
                                               class="w-full p-3  border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                               value="{{old('nama', $user->admin->nama ?? '')}}">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Email
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{$user->admin->email}}">
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
                                        <input type="text"
                                               disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{$user->admin->no_telp}}">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Agama
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{$user->admin->agama ?? ''}}">
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
                                        <input type="text"
                                               disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{$user->admin->tempat_lahir .', '. $user->admin->tgl_lahir}}">
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
                                        <input type="text"
                                               disabled
                                               class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg"
                                               value="{{ $user->admin->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 pb-2 border-b-2 border-orange-200 dark:border-orange-800 flex items-center">
                                <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                                Informasi Alamat
                            </h3>

                            <div class="space-y-2">
                                <label class="flex items-center text-sm font-semibold text-gray-600 dark:text-gray-300">
                                    <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Alamat Lengkap
                                </label>
                                <div class="relative">
                                    <textarea disabled
                                              rows="3"
                                              class="w-full p-3 border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg resize-none">{{$user->admin->alamat}}, {{$user->admin->kelurahan->name}}, {{$user->admin->kecamatan->name}}, {{$user->admin->kota->name}}, {{$user->admin->provinsi->name}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
