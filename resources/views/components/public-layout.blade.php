@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'Workouts') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-[#0f1929] min-h-screen antialiased">
    <div class="min-h-screen flex flex-col relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#1a2744]/50 via-transparent to-[#243b53]/30 pointer-events-none"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#5A9CB5]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#E5A823]/5 rounded-full blur-3xl pointer-events-none"></div>

        <header class="relative z-10 py-6 px-4 sm:px-6 lg:px-8 border-b border-[#243b53]">
            <div class="max-w-5xl mx-auto flex justify-between items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="flex aspect-square size-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#5A9CB5] to-[#4A8CA5] text-white shadow-lg shadow-[#5A9CB5]/20">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-white">{{ config('app.name', 'Workouts') }}</span>
                </a>
                <nav class="flex items-center gap-6">
                    <a href="{{ route('features') }}" class="text-sm text-[#9fb3c8] hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('filament.app.auth.login') }}" class="text-sm font-medium px-4 py-2 rounded-lg bg-[#243b53] text-white hover:bg-[#334e68] transition-all">{{ __('pages.common.login') }}</a>
                </nav>
            </div>
        </header>

        <main class="relative z-10 flex-grow px-4 sm:px-6 lg:px-8 py-12">
            {{ $slot }}
        </main>

        <footer class="relative z-10 py-6 px-4 sm:px-6 lg:px-8 border-t border-[#243b53]">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-[#627d98]">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Workouts') }}. {{ __('pages.home.copyright') }}
                </div>
                <nav class="flex items-center gap-6 text-sm text-[#627d98]">
                    <a href="{{ route('features') }}" class="hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('terms') }}" class="hover:text-white transition-colors">{{ __('pages.common.terms') }}</a>
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">{{ __('pages.common.privacy') }}</a>
                </nav>
            </div>
        </footer>
    </div>
</body>
</html>
