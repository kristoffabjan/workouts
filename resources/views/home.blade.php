@php
    $appName = \App\Helpers\SettingsHelper::getApplicationName();
    $pageTitle = $appName . ' - ' . __('pages.home.hero.title');
    $pageDescription = __('pages.home.hero.subtitle');
    $canonicalUrl = route('home');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:site_name" content="{{ $appName }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: "Space Grotesk", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
    </style>
</head>

<body class="bg-[#0f1929] min-h-screen antialiased">
    @php
        $appLogo = \App\Helpers\SettingsHelper::getApplicationLogo();
    @endphp
    <div class="min-h-screen flex flex-col relative overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-br from-[#1a2744]/50 via-transparent to-[#243b53]/30 pointer-events-none">
        </div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#5A9CB5]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#E5A823]/5 rounded-full blur-3xl pointer-events-none">
        </div>

        <header class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    @if ($appLogo)
                        <img src="{{ Storage::url($appLogo) }}" alt="{{ $appName }}" class="h-10 w-auto">
                    @else
                        <div
                            class="flex aspect-square size-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#5A9CB5] to-[#4A8CA5] text-white shadow-lg shadow-[#5A9CB5]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-5">
                                <path fill-rule="evenodd"
                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-lg font-semibold text-white">{{ $appName }}</span>
                </div>
                <nav class="flex items-center gap-6">
                    <a href="{{ route('features') }}"
                        class="text-sm text-[#9fb3c8] hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('filament.app.auth.login') }}"
                        class="text-sm font-medium px-4 py-2 rounded-lg bg-[#243b53] text-white hover:bg-[#334e68] transition-all">{{ __('pages.common.login') }}</a>
                </nav>
            </div>
        </header>

        <main class="relative z-10 flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 tracking-tight">
                    {{ __('pages.home.hero.title') }}
                </h1>
                <p class="text-lg sm:text-xl text-[#829ab1] mb-12 max-w-2xl mx-auto leading-relaxed">
                    {{ __('pages.home.hero.subtitle') }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                    <a href="{{ route('filament.app.auth.login') }}"
                        class="group inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-[#5A9CB5] to-[#4A8CA5] text-white font-semibold shadow-lg shadow-[#5A9CB5]/25 hover:shadow-[#5A9CB5]/40 hover:scale-[1.02] transition-all duration-200">
                        <span>{{ __('pages.home.login.title') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-5 h-5 group-hover:translate-x-0.5 transition-transform">
                            <path fill-rule="evenodd"
                                d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="{{ route('request-access') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-[#334e68] text-[#d9e2ec] font-medium hover:bg-[#243b53] hover:border-[#486581] transition-all duration-200">
                        {{ __('pages.home.request_access') }}
                    </a>
                </div>

                <div class="grid sm:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div
                        class="group p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53] hover:border-[#334e68] transition-all duration-200">
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#5A9CB5]/10 border border-[#5A9CB5]/20 mb-4 mx-auto group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-6 h-6 text-[#5A9CB5]">
                                <path
                                    d="M15.98 1.804a1 1 0 0 0-1.96 0l-.24 1.192a1 1 0 0 1-.784.785l-1.192.238a1 1 0 0 0 0 1.962l1.192.238a1 1 0 0 1 .785.785l.238 1.192a1 1 0 0 0 1.962 0l.238-1.192a1 1 0 0 1 .785-.785l1.192-.238a1 1 0 0 0 0-1.962l-1.192-.238a1 1 0 0 1-.785-.785l-.238-1.192ZM6.949 5.684a1 1 0 0 0-1.898 0l-.683 2.051a1 1 0 0 1-.633.633l-2.051.683a1 1 0 0 0 0 1.898l2.051.684a1 1 0 0 1 .633.632l.683 2.051a1 1 0 0 0 1.898 0l.683-2.051a1 1 0 0 1 .633-.633l2.051-.683a1 1 0 0 0 0-1.898l-2.051-.683a1 1 0 0 1-.633-.633L6.95 5.684ZM13.949 13.684a1 1 0 0 0-1.898 0l-.184.551a1 1 0 0 1-.632.633l-.551.183a1 1 0 0 0 0 1.898l.551.183a1 1 0 0 1 .633.633l.183.551a1 1 0 0 0 1.898 0l.184-.551a1 1 0 0 1 .632-.633l.551-.183a1 1 0 0 0 0-1.898l-.551-.184a1 1 0 0 1-.633-.632l-.183-.551Z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white mb-2">
                            {{ __('pages.home.features.exercise_library.title') }}</h4>
                        <p class="text-sm text-[#829ab1] leading-relaxed">
                            {{ __('pages.home.features.exercise_library.description') }}</p>
                    </div>
                    <div
                        class="group p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53] hover:border-[#334e68] transition-all duration-200">
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#E5A823]/10 border border-[#E5A823]/20 mb-4 mx-auto group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-6 h-6 text-[#E5A823]">
                                <path fill-rule="evenodd"
                                    d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white mb-2">{{ __('pages.home.features.calendar_view.title') }}
                        </h4>
                        <p class="text-sm text-[#829ab1] leading-relaxed">
                            {{ __('pages.home.features.calendar_view.description') }}</p>
                    </div>
                    <div
                        class="group p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53] hover:border-[#334e68] transition-all duration-200">
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#5A9CB5]/10 border border-[#5A9CB5]/20 mb-4 mx-auto group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-6 h-6 text-[#5A9CB5]">
                                <path
                                    d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-white mb-2">{{ __('pages.home.features.team_management.title') }}
                        </h4>
                        <p class="text-sm text-[#829ab1] leading-relaxed">
                            {{ __('pages.home.features.team_management.description') }}</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="relative z-10 py-6 px-4 sm:px-6 lg:px-8 border-t border-[#243b53]">
            <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-[#627d98]">
                    &copy; {{ date('Y') }} {{ $appName }}. {{ __('pages.home.copyright') }}
                </div>
                <nav class="flex items-center gap-6 text-sm text-[#627d98]">
                    <a href="{{ route('features') }}"
                        class="hover:text-white transition-colors">{{ __('pages.common.features') }}</a>
                    <a href="{{ route('terms') }}"
                        class="hover:text-white transition-colors">{{ __('pages.common.terms') }}</a>
                    <a href="{{ route('privacy') }}"
                        class="hover:text-white transition-colors">{{ __('pages.common.privacy') }}</a>
                </nav>
            </div>
        </footer>
    </div>
</body>

</html>
