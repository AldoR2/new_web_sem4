<x-layout>
    @vite(['resources/js/pages/superadmin/data-admin.js'])
    <div class="relative dark:text-white">
    <x-slot:title>{{ $title }}</x-slot:title>
    <p class="dark:text-gray-300">Daftar Seluruh Admin</p>
        {{-- <div x-data="{openImport: false, openView: false, file}" class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl"> --}}
        <div x-data="{ openImport: false, fileName: '', openView: false, resetFile() {
                    this.fileName = '';
                    const input = document.getElementById('file');
                    if (input) input.value = '';
                }
            }"
            class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">

            <div class="mt-2 mb-5 flex gap-4">
                <a href="{{route('superadmin.master-admin.create')}}">
                    <button class="flex items-center px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold cursor-pointer">
                        <i class="bi bi-plus-square-fill mr-2"></i>
                        <span>Tambah</span>
                    </button>
                </a>

                <button @click="openImport = !openImport" class="flex items-center px-4 py-2.5 text-white bg-green-600 hover:bg-green-700 active:bg-green-800 rounded-sm font-semibold cursor-pointer">
                    <i class="bi bi-plus-square-fill mr-2"></i>
                    <span>Import</span>
                </button>

                <div x-show="openImport" x-cloak class="fixed inset-0 z-50 flex justify-center items-center">
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <div @click.outside="openImport = false; resetFile()" class="relative z-10 bg-white dark:bg-gray-900 rounded-sm shadow-xl sm:w-[500px] w-[320px] max-w-full p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h1 class="text-gray-600 dark:text-gray-100 text-2xl font-semibold">Import Data Admin</h1>
                            <button @click="openImport = false; resetFile()"><i class="bi bi-x-lg text-2xl mb-4 cursor-pointer text-gray-600 dark:text-gray-100"></i></button>
                        </div>
                        <form method="POST" action="{{ route('admin.master-dosen.import') }}" enctype="multipart/form-data">
                            @csrf
                        <div class="flex flex-col items-center justify-center w-full h-50 border-4 border-gray-400 border-dashed mb-4">
                            <i class="bi bi-upload text-gray-600 dark:text-gray-100 text-2xl"></i>
                            <input type="file" id="file" name="file" accept=".xls,.xlsx" required class="hidden" @change="fileName = $event.target.files[0]?.name">
                            <label for="file" class="text-blue-600 dark:text-blue-600 text-center text-sm md:text-md cursor-pointer" >Jatuhkan dokumen anda disini</label>
                            {{-- <p id="file-name" class="text-sm text-blue-600 dark:text-blue-400 mt-1 hidden"></p> --}}
                                                <!-- Preview Nama File -->
                            <template x-if="fileName">
                                <p id="file-name" class="text-sm text-blue-600 dark:text-blue-400 mt-1">📄 <span x-text="fileName"></span></p>
                            </template>

                            <p class="text-gray-400 dark:text-gray-300">Didukung: VSC, XLS, XML, JSON</p>
                        </div>

                        <div class="mb-4 flex justify-center">
                            <button class="cursor-pointer px-8 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold text-white">Impor</button>
                        </div>
                    </form>
                        <div class="flex flex-col items-center justify-center w-full h-20 border-4 border-gray-400 border-dashed mb-4">
                            <p class="text-gray-600 dark:text-gray-100">Unduh template file impor <a href="{{asset('storage/template/template-admin.xlsx')}}" download class="text-blue-600">di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{openView: false}">
                <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
                    <table id="data-admin" class="text-sm text-left w-full pt-1 display nowrap">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100 sticky top-0 z-10">
                            <tr>
                            <th class=" dark:border-gray-500 px-4 py-2">No</th>
                            <th class=" dark:border-gray-500 px-4 py-2">Foto</th>
                            <th class=" dark:border-gray-500 px-4 py-2">Nama Lengkap</th>
                            <th class=" dark:border-gray-500 px-4 py-2">Jenis Kelamin</th>
                            <th class=" dark:border-gray-500 px-4 py-2">Email</th>
                            <th class=" dark:border-gray-500 px-4 py-2">Telepon</th>
                            <th class=" dark:border-gray-500 px-4 py-2 !text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="dark:text-white">
                            @foreach ($admin as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class=" dark:border-gray-500 px-4 py-2">{{$loop->iteration}}</td>
                                    <td class=" dark:border-gray-500 px-4 py-2">
                                        <div class="w-10 h-10 bg-red-200 rounded-full overflow-hidden">
                                            <img src="{{ $a->foto ? asset('storage/' . $a->foto) : asset('images/profil-kosong.png') }}" alt="Photo">
                                        </div>
                                    </td>
                                    <td class=" dark:border-gray-500 px-4 py-2">{{$a->nama}}</td>
                                    <td class=" dark:border-gray-500 px-4 py-2">{{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</td>
                                    <td class=" dark:border-gray-500 px-4 py-2">{{$a->email}}</td>
                                    <td class=" dark:border-gray-500 px-4 py-2">{{$a->no_telp}}</td>
                                    <td class=" dark:border-gray-500 px-4 py-2 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button @click="openView = true; $nextTick(() => loadAdminDetail({{ $a->id }}))" class="cursor-pointer px-2 py-1 bg-gray-600 hover:bg-gray-700 active:bg-gray-800 text-white rounded-md">
                                                <i class="bi bi-eye text-lg"></i>
                                            </button>
                                            <a href="{{route('superadmin.master-admin.edit', $a->id)}}">
                                                <button class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                                    <i class="bi bi-pencil-square text-lg"></i>
                                                </button>
                                            </a>
                                            <form action="{{ route('superadmin.master-admin.destroy', $a->id) }}" method="POST" class="form-hapus inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-md">
                                                    <i class="bi bi-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div x-show="openView" x-cloak x-transition class="shadow-xl fixed inset-0 z-50 flex justify-center items-center">
                    <div class="absolute inset-0 bg-black opacity-50 dark:opacity-70"></div>
                    <div @click.outside="openView = false" class="relative z-10 bg-white dark:bg-gray-800 rounded-sm shadow-xl sm:w-[500px] w-[305px] h-[600px] max-w-full p-6 overflow-y-scroll">
                        <div class="flex justify-between items-center mb-6 border-b pb-3 dark:border-gray-600">
                            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-100">View Data Admin</h2>
                            <button @click="openView = false" class="text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition">
                                <i class="bi bi-x-lg text-3xl"></i>
                            </button>
                        </div>

                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-indigo-500 shadow-lg mb-6 cursor-pointer">
                            <img id="foto" class="w-full h-full object-cover" alt="Photo">
                        </div>

                        <!-- INPUT FIELDS -->
                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="nama" class="mb-1 font-semibold dark:text-gray-200">Nama Lengkap:</label>
                                <input type="text" id="nama" readonly class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2">
                                <label for="jenis_kelamin" class="mb-1 font-semibold dark:text-gray-200">Jenis Kelamin:</label>
                                <input type="text" readonly id="jenis_kelamin" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="agama" class="mb-1 font-semibold dark:text-gray-200">Agama:</label>
                                <input type="text" id="agama" readonly class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2">
                                <label for="tempat_lahir" class="mb-1 font-semibold dark:text-gray-200">Tempat Lahir:</label>
                                <input type="text" readonly id="tempat_lahir" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="tgl_lahir" class="mb-1 font-semibold dark:text-gray-200">Tanggal Lahir:</label>
                                <input type="date" readonly id="tgl_lahir" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2">
                                <label for="email" class="mb-1 font-semibold dark:text-gray-200">Email:</label>
                                <input type="text" readonly id="email" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm" autocomplete="email">
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="no_telp" class="mb-1 font-semibold dark:text-gray-200">Nomor Telepon:</label>
                                <input type="text" readonly id="no_telp" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="w-full md:w-1/2 mb-4"></div>
                        </div>

                        <h1 class="font-bold text-gray-800 dark:text-white text-lg my-2 text-center xl:text-left mt-3">Alamat</h1>
                        <hr class="my-2 border-gray-600 mb-6">

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="provinsi" class="mb-1 font-semibold dark:text-gray-200">Provinsi:</label>
                                <input type="text" readonly id="provinsi" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2">
                                <label for="kota" class="mb-1 font-semibold dark:text-gray-200">Kota / Kabupaten:</label>
                                <input type="text" readonly id="kota" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="kecamatan" class="mb-1 font-semibold dark:text-gray-200">Kecamatan:</label>
                                <input type="text" readonly id="kecamatan" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2">
                                <label for="kelurahan" class="mb-1 font-semibold dark:text-gray-200">Kelurahan:</label>
                                <input type="text" readonly id="kelurahan" class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row">
                            <div class="flex flex-col items-start w-full mb-4 md:w-1/2 mr-0 md:mr-8">
                                <label for="alamat" class="mb-1 font-semibold dark:text-gray-200">Alamat:</label>
                                <input type="text" id="alamat" readonly class="bg-gray-100 dark:bg-gray-700 dark:text-white w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-sm">
                            </div>
                            <div class="w-full md:w-1/2 mb-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('file-name');

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                fileNameDisplay.textContent = `📄 ${file.name}`;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.add('hidden');
            }
        });
    });
</script>

