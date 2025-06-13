<x-layout>
    <div>
        <x-slot:title>{{ $title }}</x-slot:title>
        <p class="dark:text-white">Update Password Di Sini</p>

        @php
            if (auth()->user()->role === 'admin') {
                $route = route('admin.password.update');
            } elseif (auth()->user()->role === 'dosen') {
                $route = route('dosen.password.update');
            } elseif (auth()->user()->role === 'mahasiswa') {
                $route = route('mahasiswa.password.update');
            }elseif (auth()->user()->role === 'superadmin') {
                $route = route('superadmin.password.update');
            }
        @endphp

      <div class="mt-5">
        <div class="bg-white shadow-md w-full dark:bg-gray-800 dark:text-white ">

            {{-- <form action="{{route('admin.password.update')}}" method="post"> --}}
            <form action="{{$route}}" method="post">
                @csrf
                @method('put')

          <div class="flex items-center p-4 border-b-2 border-gray-200">
            <i class="bi bi-person-lock mr-3"></i>
            <h2 class="font-bold">Ubah Password</h2>
          </div>
          <div class="p-4 border-b-2 border-gray-200">
            <div class="mb-4">
              <div class="flex flex-col">
                <label for="current_password" class="mb-1 font-bold">Password Sekarang</label>
                <input type="password" name="current_password" id="current_password" class="p-2 border-2 border-gray-400 rounded-sm dark:bg-gray-800" data-validate="password" required>
              </div>
              <span class="text-red-600 text-sm" id="current_password_error">
                @error('current_password'){{ $message }}@enderror
            </span>
            </div>
          </div>

          <div class="p-4 border-b-2 border-gray-200">
            <div class="mb-6">
              <div class="flex flex-col">
                <label for="password" class="mb-1 font-bold">Password Baru</label>
                <input type="password" name="password" id="password" class="p-2 border-2 border-gray-400 rounded-sm dark:bg-gray-800" data-validate="password" required>
              </div>
              <span class="text-red-600 text-sm" id="password_error">
                @error('password'){{ $message }}@enderror
            </span>
            </div>
            <div>
              <div class="flex flex-col">
                <label for="password_confirmation" class="mb-1 font-bold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="p-2 border-2 border-gray-400 rounded-sm dark:bg-gray-800" data-validate="password" required>
              </div>
              <span class="text-red-600 text-sm" id="password_confirmation_error">
                @error('password_confirmation'){{ $message }}@enderror
            </span>
            </div>
          </div>
            <div class="p-4 flex justify-end">
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">Simpan</button>
            </div>
        </form>
        </div>
      </div>
    </div>

  </x-layout>
