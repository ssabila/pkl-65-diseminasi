<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['h-full'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'PKL 65') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/assets/LOGO-PKL_REV8.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/assets/LOGO-PKL_REV8.png') }}">
    
    <link rel="preload" as="image" href="{{ asset('images/assets/lanskap-gunung-abstrak.svg') }}">

    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="title" content="PKL 65 - Politeknik Statistika STIS">
    <meta name="description"
        content="Website Hasil PKL 65 - Kelola Data dan Interpretasi untuk Diseminasi Politeknik Statistika STIS D.I. Yogyakarta">
    <meta name="keywords" content="PKL 65, Politeknik Statistika STIS, Yogyakarta, Data Diseminasi, Statistik">
    <meta name="author" content="PKL 65 Team">
    <meta name="theme-color" content="#EF874B">
    <meta name="color-scheme" content="light dark">

    <meta name="tailwind-theme" content="modern">
    <meta name="tailwind-version" content="3.x">
    <meta name="tailwind-components" content="admin-dashboard">
    <meta name="tailwind-plugins" content="forms,typography,aspect-ratio,line-clamp">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="PKL 65 - Politeknik Statistika STIS">
    <meta property="og:description"
        content="Website Hasil PKL 65 - Kelola Data dan Interpretasi untuk Diseminasi Politeknik Statistika STIS D.I. Yogyakarta">
    <meta property="og:image" content="{{ asset('images/assets/LOGO-PKL_REV8.png') }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="PKL 65 - Politeknik Statistika STIS">
    <meta property="twitter:description"
        content="Website Hasil PKL 65 - Kelola Data dan Interpretasi untuk Diseminasi Politeknik Statistika STIS D.I. Yogyakarta">
    <meta property="twitter:image" content="{{ asset('images/assets/LOGO-PKL_REV8.png') }}">


    @vite(['resources/js/app.js', 'resources/js/darkMode.js', 'resources/css/app.css'])
    @inertiaHead
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>


<body @class(['antialiased', 'h-full' , 'bg-gray-50' , 'dark:bg-gray-900' , 'text-gray-900' , 'dark:text-gray-100' ])>
    @routes
    @inertia
</body>

</html>