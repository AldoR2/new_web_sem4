<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth scroll-pt-40">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>STIPRES</title>
    <link rel="icon" type="image/png" href="{{asset('images/stipress.png')}}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">


    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>
<body>
    <header class="bg-white dark:bg-gray-900 shadow-sm sticky top-0 z-50" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-16">

            <a href="#" class="flex items-center space-x-2">
              <img src="{{ asset('images/stipress.png') }}" class="h-8 w-auto" alt="Logo">
              <span class="text-xl font-bold text-gray-800 dark:text-white">STIPRES</span>
            </a>

            <div class="hidden lg:flex space-x-8 items-center">
              <a href="#home" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white transition">Home</a>
              <a href="#tentang" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white transition">Tentang</a>
              <a href="#fitur" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white transition">Fitur</a>
              <a href="#kontak" class="text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white transition">Kontak</a>
            </div>

            <div class="hidden lg:flex items-center space-x-2">
              <a href="{{route('login')}}" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow transition">Login</a>
            </div>

            <div class="lg:hidden">
              <button id="menu-toggle" class="text-gray-600 dark:text-gray-300 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-white dark:bg-gray-900 border-t dark:border-gray-700 px-4 pt-4 pb-6 space-y-3">
          <a href="#" class="block text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">Home</a>
          <a href="#" class="block text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">Tentang</a>
          <a href="#" class="block text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">Fitur</a>
          <a href="#" class="block text-gray-600 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">Kontak</a>
          <a href="{{route('login')}}" class="block w-fit px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow transition">Login</a>
        </div>
      </header>

      <section id="home" class="bg-gray-50 dark:bg-gray-900 p-20" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:flex lg:items-center lg:gap-16">
          <div class="lg:w-1/2">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-5xl leading-tight"
            data-aos="fade-right">
              STIPRES - Aplikasi Presensi <br class="hidden sm:inline" />
              Stikes Panti Waluya
            </h1>
            <p class="mt-6 text-gray-700 dark:text-gray-300 max-w-xl text-lg leading-relaxed" data-aos="fade-right"
            data-aos-delay="200">
              Solusi presensi digital lengkap untuk kampus dan perusahaan Anda. Cepat, akurat, dan dapat diakses dari mana saja.
            </p>

            <div class="mt-10" data-aos="fade-up" data-aos-delay="400">
              <a href="{{route('login')}}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Get Started
              </a>
            </div>
          </div>

          <div class="mt-12 lg:mt-0 lg:w-1/2 flex justify-center">
            <img
              src="{{ asset('images/mockup.png') }}"
              alt="Stipres App Mockup"
              class="w-full max-w-md rounded-xl float-animation"
              data-aos="zoom-in-left"
              data-aos-delay="500"
            />
          </div>
        </div>
      </section>

      <section id="tentang" class="bg-white dark:bg-gray-800 pb-16 pt-20" data-aos="fade-up">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-8">
            Tentang STIPRES
          </h2>
          <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed mb-8">
            Stipres adalah aplikasi presensi modern yang dirancang untuk memudahkan proses absensi di lingkungan kampus, khususnya digunakan oleh mahasiswa di Setikes Panti Waluya Malang. Sistem presensi offline menggunakan teknologi RFID memungkinkan mahasiswa melakukan absensi dengan cepat dan akurat tanpa perlu koneksi internet. Selain itu, aplikasi mobile Stipres mendukung presensi via Zoom, sehingga presensi daring dapat dilakukan dengan mudah selama perkuliahan atau rapat online berlangsung.
          </p>
          <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed mb-12">
            Selain fitur presensi, Stipres juga menyediakan manajemen jadwal dan kalender akademik yang membantu mahasiswa dan dosen mengatur kegiatan perkuliahan, ujian, dan event kampus secara terorganisir dan praktis.
          </p>

          <div class="mx-auto max-w-3xl">
            <img
            src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80"
            alt="Aplikasi Stipres - Presensi Offline & Mobile via Zoom"
            class="w-full rounded-lg shadow-lg"
            />

            <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm italic">
              Tampilan aplikasi Stipres: Website untuk presensi offline dengan RFID dan aplikasi mobile untuk presensi via Zoom.
            </p>
          </div>
        </div>
      </section>

      <section id="fitur" class="bg-gray-50 dark:bg-gray-900 py-16 px-6" data-aos="fade-up">
        <div class="max-w-7xl mx-auto text-center mb-12">
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Fitur Unggulan STIPRES</h2>
          <p class="mt-4 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Stipress memberikan kemudahan dalam presensi dengan teknologi modern yang lengkap dan mudah digunakan.
          </p>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8m-8 4h6M5 20h14a2 2 0 002-2v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Presensi Offline dengan RFID</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Menggunakan teknologi RFID untuk absensi tanpa koneksi internet, cepat dan akurat.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Fitur Jadwal Presensi</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Mudah mengatur jadwal presensi sesuai kebutuhan kampus atau instansi.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Kalender Akademik</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Menampilkan kalender akademik yang terintegrasi untuk memudahkan perencanaan dan pengingat penting.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4M7 17v-2a4 4 0 014-4h0M9 17v4H7v-4h2z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Rekap Absensi Lengkap</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Laporan absensi lengkap yang mudah diakses kapan saja untuk analisis dan evaluasi.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M10 15l-4.553 2.276A1 1 0 015 17.382V10.618a1 1 0 011.447-.894L10 13" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Presensi Via Zoom di Mobile</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Fitur presensi online langsung melalui aplikasi mobile via Zoom, fleksibel dan praktis.</p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md border-b-4 border-blue-500 hover:border-blue-700 transition-all duration-300 cursor-pointer">
            <div class="mb-4 text-blue-600 group-hover:text-blue-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M12 3v18" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white group-hover:text-blue-700 transition-colors duration-300">Tampilan Responsive</h3>
            <p class="text-gray-600 dark:text-gray-300 group-hover:text-blue-600 transition-colors duration-300">Desain tampilan yang responsif di semua perangkat, dari desktop hingga smartphone.</p>
          </div>
        </div>
      </section>

      <section id="kontak" class="bg-gray-50 dark:bg-gray-900 py-16" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
          <div class="md:w-1/2 rounded-lg overflow-hidden shadow-2xl max-h-[300px] md:max-h-[400px] flex justify-center items-center bg-gray-100">
            <img
              src="{{ asset('images/gedung.jpg') }}"
              alt="Gedung Stikes Panti Waluya Malang"
              class="w-auto max-h-full object-contain transition-transform duration-500 hover:scale-105"
            />
          </div>

          <div class="md:w-1/2 space-y-8 text-center md:text-left">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
              Kontak Kami
            </h2>
            <p class="text-lg text-gray-700 dark:text-gray-300 font-semibold">
              Sekolah Tinggi Ilmu Kesehatan <br />
              <span class="text-blue-600 dark:text-blue-400">Panti Waluya Malang</span>
            </p>

            <div class="space-y-4">
              <div class="flex items-center space-x-4 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow cursor-default">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v9m0 0l3-3m-3 3l-3-3" />
                </svg>
                <p class="text-gray-700 dark:text-gray-300 text-base text-left">
                  <strong class="font-semibold">Alamat:</strong><br />
                  Jl. Trunojoyo No.52, Tlogomas, Lowokwaru, Malang, Jawa Timur 65141
                </p>
              </div>

              <div class="flex items-center space-x-4 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l7 7m0 0l7-7m-7 7V3" />
                </svg>
                <p class="text-gray-700 dark:text-gray-300 text-base text-left">
                  <strong class="font-semibold">Telepon:</strong><br />
                  (0341) 123456
                </p>
              </div>

              <div class="flex items-center space-x-4 bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m8 0l-4-4m4 4l-4 4" />
                </svg>
                <p class="text-gray-700 dark:text-gray-300 text-base text-left">
                  <strong class="font-semibold">Email:</strong><br />
                  info@stipres.ac.id
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <footer class="bg-gray-800 text-gray-300 py-10">
        <div class="max-w-7xl mx-auto px-6 md:px-12 flex flex-col md:flex-row justify-between gap-8">
          <div class="md:w-1/3">
            <h3 class="text-white text-2xl font-bold mb-4">Stipres</h3>
            <p class="text-gray-400">
              Aplikasi presensi modern untuk kemudahan absensi offline dengan RFID dan presensi via Zoom di aplikasi mobile.
            </p>
          </div>
          <div class="md:w-1/3">
            <h4 class="text-white font-semibold mb-4">Menu</h4>
            <ul>
              <li><a href="#" class="hover:text-white transition-colors duration-200">Home</a></li>
              <li><a href="#" class="hover:text-white transition-colors duration-200">Tentang</a></li>
              <li><a href="#" class="hover:text-white transition-colors duration-200">Fitur</a></li>
              <li><a href="#" class="hover:text-white transition-colors duration-200">Kontak</a></li>
            </ul>
          </div>
          <div class="md:w-1/3">
            <h4 class="text-white font-semibold mb-4">Kontak Kami</h4>
            <p>Stikes Panti Waluya Malang</p>
            <p>Jl. Trunojoyo No.52, Tlogomas, Lowokwaru, Malang</p>
            <p>Telepon: (0341) 123456</p>
            <p>Email: info@stipres.ac.id</p>
          </div>
        </div>
        <div class="mt-10 border-t border-gray-700 pt-6 text-center text-gray-500 text-sm">
          &copy; 2025 Stipres. All rights reserved.
        </div>
      </footer>




<script>
const button = document.getElementById('menu-toggle');
const menu = document.getElementById('mobile-menu');

button.addEventListener('click', () => {
    menu.classList.toggle('hidden');
});
</script>


<script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
    duration: 1700,
    offset: 120,
    once: true,
    mirror: false,
  });
</script>

<script
    async
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"
  ></script>
</body>
</html>
