<x-layout>
    @vite(['resources/js/pages/admin/data-presensi.js'])
    <div class="h-full">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-800 dark:text-gray-200">Silahkan tambahkan Data Presensi</p>

        <div class="w-full h-max max-w-full mt-5 p-8 bg-white dark:bg-gray-800 rounded-sm shadow-xl">

            {{-- @if ($errors->any())
                <div class="alert alert-danger text-red-600 dark:text-red-400">
                    <ul>
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <form action="{{ route('dosen.presensi.store') }}" method="POST" class="form-validasi">
                @csrf

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="prodi" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pilih Program Studi:</label>
                        <select id="prodi" name="prodi_id" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Program Studi</option>
                            @foreach ($prodi as $p)
                                <option value="{{ $p->id }}" {{ old('prodi_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->jenjang . ' ' . $p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-600 text-sm" id="prodi_id_error">@error('prodi_id'){{ $message }}@enderror</span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0">
                        <label for="semester" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pilih Semester:</label>
                        <select id="semester" name="semester" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Semester</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                        <span class="text-red-600 text-sm" id="semester_error">@error('semester'){{ $message }}@enderror</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="matkul" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pilih Matkul:</label>
                        <select id="matkul" name="matkul_id" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Matkul</option>
                        </select>
                        <span class="text-red-600 text-sm" id="matkul_id_error">@error('matkul_id'){{ $message }}@enderror</span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2 ">
                        <label for="ruangan" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pilih Ruangan:</label>
                        <select id="ruangan" name="ruangan_id" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Ruangan</option>
                            @foreach ($ruangan as $r)
                                <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-600 text-sm" id="ruangan_id_error">@error('ruangan_id'){{ $message }}@enderror</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="pertemuan" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pertemuan Ke :</label>
                        <select id="pertemuan" name="pertemuan_ke" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Pertemuan</option>
                                @for($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" {{ old('pertemuan_ke') == $i ? 'selected' : '' }}>
                                        {{$i}}
                                    </option>
                                @endfor
                        </select>
                        <span class="text-red-600 text-sm" id="pertemuan_ke_error">
                            @error('pertemuan_ke'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="status" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Status Pertemuan:</label>
                        <select id="status" name="status" class="w-full dark:bg-gray-700 dark:text-white dark:border-gray-600 border-2 border-gray-400 rounded-sm" required>
                            <option value="" hidden selected>Pilih Status</option>
                            <option value="aktif" {{old('status') == 'aktif' ? 'selected' : ''}}>Aktif</option>
                            <option value="libur" {{old('status') == 'libur' ? 'selected' : ''}}>Libur</option>
                            <option value="uts" {{old('status') == 'uts' ? 'selected' : ''}}>UTS</option>
                            <option value="uas" {{old('status') == 'uas' ? 'selected' : ''}}>UAS</option>
                        </select>
                        @error('status')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/3 mr-0 md:mr-4">
                        <label for="tgl_presensi" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Pilih Tanggal:</label>
                        <input type="date" id="tgl_presensi" name="tgl_presensi" value="{{ old('tgl_presensi') }}"
                            class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" required>
                        <span class="text-red-600 text-sm" id="tgl_presensi_error">@error('tgl_presensi'){{ $message }}@enderror</span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/3 mr-0 md:mr-4">
                        <label for="jam_awal" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Jam Mulai:</label>
                        <input type="time" id="jam_awal" name="jam_awal" value="{{ old('jam_awal') }}"
                            class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" required>
                        <span class="text-red-600 text-sm" id="jam_awal_error">@error('jam_awal'){{ $message }}@enderror</span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/3">
                        <label for="jam_akhir" class="mb-1 font-semibold text-gray-800 dark:text-gray-200">Jam Selesai:</label>
                        <input type="time" id="jam_akhir" name="jam_akhir" value="{{ old('jam_akhir') }}"
                            class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-sm" required>
                        <span class="text-red-600 text-sm" id="jam_akhir_error">@error('jam_akhir'){{ $message }}@enderror</span>
                    </div>
                </div>

                <div class="w-full flex justify-end mt-7">
                    <a href="{{ route('dosen.presensi.index') }}"
                        class="px-5 py-2 mr-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
