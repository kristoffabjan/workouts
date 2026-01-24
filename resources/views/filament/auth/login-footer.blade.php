<div class="mt-4 flex flex-col gap-3 text-center">
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
        <x-filament::icon icon="heroicon-m-arrow-left" class="h-4 w-4" />
        {{ __('Back') }}
    </a>
    <span class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('auth.request_access.dont_have_account') }}
        <a href="{{ route('request-access') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
            {{ __('auth.request_access.request_access_link') }}
        </a>
    </span>
</div>
