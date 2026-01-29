@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'Workouts') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 dark:bg-zinc-900 min-h-screen antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="py-6 px-4 sm:px-6 lg:px-8 border-b border-zinc-200 dark:border-zinc-700">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="flex aspect-square size-10 items-center justify-center rounded-lg bg-[#5A9CB5] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-zinc-900 dark:text-white">{{ config('app.name', 'Workouts') }}</span>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('features') }}" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('filament.app.auth.login') }}" class="text-sm font-medium text-[#5A9CB5] hover:text-[#4A8CA5] transition-colors">{{ __('pages.common.login') }}</a>
                </nav>
            </div>
        </header>

        <main class="flex-grow px-4 sm:px-6 lg:px-8 py-12">
            {{ $slot }}
        </main>

        <footer class="py-6 px-4 sm:px-6 lg:px-8 border-t border-zinc-200 dark:border-zinc-700">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Workouts') }}. {{ __('pages.home.copyright') }}
                </div>
                <nav class="flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
                    <a href="{{ route('features') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('terms') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">{{ __('pages.common.terms') }}</a>
                    <a href="{{ route('privacy') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">{{ __('pages.common.privacy') }}</a>
                </nav>
            </div>
        </footer>
    </div>
</body>
</html>
