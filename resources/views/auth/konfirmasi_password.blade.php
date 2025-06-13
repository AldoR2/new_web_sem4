<x-layoutAuth title="lupa_password">
  <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-100 to-gray-100 px-4">
      <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
          <div class="w-full flex justify-center">
            <img src="{{ asset('images/stipress.png') }}" alt="Logo Aplikasi" class="h-24">
          </div>

          <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            <p class="text-sm text-gray-500 mt-1">Silahkan masukkan alamat email anda</p>
          </div>

          <x-auth-session-status class="mb-4" :status="session('status')" />

{{--
          <form method="POST" class="space-y-5">
              @csrf

              <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input type="text" name="email" id="email" required
                      class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder" placeholder="Masukkan email...">
              </div>

              <button type="submit"
                  class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                  Submit
              </button>
          </form> --}}

          <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')"/>
                <x-text-input id="email" type="email" name="email" :value="old('email',)" :value="old('email', $request->email)" required autofocus class="mt-1 block w-full" placeholder="Masukkan email..." />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" type="password" name="password" required autofocus class="mt-1 block w-full" placeholder="Password Baru"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autofocus class="mt-1 block w-full" placeholder="Konfirmasi Password"/>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div>
                <x-primary-button class="w-full justify-center">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>


          <div class="text-center text-sm text-gray-500">
              Sudah memiliki akun?
              <a href="{{route('login')}}" class="text-blue-600 hover:underline">Login</a>
          </div>
      </div>
  </div>
</x-layoutAuth>
