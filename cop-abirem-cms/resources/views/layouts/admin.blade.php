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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('admin.partials.styles')
    @stack('styles')

    <style>
        /* Page entry animation */
        .content-area > * {
            animation: pageFadeIn 0.25s ease both;
        }
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Overlay (Mobile) -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:35;cursor:pointer;backdrop-filter:blur(1px);"></div>

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
