<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Helpers\SettingsHelper::getApplicationName() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 dark:bg-zinc-900 min-h-screen antialiased">
    @php
        $appName = \App\Helpers\SettingsHelper::getApplicationName();
        $appLogo = \App\Helpers\SettingsHelper::getApplicationLogo();
    @endphp
    <div class="min-h-screen flex flex-col">
        <header class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    @if($appLogo)
                        <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-10 w-auto">
                    @else
                        <div class="flex aspect-square size-10 items-center justify-center rounded-lg bg-amber-500 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $appName }}</span>
                </div>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('features') }}" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Features</a>
                    <a href="{{ route('filament.app.auth.login') }}" class="text-sm font-medium text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">Login</a>
                </nav>
            </div>
        </header>

        <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white mb-4">
                    Training Management System
                </h1>
                <p class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-400 mb-12 max-w-2xl mx-auto">
                    Create, schedule, and manage workout programs. Track training completion and gather feedback from your athletes.
                </p>

                <div class="grid sm:grid-cols-2 gap-6 mb-16 max-w-2xl mx-auto">
                    <a href="{{ route('filament.app.auth.login') }}"
                       class="group relative flex flex-col items-center p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-amber-500 dark:hover:border-amber-500 transition-colors duration-200">
                        <div class="flex items-center justify-center w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-amber-600 dark:text-amber-400">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">User Login</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">For coaches and athletes</p>
                    </a>

                    <a href="{{ route('filament.admin.auth.login') }}"
                       class="group relative flex flex-col items-center p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-500 transition-colors duration-200">
                        <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-zinc-600 dark:text-zinc-400">
                                <path fill-rule="evenodd" d="M12 6.75a5.25 5.25 0 0 1 6.775-5.025.75.75 0 0 1 .313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 0 1 1.248.313 5.25 5.25 0 0 1-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 1 1 2.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0 1 12 6.75ZM4.117 19.125a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Admin Login</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">System administration</p>
                    </a>
                </div>

                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-12">
                    <h3 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-8">Features</h3>
                    <div class="grid sm:grid-cols-3 gap-8 text-left">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-amber-500">
                                    <path d="M15.98 1.804a1 1 0 0 0-1.96 0l-.24 1.192a1 1 0 0 1-.784.785l-1.192.238a1 1 0 0 0 0 1.962l1.192.238a1 1 0 0 1 .785.785l.238 1.192a1 1 0 0 0 1.962 0l.238-1.192a1 1 0 0 1 .785-.785l1.192-.238a1 1 0 0 0 0-1.962l-1.192-.238a1 1 0 0 1-.785-.785l-.238-1.192ZM6.949 5.684a1 1 0 0 0-1.898 0l-.683 2.051a1 1 0 0 1-.633.633l-2.051.683a1 1 0 0 0 0 1.898l2.051.684a1 1 0 0 1 .633.632l.683 2.051a1 1 0 0 0 1.898 0l.683-2.051a1 1 0 0 1 .633-.633l2.051-.683a1 1 0 0 0 0-1.898l-2.051-.683a1 1 0 0 1-.633-.633L6.95 5.684ZM13.949 13.684a1 1 0 0 0-1.898 0l-.184.551a1 1 0 0 1-.632.633l-.551.183a1 1 0 0 0 0 1.898l.551.183a1 1 0 0 1 .633.633l.183.551a1 1 0 0 0 1.898 0l.184-.551a1 1 0 0 1 .632-.633l.551-.183a1 1 0 0 0 0-1.898l-.551-.184a1 1 0 0 1-.633-.632l-.183-.551Z" />
                                </svg>
                                <h4 class="font-semibold text-zinc-900 dark:text-white">Exercise Library</h4>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Build your exercise catalog with videos, descriptions, and tags for easy filtering.</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-amber-500">
                                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                                </svg>
                                <h4 class="font-semibold text-zinc-900 dark:text-white">Calendar View</h4>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Schedule trainings and view them in an intuitive calendar interface.</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-amber-500">
                                    <path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" />
                                </svg>
                                <h4 class="font-semibold text-zinc-900 dark:text-white">Team Management</h4>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage coaches and athletes with role-based access control.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-6 px-4 sm:px-6 lg:px-8 border-t border-zinc-200 dark:border-zinc-700">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                </div>
                <nav class="flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
                    <a href="{{ route('features') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Features</a>
                    <a href="{{ route('terms') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Terms</a>
                    <a href="{{ route('privacy') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Privacy</a>
                </nav>
            </div>
        </footer>
    </div>
</body>
</html>
