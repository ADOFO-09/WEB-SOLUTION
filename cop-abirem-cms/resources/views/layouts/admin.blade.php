<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'COP Abirem CMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('admin.partials.styles')
    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        @include('admin.partials.topnav')

        <!-- Page Header -->
        @hasSection('header')
        <div class="page-header">
            @yield('header')
        </div>
        @endif

        <!-- Content Area -->
        <main class="content-area">
            @include('admin.partials.alerts')
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
 

