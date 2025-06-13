<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{asset('images/stipress.png')}}">
  <title>{{ $title ?? 'Login' }} | STIPRES</title>
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="antialiased bg-gray-100 font-sans">

    {{-- <body class="antialiased bg-gray-100 font-sans"> --}}
        <main data-aos="flip-right">
            {{ $slot }}
        </main>
    {{-- </body> --}}

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
        duration: 500,
        offset: 120,
        once: true,
        mirror: false,
    });
    </script>

</body>
</html>
