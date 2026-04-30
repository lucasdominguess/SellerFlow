<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ethereal Core') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background text-on-surface selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col font-sans antialiased">
    
    <!-- Top Navigation Anchor (Shared) -->
    <header class="fixed top-0 w-full flex justify-between items-center px-6 h-16 z-50 bg-transparent">
        <div class="flex items-center gap-2 cursor-pointer active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-violet-300 dark:text-violet-200" style="font-variation-settings: 'FILL' 0;">blur_on</span>
            <span class="text-xl font-bold tracking-tighter text-violet-100 font-label tracking-tight">Ethereal Core</span>
        </div>
        <div class="flex items-center gap-4">
            <button id="theme-toggle" type="button" class="material-symbols-outlined text-violet-400 hover:opacity-80 transition-opacity cursor-pointer active:scale-95 transition-transform" style="font-variation-settings: 'FILL' 1;">
                dark_mode
            </button>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-6 ethereal-bg relative">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-on-primary-fixed-variant/5 rounded-full blur-[150px]"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            {{ $slot }}
        </div>
    </main>

    <!-- Bottom Navigation Shell -->
    <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-8 pb-6 pt-4 bg-violet-950/20 backdrop-blur-xl rounded-t-[2rem] shadow-[0_-4px_20px_rgba(85,22,190,0.1)]">
        <a class="flex items-center justify-center bg-violet-500/20 text-violet-100 rounded-full p-3 active:scale-90 transition-all duration-300" href="#">
            <span class="material-symbols-outlined" data-icon="login">login</span>
        </a>
        <a class="flex items-center justify-center text-violet-400/60 p-3 hover:text-violet-200 transition-colors active:scale-90 transition-all duration-300" href="#">
            <span class="material-symbols-outlined" data-icon="fingerprint">fingerprint</span>
        </a>
        <a class="flex items-center justify-center text-violet-400/60 p-3 hover:text-violet-200 transition-colors active:scale-90 transition-all duration-300" href="#">
            <span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
        </a>
    </nav>
    <div class="fixed bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/20 to-transparent"></div>

    @stack('scripts')
</body>
</html>
