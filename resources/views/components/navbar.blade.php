<nav class="fixed z-50 xl:sticky top-0 w-full border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 py-3 px-4 ">
    <div class="flex justify-between items-center" x-data="{ open: false, isDark: localStorage.getItem('theme') === 'dark' }"
         x-init="document.documentElement.classList.toggle('dark', isDark)">
        <div class="text-gray-600">
            <button @click="isSideMenuOpen = !isSideMenuOpen" class="cursor-pointer block xl:hidden px-2 py-1 active:bg-gray-200 rounded-sm"><i class="bi bi-list font-bold text-2xl"></i></button>
                {{-- <h1 class="text-lg px-2 font-semibold text-gray-600 hidden xl:block">{{ $title ?? config('app.name', 'Laravel') }}</h1> --}}
        </div>

      @php
          $user = Auth::user();
          $user->load($user->role); // 'admin', 'dosen', atau 'mahasiswa'
          $profile = $user->{$user->role}; // Ambil model relasinya
      @endphp

        <div class="relative flex items-center gap-2" x-data="{ open: false }">
            <div class="relative flex items-center gap-4">
                <button @click="isDark = !isDark;
                                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                                document.documentElement.classList.toggle('dark', isDark)"
                        class="text-gray-600 dark:text-white text-lg hover:text-black">
                        <template x-if="!isDark">
                            <i class="bi bi-sun"></i>
                        </template>
                        <template x-if="isDark">
                            <i class="bi bi-moon"></i>
                        </template>
                </button>
                <a href="{{ route('kalender-akademik.view') }}" class="relative group">
                    <i class="bi bi-calendar3 text-gray-600 dark:text-white text-lg mb-1"></i>
                    <span class="absolute left-1/2 top-full mt-1 -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
                        Kalender Akademik
                    </span>
                </a>
                <div class="w-px h-10 bg-gray-400 dark:bg-gray-500"></div>
            </div>

            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                <img src="{{ $profile?->foto ? asset('storage/' . $profile->foto) : asset('images/profil-kosong.png') }}"
                    class="w-10 h-10 rounded-full object-cover border-2 border-white hover:border-gray-300" alt="User">
                <span class="text-gray-600 hidden md:inline dark:text-white">{{ Auth::user()->name }}</span>
                <svg class="w-4 h-4 text-gray-600 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"/>
                </svg>
            </button>

            @if (Auth::user()->role === 'superadmin')
                <div x-show="open" x-cloak x-transition.top.duration.300ms
                    class="absolute top-14 right-0 w-56 p-2 rounded-md bg-white shadow-xl text-gray-800 dark:bg-gray-800 dark:text-white font-semibold z-50">
                    {{-- <a href="{{ route(Auth::user()->role . '.profile.edit') }}"
                        class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-person-circle text-lg"></i>
                        <span>Profile</span>
                    </a> --}}
                    <a href="{{ route(Auth::user()->role . '.change-password') }}"
                        class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-gear-fill text-lg"></i>
                        <span>Ubah Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full text-left">
                            <i class="bi bi-box-arrow-left text-lg"></i>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            @endif

            @if (Auth::user()->role === 'admin')
                <div x-show="open" x-cloak x-transition.top.duration.300ms
                    class="absolute top-14 right-0 w-56 p-2 rounded-md bg-white shadow-xl text-gray-800 dark:bg-gray-800 dark:text-white font-semibold z-50">
                    <a href="{{ route(Auth::user()->role . '.profile.edit') }}"
                        class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-person-circle text-lg"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route(Auth::user()->role . '.change-password') }}"
                        class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-gear-fill text-lg"></i>
                        <span>Ubah Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full text-left">
                            <i class="bi bi-box-arrow-left text-lg"></i>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            @endif

            @if (Auth::user()->role === 'dosen')
                <div x-show="open" x-cloak x-transition.top.duration.300ms
                    class="absolute top-14 right-0 w-56 p-2 rounded-md bg-white shadow-xl text-gray-800 dark:bg-gray-800 dark:text-white font-semibold z-50">
                    <a href="{{ route(Auth::user()->role . '.profile.edit') }}" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-person-circle text-lg"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route(Auth::user()->role . '.change-password') }}" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-gear-fill text-lg"></i>
                        <span>Ubah Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                    @csrf
                        <button type="submit" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full text-left">
                            <i class="bi bi-box-arrow-left text-lg"></i>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            @endif

            @if (Auth::user()->role === 'mahasiswa')
                <div x-show="open" x-cloak x-transition.top.duration.300ms
                    class="absolute top-14 right-0 w-56 p-2 rounded-md bg-white shadow-xl text-gray-800 dark:bg-gray-800 dark:text-white font-semibold z-50">
                    <a href="{{ route(Auth::user()->role . '.change-password') }}" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full">
                        <i class="bi bi-gear-fill text-lg"></i>
                        <span>Ubah Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                    @csrf
                        <button type="submit" class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full text-left">
                            <i class="bi bi-box-arrow-left text-lg"></i>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</nav>
