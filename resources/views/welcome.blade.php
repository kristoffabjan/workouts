<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workouts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Workouts</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">Training management system</p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('filament.app.auth.login') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition-colors duration-200">
                User Login
            </a>

            <a href="{{ route('filament.admin.auth.login') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold rounded-lg transition-colors duration-200">
                Admin Login
            </a>
        </div>
    </div>
</body>
</html>
