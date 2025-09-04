<x-layout>
  <div class="dark:text-white">
      <x-slot:title>{{ $title }}</x-slot:title>
      <p>Update Password Di Sini</p>

      @php
          if (auth()->user()->role === 'admin') {
              $route = route('admin.password.update');
          } elseif (auth()->user()->role === 'dosen') {
              $route = route('dosen.password.update');
          } elseif (auth()->user()->role === 'mahasiswa') {
              $route = route('mahasiswa.password.update');
          } elseif (auth()->user()->role === 'superadmin') {
              $route = route('superadmin.password.update');
          }
      @endphp

        <div class="mt-5" x-data="{
            showCurrentPassword: false,
            showPassword: false,
            showPasswordConfirmation: false
        }">
            <div class="bg-white shadow-md w-full dark:bg-gray-800">
                <form action="{{ $route }}" method="post">
                    @csrf
                    @method('put')

                    <div class="flex items-center p-4 border-b-2 border-gray-200 dark:border-gray-600">
                        <i class="bi bi-person-lock mr-3"></i>
                        <h2 class="font-bold">Ubah Password</h2>
                    </div>

                    <div class="p-4 border-b-2 border-gray-200 dark:border-gray-600">
                        <div class="mb-4">
                            <label for="current_password" class="mb-1 font-bold">Password Sekarang</label>
                            <div class="relative">
                                <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password" id="current_password" class="p-2 pr-10 border-2 border-gray-400 rounded-sm dark:bg-gray-800 w-full" required >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    @click="showCurrentPassword = !showCurrentPassword">

                                    <svg x-show="!showCurrentPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>

                                    <svg x-show="showCurrentPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L12 12m0 0l2.122 2.122m-2.122-2.122l2.122-2.122" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-red-600 text-sm" id="current_password_error">
                                @error('current_password'){{ $message }}@enderror
                            </span>
                        </div>
                    </div>

                    <div class="p-4 border-b-2 border-gray-200 dark:border-gray-600">
                        <div class="mb-6">
                            <label for="password" class="mb-1 font-bold">Password Baru</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" class="p-2 pr-10 border-2 border-gray-400 rounded-sm dark:bg-gray-800 w-full" required >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    @click="showPassword = !showPassword">

                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>

                                    <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L12 12m0 0l2.122 2.122m-2.122-2.122l2.122-2.122" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-red-600 text-sm" id="password_error">
                                @error('password'){{ $message }}@enderror
                            </span>
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1 font-bold">Konfirmasi Password</label>
                            <div class="relative">
                                <input :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" class="p-2 pr-10 border-2 border-gray-400 rounded-sm dark:bg-gray-800 w-full" required >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation">

                                    <svg x-show="!showPasswordConfirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>

                                    <svg x-show="showPasswordConfirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L12 12m0 0l2.122 2.122m-2.122-2.122l2.122-2.122" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-red-600 text-sm" id="password_confirmation_error">
                                @error('password_confirmation'){{ $message }}@enderror
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-md font-semibold cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
