<x-layout>
    @vite(['resources/js/pages/admin/data-ruangan.js'])
    <div class="relative dark:text-white">
    <x-slot:title>{{ $title }}</x-slot:title>
    <p class="dark:text-gray-300">Lihat Data Ruangan</p>

    @if(session('success'))
        <div class="p-4 mb-4 text-green-800 dark:text-green-200 rounded-lg bg-green-100 dark:bg-green-900" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{openImport: false}" class="w-full overflow-x-auto max-w-full mt-5 p-5 bg-white dark:bg-gray-800 rounded-sm shadow-xl">
        <div class="mt-2 mb-5 flex gap-4">
            <a href="{{route('admin.master-ruangan.create')}}">
                <button class="flex items-center px-4 py-2.5 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-sm font-semibold cursor-pointer">
                    <i class="bi bi-plus-square-fill mr-2"></i>
                    <span>Tambah</span>
                </button>
            </a>
        </div>

        <div class="overflow-x-auto w-[270px] sm:w-150 md:w-full mt-3 pb-3">
            <table id="data-ruangan" class="text-sm text-left w-full pt-1 display nowrap">
                <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class=" dark:border-gray-600 px-4 py-2">No</th>
                        <th class=" dark:border-gray-600 px-4 py-2">Ruangan</th>
                        <th class=" dark:border-gray-600 px-4 py-2 !text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ruangan as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class=" dark:border-gray-600 px-4 py-2 text-gray-800 dark:text-gray-100">{{$loop->iteration}}</td>
                            <td class=" dark:border-gray-600 px-4 py-2 text-gray-800 dark:text-gray-100">{{$r->nama_ruangan}}</td>
                            <td class=" dark:border-gray-600 px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{route('admin.master-ruangan.edit', $r->id)}}">
                                        <button class="cursor-pointer px-2 py-1 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white rounded-md">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </button>
                                    </a>

                                    <form action="{{ route('admin.master-ruangan.destroy', $r->id) }}" method="POST" class="form-hapus inline-block">
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
    </div>
</div>
</x-layout>
