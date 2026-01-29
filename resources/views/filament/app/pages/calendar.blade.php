<x-filament-panels::page>
    @php
        $user = auth()->user();
        $team = \Filament\Facades\Filament::getTenant();
        $isCoach = $user?->isCoach($team);
    @endphp

    <div class="flex items-center gap-4 mb-4">
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-gray-400"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('trainings.calendar.legend.draft') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-[#FAAC68]"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('trainings.calendar.legend.scheduled') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('trainings.calendar.legend.completed') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('trainings.calendar.legend.skipped') }}</span>
        </div>
    </div>

    @if($isCoach)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ __('trainings.calendar.coach_description') }}
        </p>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ __('trainings.calendar.client_description') }}
        </p>
    @endif
</x-filament-panels::page>
