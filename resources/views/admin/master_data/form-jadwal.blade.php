<x-layout>
    @vite(['resources/js/pages/admin/data-jadwal.js','resources/js/components/form-validasi.js'])
    <div class="h-full transition-colors duration-300">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-900 dark:text-gray-200">Silahkan tambahkan data Admin</p>
        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl transition-colors duration-300">

            <form action="{{route('admin.master-jadwal.store')}}" method="POST" class="form-presensi">
            @csrf

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="dosen" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Dosen:</label>
                        <select id="dosen" name="dosen_id" required
                            class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Dosen</option>
                            @foreach ($dosen as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="prodi" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Tahun Ajaran:</label>
                        <select id="tahun-ajaran" name="tahun_ajaran" class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required>
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Tahun Ajaran</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}" {{ old('tahun_ajaran_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->tahun_awal.'/'.$t->tahun_akhir .' '. $t->keterangan }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-600 text-sm" id="prodi_id_error">
                            @error('prodi_id'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">

                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="prodi" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Program Studi:</label>
                        <select id="prodi" name="prodi_id" class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required>
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Program Studi</option>
                            @foreach ($prodi as $p)
                                <option value="{{ $p->id }}" {{ old('prodi_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->jenjang.' '.$p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-600 text-sm" id="prodi_id_error">
                            @error('prodi_id'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/2 ">
                        <label for="semester" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Semester:</label>
                        <select id="semester" name="semester" class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required >
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Semester</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                        Semester {{$i}}
                                    </option>
                                @endfor
                        </select>
                        <span class="text-red-600 text-sm" id="semester_error">
                            @error('semester'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">

                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="matkul" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Matkul:</label>
                        <select id="matkul" name="matkul_id"  class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required>
                            {{-- <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Matkul</option> --}}
                        </select>
                        <span class="text-red-600 text-sm" id="matkul_id_error">
                            @error('matkul_id'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="ruangan" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Ruangan:</label>
                        <select id="ruangan" name="ruangan_id" class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required>
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Ruangan</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-600 text-sm" id="ruangan_id_error">
                            @error('ruangan_id'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/3 mr-0 md:mr-4">
                        <label for="hari" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Pilih Hari:</label>
                        <select id="hari" name="hari" class="w-full p-2 border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600" required>
                            <option value="" hidden selected class="text-gray-500 dark:text-gray-400">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                        <span class="text-red-600 text-sm" id="hari_error">
                            @error('hari'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/3 mr-0 md:mr-4">
                        <label for="durasi" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Durasi</label>
                        <input type="number" id="durasi" name="durasi" value="{{old('durasi')}}" placeholder="Contoh : 2" required
                            class="p-2 w-full border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400" data-validate="jadwal">
                        <span class="text-red-600 text-sm" id="durasi_error">
                            @error('durasi'){{ $message }}@enderror
                        </span>
                    </div>
                    <div class="flex flex-col w-full mb-4 md:w-1/3">
                        <label for="jam" class="mb-1 font-semibold text-gray-900 dark:text-gray-300">Jam Jadwal</label>
                        <input type="time" id="jam" name="jam" value="{{old('jam')}}" required
                            class="p-2 w-full border-2 border-gray-400 rounded-sm bg-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400" >
                        <span class="text-red-600 text-sm" id="jam_error">
                            @error('jam'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="w-full flex justify-end mt-7">
                    <a href="{{route('admin.master-jadwal.index')}}"
                       class="px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer transition-colors duration-300">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer transition-colors duration-300">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
