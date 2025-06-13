<x-layout>
    <div class="h-full dark:text-white">
            @vite(['resources/js/components/form-validasi.js'])
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-700 dark:text-gray-300">Silahkan tambahkan data Program Studi</p>

        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <form action="{{ isset($prodi) ? route('admin.master-prodi.update', $prodi->id) : route('admin.master-prodi.store') }}" method="POST">
                @csrf
                @if (isset($prodi))
                    @method('PUT')
                    <input type="hidden" id="edit_id" value="{{ $prodi->id }}">
                @endif

                <h1 class="font-bold text-gray-800 dark:text-gray-100 text-2xl mb-2 text-center xl:text-left">Informasi Umum</h1>
                <hr class="my-2 border-gray-300 dark:border-gray-600 mb-6">

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 md:mr-8">
                        <label for="kode_prodi" class="mb-1 font-semibold text-gray-700 dark:text-gray-300">Kode Program Studi:</label>
                        <input type="text" class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" placeholder="Contoh: KEP" name="kode_prodi" id="kode_prodi" value="{{old('kode_prodi', $prodi->kode_prodi ?? '')}}" required>
                        <span class="text-red-600 text-sm" id="kode_prodi_error">
                            @error('kode_prodi'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="jenjang" class="mb-1 font-semibold text-gray-700 dark:text-gray-300">Jenjang:</label>
                        <select name="jenjang" id="jenjang" required class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm">
                            <option value="" hidden selected>Pilih Jenjang</option>
                            <option value="S1" {{ old('jenjang', $prodi->jenjang ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('jenjang', $prodi->jenjang ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="D3" {{ old('jenjang', $prodi->jenjang ?? '') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="D4" {{ old('jenjang', $prodi->jenjang ?? '') == 'D4' ? 'selected' : '' }}>D4</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 md:mr-8">
                        <label for="nama_prodi" class="mb-1 font-semibold text-gray-700 dark:text-gray-300">Nama Program Studi:</label>
                        <input type="text" name="nama_prodi" id="nama_prodi" value="{{old('nama_prodi', $prodi->nama_prodi ?? '')}}" required class="p-2 mt-1 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" placeholder="Contoh: Keperawatan">
                        <span class="text-red-600 text-sm" id="nama_prodi_error">
                            @error('nama_prodi'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/2"></div>
                </div>

                <div class="w-full flex justify-end mt-7">
                    <a href="{{ route('admin.master-prodi.index') }}" class="inline-block mr-2 px-5 py-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">
                        Batal
                    </a>
                    <button class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
