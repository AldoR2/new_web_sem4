<x-layout>
    @vite(['resources/js/pages/admin/data-presensi.js'])
    <div class="h-full dark:text-white">
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="dark:text-white">{{$subtitle}}</p>
        <div class="w-full h-max max-w-full mt-5 p-8 bg-white rounded-sm shadow-xl dark:bg-gray-800">

            <form action="{{ isset($presensi) ? route('admin.presensi.update', $presensi->id) : route('admin.presensi.store') }}" method="POST" class="form-validasi">
                @csrf
                @if (isset($presensi))
                    @method('PUT')
                    <input type="hidden" id="edit_id" value="{{ $presensi->id }}">
                @endif

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="prodi" class="mb-1 font-semibold dark:text-white">Pilih Program Studi:</label>
                        <select id="prodi" name="prodi_id" class="w-full" required @if (isset($presensi)) disabled @endif>
                            <option value="" hidden selected>Pilih Program Studi</option>
                            @foreach ($prodi as $p)
                            <option value="{{ $p->id }}" @if (old('prodi_id', $presensi->pertemuan->prodi_id ?? '') == $p->id) selected @endif>
                                    {{ $p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                            @if(isset($presensi))
                                <input type="hidden" name="prodi_id" value="{{ $presensi->pertemuan->prodi_id }}">
                            @endif
                        <span class="text-red-600 text-sm" id="prodi_id_error">
                            @error('prodi_id'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="semester" class="mb-1 font-semibold dark:text-white">Pilih Semester:</label>
                        <select id="semester" name="semester" class="w-full" required @if (isset($presensi)) disabled @endif>
                            <option value="" hidden selected>Pilih Senester</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" @if (old('semester', $presensi->pertemuan->semester ?? '') == $i) selected @endif>
                                        Semester {{$i}}
                                    </option>
                                @endfor
                        </select>
                            @if(isset($presensi))
                                <input type="hidden" name="semester" value="{{ $presensi->pertemuan->semester }}">
                            @endif
                        <span class="text-red-600 text-sm" id="semester_error">
                            @error('semester'){{ $message }}@enderror
                        </span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                        <label for="matkul" class="mb-1 font-semibold dark:text-white">Pilih Matkul:</label>
                        <select id="matkul" name="matkul_id" class="w-full" required @if (isset($presensi)) disabled @endif data-old="{{ old('matkul_id', $presensi->pertemuan->matkul_id ?? '') }}" data-matkul-text="{{ $presensi->pertemuan->matkul->nama_matkul ?? '' }}">
                        </select>
                        @if(isset($presensi))
                            <input type="hidden" name="matkul_id" value="{{ $presensi->pertemuan->matkul_id }}">
                        @endif
                        <span class="text-red-600 text-sm" id="matkul_id_error">
                            @error('matkul_id'){{ $message }}@enderror
                        </span>
                    </div>

                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                        <label for="dosen" class="mb-1 font-semibold dark:text-white">Pilih Dosen:</label>
                        <select id="dosen" name="dosen_id" required data-validate="presensi" @if (isset($presensi)) disabled @endif>
                            <option value="" hidden selected>Pilih Dosen</option>
                            @foreach ($dosen as $d)
                                <option value="{{ $d->id }}" @if (old('dosen_id', $presensi->dosen_id ?? '') == $d->id) selected @endif>
                                    {{ $d->nama }}
                                </option>
                            @endforeach
                        </select>
                            @if(isset($presensi))
                                <input type="hidden" name="dosen_id" value="{{ $presensi->dosen_id }}">
                            @endif
                        @error('dosen_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div x-data="{ status: '{{ old('status', $presensi->pertemuan->status ?? '') }}' }">
                    <div class="flex flex-col md:flex-row">
                        <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                            <label for="status" class="mb-1 font-semibold dark:text-white">Status Pertemuan:</label>
                            <select id="status" name="status" x-model="status"  class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                <option value="" hidden selected>Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="libur">Libur</option>
                                <option value="uts">UTS</option>
                                <option value="uas">UAS</option>
                            </select>
                            @error('status')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col w-full mb-4 md:w-1/2">
                            <label for="ruangan" class="font-semibold dark:text-white">Pilih Ruangan:</label>
                            <select id="ruangan" name="ruangan_id" class="w-full" x-bind:disabled="status === 'libur'" data-validate="presensi">
                                <option value="" hidden selected>Pilih Ruangan</option>
                                @foreach ($ruangan as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id', $presensi->ruangan_id ?? '') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-red-600 text-sm" id="ruangan_id_error">
                                @error('ruangan_id'){{ $message }}@enderror
                            </span>
                        </div>
                    </div>

                    @if (!isset($presensi))
                        <div
                            x-data="{
                                inputs: {{ Js::from(old('inputs') ? : [[
                                        'pertemuan_ke' => '',
                                        'jenis' =>'',
                                        'tgl_presensi' => '',
                                        'jam_awal' => '',
                                        'jam_akhir' => ''
                                    ]]
                                ) }},
                                errors: {{Js::from($errors->toArray())}},
                                init() {
                                    this.$watch('status', (value) => {
                                        if (value && value !== 'aktif') {
                                            this.inputs = [{
                                                pertemuan_ke: '',
                                                jenis: '',
                                                tgl_presensi: '',
                                                jam_awal: '',
                                                jam_akhir: ''
                                            }];
                                        }
                                    });
                                }
                            }">

                            <template x-for="(input, index) in inputs" :key="index">
                                <div class="md:flex-row mb-4 p-3 border rounded-md dark:border-gray-600">
                                    <div class="flex flex-col md:flex-row w-full mb-4">
                                        <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                            <label :for="'pertemuan_' + index" class="mb-1 font-semibold dark:text-white">Pertemuan Ke :</label>
                                            <select :id="'pertemuan_' + index" :name="`inputs[${index}][pertemuan_ke]`" x-model="input.pertemuan_ke"
                                                class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm" required data-validate="presensi">
                                                <option value="" hidden selected>Pilih Pertemuan</option>
                                                @for($i = 1; $i <= 50; $i++)
                                                    <option value="{{ $i }}">
                                                        {{$i}}
                                                    </option>
                                                @endfor
                                            </select>
                                            <span class="text-red-600 text-sm" :id="`inputs[${index}][pertemuan_ke]_error`"
                                                x-text="errors[`inputs.${index}.pertemuan_ke`] ? errors[`inputs.${index}.pertemuan_ke`][0] : ''">
                                            </span>
                                        </div>

                                        <div class="flex flex-col w-full mb-4 md:w-1/2">
                                            <label :for="'jenis_' + index" class="mb-1 font-semibold dark:text-white">Jenis Perkuliahan:</label>
                                            <select :id="'jenis_' + index" :name="`inputs[${index}][jenis]`" x-model="input.jenis"
                                                class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                                <option value="" hidden selected>Pilih Jenis Perkuliahan</option>
                                                <option value="teori">Teori</option>
                                                <option value="praktik">Praktik</option>
                                            </select>
                                            <span class="text-red-600 text-sm" :id="`inputs[${index}][jenis]_error`"
                                                x-text="errors[`inputs.${index}.jenis`] ? errors[`inputs.${index}.jenis`][0] : ''">
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:flex-row w-full">
                                        <div class="flex flex-col w-full mb-2 md:w-1/3 mr-0 md:mr-4">
                                            <label :for="'tgl_presensi_' + index" class="mb-1 font-semibold dark:text-white">Pilih Tanggal Perkuliahan</label>
                                            <input type="date" :id="'tgl_presensi_' + index" :name="`inputs[${index}][tgl_presensi]`" data-validate="presensi" x-model="input.tgl_presensi"
                                                class="p-2 border-2 mt-1 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                            <span class="text-red-600 text-sm" :id="`inputs[${index}][tgl_presensi]_error`"
                                                x-text="errors[`inputs.${index}.tgl_presensi`] ? errors[`inputs.${index}.tgl_presensi`][0] : ''">
                                            </span>
                                        </div>

                                        <div class="flex flex-col w-full mb-2 md:w-1/3 mr-0 md:mr-4">
                                            <label :for="'jam_awal_' + index" class="mb-2 font-semibold dark:text-white">Jam Mulai</label>
                                            <input type="time" :id="'jam_awal_' + index" :name="`inputs[${index}][jam_awal]`" data-validate="presensi" x-model="input.jam_awal"
                                                class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                            <span class="text-red-600 text-sm" :id="`inputs[${index}][jam_awal]_error`"
                                                x-text="errors[`inputs.${index}.jam_awal`] ? errors[`inputs.${index}.jam_awal`][0] : ''">
                                            </span>
                                        </div>

                                        <div class="flex flex-col w-full mb-2 md:w-1/3">
                                            <label :for="'jam_akhir_' + index" class="mb-2 font-semibold dark:text-white">Jam Selesai</label>
                                            <input type="time" :id="'jam_akhir_' + index" :name="`inputs[${index}][jam_akhir]`" data-validate="presensi" x-model="input.jam_akhir"
                                                class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                            <span class="text-red-600 text-sm" :id="`inputs[${index}][jam_akhir]_error`"
                                                x-text="errors[`inputs.${index}.jam_akhir`] ? errors[`inputs.${index}.jam_akhir`][0] : ''">
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end mt-2" x-show="inputs.length > 1" x-show="status && status === 'aktif'" x-transition x-cloak>
                                        <button type="button" class="px-3 py-1 bg-gray-500 text-white rounded-md hover:bg-gray-600 font-semibold"
                                            @click="inputs.splice(index, 1)">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div class="mt-4 mb-4" x-show="status && status === 'aktif'" x-transition x-cloak>
                                <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 font-semibold"
                                    @click="inputs.push({ pertemuan_ke: '', jenis: '', tgl_presensi: '', jam_awal: '', jam_akhir: '' })">
                                    Tambah Pertemuan
                                </button>
                            </div>
                        </div>
                    @endif

                    @if (isset($presensi))
                        <div
                            x-data="{
                                pertemuan_ke : '{{ old('pertemuan_ke',$presensi->pertemuan->pertemuan_ke ?? '') }}',
                                jenis : '{{ old('jenis',$presensi->pertemuan->jenis ?? '') }}',
                                tgl_presensi : '{{ old('tgl_presensi',$presensi->tgl_presensi ?? '') }}',
                                jam_awal : '{{ old('jam_awal',$presensi->jam_awal ?? '') }}',
                                jam_akhir : '{{old('jam_akhir', $presensi->jam_akhir ?? '') }}'
                            }">

                            <div class="md:flex-row mb-4 p-3 border rounded-md dark:border-gray-600">
                                <div class="flex flex-col md:flex-row w-full mb-4">
                                    <div class="flex flex-col w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                        <label for="pertemuan" class="mb-1 font-semibold dark:text-white">Pertemuan Ke :</label>
                                        <select id="pertemuan" name="pertemuan_ke" x-model="pertemuan_ke"
                                            class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm" required data-validate="presensi">
                                            <option value="" hidden selected>Pilih Pertemuan</option>
                                            @for($i = 1; $i <= 50; $i++)
                                                <option value="{{ $i }}">
                                                    {{$i}}
                                                </option>
                                            @endfor
                                        </select>
                                        <span class="text-red-600 text-sm" id="pertemuan_ke_error">
                                            @error('pertemuan_ke'){{ $message }}@enderror
                                        </span>
                                    </div>

                                    <div class="flex flex-col w-full mb-4 md:w-1/2">
                                        <label for="jenis" class="mb-1 font-semibold dark:text-white">Jenis Perkuliahan:</label>
                                        <select id="jenis" name="jenis" x-model="jenis"
                                            class="p-2 w-full border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm">
                                            <option value="" hidden selected>Pilih Jenis Perkuliahan</option>
                                            <option value="teori">Teori</option>
                                            <option value="praktik">Praktik</option>
                                        </select>
                                        <span class="text-red-600 text-sm" id="jenis_error">
                                            @error('jenis'){{ $message }}@enderror
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full">
                                    <div class="flex flex-col w-full mb-2 md:w-1/3 mr-0 md:mr-4">
                                        <label for="tgl_presensi" class="mb-1 font-semibold dark:text-white">Pilih Tanggal Perkuliahan</label>
                                        <input type="date" id="tgl_presensi" name="tgl_presensi" x-model="tgl_presensi"
                                            class="p-2 border-2 mt-1 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm" data-validate="presensi">
                                        <span class="text-red-600 text-sm" id="tgl_presensi_error">
                                            @error('tgl_presensi'){{ $message }}@enderror
                                        </span>
                                    </div>

                                    <div class="flex flex-col w-full mb-2 md:w-1/3 mr-0 md:mr-4">
                                        <label for="jam_awal" class="mb-2 font-semibold dark:text-white">Jam Mulai</label>
                                        <input type="time" id="jam_awal" name="jam_awal" x-model="jam_awal"
                                            class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm" data-validate="presensi">
                                        <span class="text-red-600 text-sm" id="jam_awal_error">
                                            @error('jam_awal'){{ $message }}@enderror
                                        </span>
                                    </div>

                                    <div class="flex flex-col w-full mb-2 md:w-1/3">
                                        <label for="jam_akhir" class="mb-2 font-semibold dark:text-white">Jam Selesai</label>
                                        <input type="time" id="jam_akhir" name="jam_akhir" x-model="jam_akhir"
                                            class="p-2 border-2 border-gray-400 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-sm" data-validate="presensi">
                                        <span class="text-red-600 text-sm" id="jam_akhir_error"
                                            @error('jam_akhir'){{ $message }}@enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="w-full flex justify-end">
                    <a href="{{route('admin.presensi.index')}}" class="mr-2 px-5 py-2 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-md cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
