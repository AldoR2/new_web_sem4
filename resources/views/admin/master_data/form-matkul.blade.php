<x-layout>
    <div class="h-full dark:text-white">
            @vite(['resources/js/components/form-validasi.js'])
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-800 dark:text-gray-100">Silahkan tambahkan data Mata Kuliah</p>
        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <form action="{{ isset($matkul) ? route('admin.master-matkul.update', $matkul->id) : route('admin.master-matkul.store') }}" method="POST">
                @csrf
                @if (isset($matkul))
                    @method('PUT')
                @endif

                <h1 class="font-bold text-gray-800 dark:text-white text-2xl mb-2 text-center xl:text-left">Informasi Umum</h1>
                <hr class="my-2 text-gray-600 dark:text-gray-300 mb-6">

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="nama_matkul" class="mb-1 font-semibold text-gray-700 dark:text-gray-200">Nama Mata Kuliah:</label>
                        <input type="text" class="p-2 py-[10.5px] border-2 border-gray-400 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white rounded-sm" name="nama_matkul" id="nama_matkul" value="{{old('nama_matkul', $matkul->nama_matkul ?? '')}}" required data-validate="matkul" placeholder="Contoh: Biologi">
                        <span class="text-red-600 text-sm" id="nama_matkul_error">
                            @error('nama_matkul'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="prodi_id" class="mb-1 font-semibold text-gray-700 dark:text-gray-200">Program Studi:</label>
                        <select class="p-2 py-[10.5px] w-full border-2 font-normal border-gray-400 dark:border-gray-600 rounded-sm bg-white dark:bg-gray-700 text-black dark:text-white" name="prodi_id" id="prodi_id" required>
                            <option value="" hidden selected>Pilih Program Studi</option>
                            @foreach ($prodi as $p)
                                <option value="{{ $p->id }}" @if (old('prodi_id', $matkul->prodi_id ?? '') == $p->id) selected @endif>
                                    {{ $p->jenjang.' '.$p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        @error('prodi_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="tahun_ajaran_id" class="mb-1 font-semibold text-gray-700 dark:text-gray-200">Tahun Ajaran:</label>
                        <select class="p-2 mt-1 py-[10.5px] w-full border-2 font-normal border-gray-400 dark:border-gray-600 rounded-sm bg-white dark:bg-gray-700 text-black dark:text-white" name="tahun_ajaran_id" id="tahun_ajaran_id" required>
                            <option value="" hidden selected>Pilih Tahun Ajaran</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}" @if (old('tahun_ajaran_id', $matkul->tahun_ajaran_id ?? '') == $t->id) selected @endif>
                                    {{ $t->tahun_awal. '/'. $t->tahun_akhir. ' '. $t->keterangan }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="semester" class="mb-1 font-semibold text-gray-700 dark:text-gray-200">Semester:</label>
                        <select class="p-2 mt-1 py-[10.5px] w-full border-2 font-normal border-gray-400 dark:border-gray-600 rounded-sm bg-white dark:bg-gray-700 text-black dark:text-white" name="semester" id="semester" required>
                            <option value="" hidden selected>Pilih Semester</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" @if (old('semester', $matkul->semester ?? '') == $i) selected @endif>
                                    Semester {{$i}}
                                </option>
                            @endfor
                        </select>
                        @error('semester')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="durasi_matkul" class="mb-1 font-semibold text-gray-700 dark:text-gray-200">SKS:</label>
                        <input type="number" class="p-2 border-2 border-gray-400 dark:border-gray-600 rounded-sm bg-white dark:bg-gray-700 text-black dark:text-white" name="durasi_matkul" id="durasi_matkul" value="{{old('durasi_matkul', $matkul->durasi_matkul ?? '')}}" required data-validate="matkul" placeholder="Contoh : 3">
                        <span class="text-red-600 text-sm" id="durasi_matkul_error">
                            @error('durasi_matkul'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/2"></div>
                </div>

                <div class="w-full flex justify-end mt-7">
                    <a href="{{route('admin.master-matkul.index')}}" class="px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">
                        Batal
                    </a>
                    <button class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
