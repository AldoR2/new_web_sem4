<x-layout>
    <div class="h-full">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="text-gray-800 dark:text-gray-200">Tinjau detail kehadiran perkuliahan</p>

        <div class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
            <div class="mb-5 justify-start flex">
                <a href="{{route('dosen.presensi.index')}}">
                <button class="px-5 py-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">Batal</button>
                </a>
            </div>
            <h1 class="mb-2 text-2xl font-semibold text-gray-700 dark:text-gray-100">Dosen Pengajar</h1>
            <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                <table id="detail-presensi" class="text-sm text-left w-full pt-2" width="100%">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Tanggal</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Jam Perkuliahan</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Mata Kuliah</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Dosen</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Program Studi</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Semester</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Ruangan</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Tahun Ajaran</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Link Zoom</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-gray-800 dark:text-gray-100">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->tgl_presensi ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{substr($presensi->jam_awal,0,5) .' - '.substr($presensi->jam_akhir,0,5) ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->matkul->nama_matkul ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->dosen->nama ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->prodi->jenjang .' '.$presensi->prodi->nama_prodi ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->semester ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->ruangan->nama_ruangan ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->tahunAjaran->tahun_awal .'/'. $presensi->tahunAjaran->tahun_akhir .' '. $presensi->tahunAjaran->keterangan ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->link_zoom ?? '-'}}</td>
                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$presensi->link_zoom ? 'Daring' : 'Luring'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h1 class="mb-2 mt-6 text-2xl font-semibold text-gray-700 dark:text-gray-100">Mahasiswa</h1>
            <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                <table id="tbl-pres" class="text-sm text-left w-full pt-2">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">No</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nim</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Nama</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">waktu Presensi</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Presensi</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Alasan</th>
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-gray-800 dark:text-gray-100">
                        @foreach ($detail as $dp )
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$loop->iteration}}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$dp->mahasiswa->nim}}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$dp->mahasiswa->nama}}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$dp->waktu_presensi ?? '-'}}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    @switch($dp->status)
                                        @case(0)
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-red-500 rounded-full">Alpha</span>
                                        @break
                                        @case(1)
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-green-500 rounded-full">Hadir</span>
                                        @break
                                        @case(2)
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-gray-500 rounded-full">Izin</span>
                                        @break
                                        @case(3)
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-orange-500 rounded-full">Sakit</span>
                                        @break
                                        @default
                                            <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-gray-500 rounded-full">-</span>
                                        @endswitch
                                </td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{$dp->alasan ?? '-'}}</td>
                                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-center">
                                    <div x-data="{openEdit: false, status: '{{$dp->status}}', defaultStatus: '{{$dp->status}}', alasan: '{{$dp->alasan ?? ''}}', defaultAlasan: '{{$dp->alasan ?? ''}}'}" class="flex justify-center gap-2" x-init="openEdit = false, alasan = defaultAlasan">
                                        <button @click="openEdit = !openEdit;" class="cursor-pointer px-2 py-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-md">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </button>
                                        <form action="{{route('dosen.update-detail-presensi')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="mahasiswa_id" value="{{ $dp->mahasiswa_id }}">
                                            <input type="hidden" name="presensi_id" value="{{ $dp->presensi_id }}">
                                            <div x-show="openEdit" x-cloak x-transition class="fixed inset-0 z-50 flex justify-center items-center">
                                                <div class="absolute inset-0 bg-black opacity-50"></div>
                                                <div @click.outside="status = defaultStatus; alasan = defaultAlasan; openEdit = false" class="relative z-10 bg-white dark:bg-gray-900 rounded-lg shadow-2xl w-[90%] max-w-md p-6">
                                                    <div class="flex justify-center mb-4">
                                                        <div class="bg-blue-100 dark:bg-blue-300 rounded-full p-4">
                                                            <i class="bi bi-pencil-square text-4xl text-blue-600"></i>
                                                        </div>
                                                    </div>
                                                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white text-center mb-4">Ubah Presensi Manual</h2>
                                                    <div class="flex flex-wrap justify-center gap-4 mb-6 text-gray-800 dark:text-gray-100">
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="status" value="1" class="form-radio text-green-600" x-model="status">
                                                            <span class="ml-2">Hadir</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="status" value="2" class="form-radio text-gray-500" x-model="status">
                                                            <span class="ml-2">Izin</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="status" value="3" class="form-radio text-yellow-500" x-model="status">
                                                            <span class="ml-2">Sakit</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="status" value="0" class="form-radio text-red-600" x-model="status">
                                                            <span class="ml-2">Alpha</span>
                                                        </label>
                                                    </div>
                                                    <div class="mb-6 w-full">
                                                        <label for="alasan" class="block text-gray-700 dark:text-gray-200 mb-1">Alasan:</label>
                                                        <textarea id="alasan" name="alasan" x-model="alasan" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" x-bind:disabled="!(status == 2 || status == 3)"></textarea>
                                                    </div>

                                                    @if ($dp->bukti)
                                                        <a href="{{ asset('storage/bukti/' . $dp->bukti) }}" target="_blank" class="cursor-pointer px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-md" title="Download Bukti">
                                                            <i class="bi bi-download text-lg"></i>
                                                        </a>
                                                    @endif

                                                    <div class="flex justify-end space-x-3">
                                                        <button type="button" @click=" status = defaultStatus; alasan = defaultAlasan; openEdit = false;" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
