<x-layoutAuth title="Validasi">
  <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-100 to-gray-100 px-4">
      <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
          <div class="w-full flex justify-center">
            <img src="{{ asset('images/stipress.png') }}" alt="Logo Aplikasi" class="h-24">
          </div>

          <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Validasi Akun Mahasiswa</h1>
            <p class="text-sm text-gray-500 mt-1">Silahkan masukkan alamat email anda</p>
          </div>

          <x-auth-session-status class="mb-4" :status="session('status')" />

          <form method="POST" action="{{route('verify.email')}}" class="space-y-5">
              @csrf

              <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input type="text" name="email" id="email" required
                      class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan email..." autocomplete="email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
              </div>

              <button type="submit"
                  class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                  Submit
              </button>
          </form>

          <div class="text-center text-sm text-gray-500">
              Sudah memiliki akun?
              <a href="{{route('login')}}" class="text-blue-600 hover:underline">Login</a>
          </div>
      </div>
  </div>
</x-layoutAuth>
