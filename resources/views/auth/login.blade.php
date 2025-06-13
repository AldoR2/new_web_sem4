<x-layoutAuth title="Login">
  <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-100 to-gray-100 px-4">
      <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
          <div class="w-full flex justify-center">
            <img src="{{ asset('images/stipress.png') }}" alt="Logo Aplikasi" class="h-24">
          </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

          <form method="POST" class="space-y-5" action="{{ route('login') }}">
              @csrf

              <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" value="{{old('username', request()->cookie('cookie_username'))}}" required autofocus autocomplete="on"
                        class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan username...">
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
              </div>

              <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan password...">

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
              </div>

              <div class="flex items-center justify-between text-sm">
                  <label class="flex items-center">
                      <input type="checkbox" class="mr-2" name="remember_me" id="remember_me" {{ request()->cookie('cookie_ingat') ? 'checked' : '' }}>
                      Remember me
                  </label>
                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">Lupa password?</a>
                  @endif

              </div>

              <button type="submit"
                  class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                  Login
              </button>
          </form>

          <div class="text-center text-sm text-gray-500">
              Tahap registrasi harus
              @if (Route::has('verify'))
                <a href="{{route('verify')}}" class="text-blue-600 hover:underline">Validasi Akun!</a>
              @endif
          </div>
      </div>
  </div>
</x-layoutAuth>
