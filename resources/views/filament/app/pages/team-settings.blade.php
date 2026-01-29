<x-filament-panels::page>
    @php
        $team = $this->getTeam();
        $role = auth()->user()->getRoleInTeam($team);
        $isOwner = $this->isOwner();
        $memberCount = $team->users()->count();
        $coachCount = $team->coaches()->count();
        $clientCount = $team->clients()->count();
        $isPersonal = $team->is_personal;
    @endphp

    @if($isPersonal)
        {{-- Personal Team Info Banner --}}
        <x-filament::section icon="heroicon-o-user" icon-color="primary">
            <x-slot name="heading">
                {{ __('settings.team.personal.heading') }}
            </x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('settings.team.personal.description') }}
                </p>
            </div>
        </x-filament::section>
    @else
        {{-- Team Settings Form --}}
        <form wire:submit="save">
            {{ $this->form }}

            @if($this->canEditSettings())
                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-check">
                        {{ __('app.actions.save') }}
                    </x-filament::button>
                </div>
            @endif
        </form>
    @endif

    <div class="grid gap-6 md:grid-cols-2 mt-6">
        {{-- Team Overview --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-user-group" class="h-5 w-5 text-primary-500" />
                    {{ __('settings.team.overview.heading') }}
                </div>
            </x-slot>

            <x-slot name="description">
                {{ __('settings.team.overview.description') }}
            </x-slot>

            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.overview.team_name') }}</dt>
                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $team->name }}</dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.overview.team_type') }}</dt>
                    <dd>
                        @if($team->is_personal)
                            <x-filament::badge color="info" icon="heroicon-m-user">
                                {{ __('settings.team.overview.type_personal') }}
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="success" icon="heroicon-m-building-office">
                                {{ __('settings.team.overview.type_organization') }}
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.overview.team_owner') }}</dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::avatar
                            :src="null"
                            size="sm"
                        />
                        {{ $team->owner?->name ?? __('settings.team.overview.no_owner') }}
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.overview.created') }}</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $team->created_at->format('M j, Y') }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>

        {{-- Your Membership --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-identification" class="h-5 w-5 text-primary-500" />
                    {{ __('settings.team.membership.heading') }}
                </div>
            </x-slot>

            <x-slot name="description">
                {{ __('settings.team.membership.description') }}
            </x-slot>

            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.membership.your_role') }}</dt>
                    <dd class="flex items-center gap-2">
                        @if($role)
                            <x-filament::badge :color="$role->getColor()" :icon="$role->getIcon()">
                                {{ $role->getLabel() }}
                            </x-filament::badge>
                        @endif
                        @if($isOwner)
                            <x-filament::badge color="warning" icon="heroicon-m-star">
                                {{ __('settings.team.membership.owner') }}
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.membership.member_since') }}</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ auth()->user()->teams()->where('team_id', $team->id)->first()?->pivot?->created_at?->format('M j, Y') ?? __('settings.team.membership.unknown') }}
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('settings.team.membership.permissions') }}</dt>
                    <dd class="text-sm text-gray-600 dark:text-gray-400">
                        @if($role?->value === 'coach')
                            {{ __('settings.team.membership.permissions_coach') }}
                        @else
                            {{ __('settings.team.membership.permissions_client') }}
                        @endif
                    </dd>
                </div>
            </dl>
        </x-filament::section>
    </div>

    {{-- Team Statistics --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-primary-500" />
                {{ __('settings.team.statistics.heading') }}
            </div>
        </x-slot>

        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-50 dark:bg-[#E5A823]/10 border border-transparent dark:border-[#E5A823]/20 p-4 text-center">
                <div class="text-3xl font-bold text-[#E5A823]">{{ $memberCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.team.statistics.total_members') }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-[#FAAC68]/10 border border-transparent dark:border-[#FAAC68]/20 p-4 text-center">
                <div class="text-3xl font-bold text-[#FAAC68]">{{ $coachCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.team.statistics.coaches') }}</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-[#5AB58A]/10 border border-transparent dark:border-[#5AB58A]/20 p-4 text-center">
                <div class="text-3xl font-bold text-[#5AB58A]">{{ $clientCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.team.statistics.clients') }}</div>
            </div>
        </div>
    </x-filament::section>

    {{-- Personal Team Info or Danger Zone --}}
    @if($team->is_personal)
        <x-filament::section icon="heroicon-o-information-circle" icon-color="info">
            <x-slot name="heading">
                {{ __('settings.team.about_personal.heading') }}
            </x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('settings.team.about_personal.description') }}
                </p>
                <ul class="text-gray-600 dark:text-gray-400 mt-2 space-y-1">
                    <li>{{ __('settings.team.about_personal.item_exercises') }}</li>
                    <li>{{ __('settings.team.about_personal.item_trainings') }}</li>
                    <li>{{ __('settings.team.about_personal.item_progress') }}</li>
                </ul>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-4">
                    {{ __('settings.team.about_personal.note') }}
                </p>
            </div>
        </x-filament::section>
    @else
        <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
            <x-slot name="heading">
                {{ __('settings.team.danger_zone.heading') }}
            </x-slot>

            <x-slot name="description">
                {{ __('settings.team.danger_zone.description') }}
            </x-slot>

            <div class="space-y-4">
                @if($this->canLeaveTeam())
                    <div class="rounded-lg border border-danger-300 dark:border-danger-700 bg-danger-50 dark:bg-danger-950/50 p-4">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-arrow-right-on-rectangle" class="h-5 w-5 text-danger-500" />
                            <div>
                                <h4 class="text-sm font-medium text-danger-800 dark:text-danger-200">{{ __('settings.team.danger_zone.leave_team') }}</h4>
                                <p class="text-sm text-danger-600 dark:text-danger-400 mt-1">
                                    {!! __('settings.team.danger_zone.leave_team_description') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($isOwner)
                    <div class="rounded-lg border border-warning-300 dark:border-warning-700 bg-warning-50 dark:bg-warning-950/50 p-4">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 text-warning-500" />
                            <div>
                                <h4 class="text-sm font-medium text-warning-800 dark:text-warning-200">{{ __('settings.team.danger_zone.transfer_first') }}</h4>
                                <p class="text-sm text-warning-600 dark:text-warning-400 mt-1">
                                    {!! __('settings.team.danger_zone.transfer_first_description') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
