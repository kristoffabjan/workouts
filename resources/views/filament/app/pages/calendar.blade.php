<x-filament-panels::page>
    @php
        $user = auth()->user();
        $team = \Filament\Facades\Filament::getTenant();
        $isCoach = $user?->isCoach($team);
    @endphp

    <div class="flex items-center gap-4 mb-4">
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-gray-400"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">Draft</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-amber-400"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">Scheduled</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
            <span class="text-sm text-gray-600 dark:text-gray-400">Skipped</span>
        </div>
    </div>

    @if($isCoach)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Viewing all team trainings. Click on a training to view details.
        </p>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Viewing your assigned trainings. Click on a training to view details.
        </p>
    @endif
</x-filament-panels::page>
