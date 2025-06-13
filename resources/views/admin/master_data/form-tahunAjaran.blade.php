<x-layout>
    <div class="h-full dark:text-white">
            @vite(['resources/js/components/form-validasi.js'])
        <x-slot:title>{{ $title }}</x-slot:title>
        <p>Silahkan tambahkan data Tahun Ajaran</p>
        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <form action="{{ isset($tahun) ? route('admin.master-tahun.update', $tahun->id) : route('admin.master-tahun.store') }}" method="POST">
                @csrf
                @if (isset($tahun))
                    @method('PUT')
                @endif

                <h1 class="font-bold text-gray-800 dark:text-white text-2xl mb-2 text-center xl:text-left">Informasi Umum</h1>
                <hr class="my-2 border-gray-600 mb-6 dark:border-gray-400">

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="tahun_awal" class="mb-1 font-semibold dark:text-white">Tahun Mulai:</label>
                        <input list="tahun-list" name="tahun_awal" class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" id="tahun_awal" data-validate="tahun" placeholder="Contoh: 2023" value="{{ old('tahun_awal', $tahun->tahun_awal ?? '') }}" required>
                        <datalist id="tahun-list">
                            @for($i = date('Y'); $i >= 2000; $i--)
                                <option value="{{ $i }}">
                            @endfor
                        </datalist>
                        <span class="text-red-600 text-sm" id="tahun_awal_error">
                            @error('tahun_awal'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="tahun_akhir" class="mb-1 font-semibold dark:text-white">Tahun Selesai:</label>
                        <input list="tahun-list" name="tahun_akhir" class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" id="tahun_akhir" data-validate="tahun" placeholder="Contoh: 2024" value="{{ old('tahun_akhir', $tahun->tahun_akhir ?? '') }}" required>
                        <datalist id="tahun-list">
                            @for($i = date('Y'); $i >= 2000; $i--)
                                <option value="{{ $i }}">
                            @endfor
                        </datalist>
                        <span class="text-red-600 text-sm" id="tahun_akhir_error">
                            @error('tahun_akhir'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="keterangan" class="mb-1 font-semibold dark:text-white">Keterangan:</label>
                        <select class="p-2 mt-1 py-[10.5px] w-full border-2 font-normal border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" name="keterangan" id="keterangan" required>
                            <option value="" hidden selected>Pilih Keterangan</option>
                            <option value="Ganjil" {{ old('keterangan', $tahun->keterangan ?? '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('keterangan', $tahun->keterangan ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('keterangan')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8"></div>

                    @if (isset($tahun))
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="aktif" class="mb-1 font-semibold dark:text-white">Tahun Ajaran Aktif:</label>
                        <div class="flex items-center mt-2">
                            <input id="aktif" name="status" type="radio" class="mr-2 w-5 h-5" value="1" {{ old('status', $tahun->status ?? '') == 1 ? 'checked' : '' }}>
                            <label for="aktif" class="mr-9 text-gray-600 dark:text-white">Aktif</label>
                            <input id="tidakAktif" name="status" type="radio" class="mr-2 w-5 h-5" value="0" {{ old('status', $tahun->status ?? '') == 0 ? 'checked' : '' }}>
                            <label for="tidakAktif" class="text-gray-600 dark:text-white">Tidak Aktif</label>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="w-full flex justify-end mt-7">
                    <a href="{{ route('admin.master-tahun.index') }}" class="inline-block px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">
                        Batal
                    </a>
                    <button class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
