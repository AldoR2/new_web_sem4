<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{asset('images/stipress.png')}}">
    <title>{{ $title ?? config('app.name', 'Laravel') }} | STIPRES</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Buttons + File export -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- FileSaver.js and JSZip for Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>[x-cloak] { display: none !important; }</style>

</head>
<body class="bg-gray-100 dark:bg-darkCard">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div x-data="{isSideMenuOpen: false}" @resize.window="if (window.innerWidth >= 1280) isSideMenuOpen = false" class="flex h-screen">
        <x-sidebar></x-sidebar>

        <div class="xl:ml-64 ease-in-out duration-200 flex flex-col flex-1">
            <x-navbar></x-navbar>
            <div x-bind:class="isSideMenuOpen ? 'opacity-70 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                class="fixed inset-0 z-20 bg-gray-600 transition-opacity duration-300"></div>

            <main class="relative mt-16 xl:mt-0 p-6 bg-gray-100 dark:bg-darkCard">
                <div>
                    <h1 class="font-bold text-gray-800 dark:text-white text-2xl">{{ $title }}</h1>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
<script>
document.addEventListener('submit', function (e) {
    if (e.target.classList.contains('form-hapus')) {
        e.preventDefault();

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }

    if (e.target.classList.contains('form-validasi')) {
        e.preventDefault();

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pastikan data sudah sesuai!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, simpan!',
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    }
});

        @if (session('status') && session('message'))
            window.addEventListener('pageshow', function (event) {
                if (!event.persisted) {
                    Swal.fire({
                        icon: '{{ session('status') }}',
                        title: '{{ ucfirst(session('status')) }}',
                        text: '{{ session('message') }}',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    if (window.history.replaceState) {
                        window.history.replaceState(null, null, window.location.href);
                    }
                }
            });
        @endif

    </script>
</body>
</html>
