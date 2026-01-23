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
                    Team Overview
                </div>
            </x-slot>

            <x-slot name="description">
                Basic information about this team
            </x-slot>

            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Name</dt>
                    <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $team->name }}</dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Type</dt>
                    <dd>
                        @if($team->is_personal)
                            <x-filament::badge color="info" icon="heroicon-m-user">
                                Personal
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="success" icon="heroicon-m-building-office">
                                Organization
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team Owner</dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::avatar
                            :src="null"
                            size="sm"
                        />
                        {{ $team->owner?->name ?? 'No owner' }}
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
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
                    Your Membership
                </div>
            </x-slot>

            <x-slot name="description">
                Your role and permissions in this team
            </x-slot>

            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Role</dt>
                    <dd class="flex items-center gap-2">
                        @if($role)
                            <x-filament::badge :color="$role->getColor()" :icon="$role->getIcon()">
                                {{ $role->getLabel() }}
                            </x-filament::badge>
                        @endif
                        @if($isOwner)
                            <x-filament::badge color="warning" icon="heroicon-m-star">
                                Owner
                            </x-filament::badge>
                        @endif
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ auth()->user()->teams()->where('team_id', $team->id)->first()?->pivot?->created_at?->format('M j, Y') ?? 'Unknown' }}
                    </dd>
                </div>

                <div class="py-3 flex justify-between items-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Permissions</dt>
                    <dd class="text-sm text-gray-600 dark:text-gray-400">
                        @if($role?->value === 'coach')
                            Manage exercises, trainings & clients
                        @else
                            View assigned trainings
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
                Team Statistics
            </div>
        </x-slot>

        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-4 text-center">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $memberCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Members</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-4 text-center">
                <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $coachCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Coaches</div>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-white/5 p-4 text-center">
                <div class="text-3xl font-bold text-sky-600 dark:text-sky-400">{{ $clientCount }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Clients</div>
            </div>
        </div>
    </x-filament::section>

    {{-- Personal Team Info or Danger Zone --}}
    @if($team->is_personal)
        <x-filament::section icon="heroicon-o-information-circle" icon-color="info">
            <x-slot name="heading">
                About Personal Teams
            </x-slot>

            <div class="prose prose-sm dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-400">
                    This is your personal workspace. Use it to:
                </p>
                <ul class="text-gray-600 dark:text-gray-400 mt-2 space-y-1">
                    <li>Create and organize your own exercises</li>
                    <li>Plan personal training sessions</li>
                    <li>Track your individual progress</li>
                </ul>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-4">
                    Personal teams cannot be deleted or left. They are permanently linked to your account.
                </p>
            </div>
        </x-filament::section>
    @else
        <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger">
            <x-slot name="heading">
                Danger Zone
            </x-slot>

            <x-slot name="description">
                Irreversible actions for this team
            </x-slot>

            <div class="space-y-4">
                @if($this->canLeaveTeam())
                    <div class="rounded-lg border border-danger-300 dark:border-danger-700 bg-danger-50 dark:bg-danger-950/50 p-4">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-arrow-right-on-rectangle" class="h-5 w-5 text-danger-500" />
                            <div>
                                <h4 class="text-sm font-medium text-danger-800 dark:text-danger-200">Leave this team</h4>
                                <p class="text-sm text-danger-600 dark:text-danger-400 mt-1">
                                    You will lose access to all team resources. Use the <strong>Leave Team</strong> button in the header to proceed.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($isOwner)
                    <div class="rounded-lg border border-warning-300 dark:border-warning-700 bg-warning-50 dark:bg-warning-950/50 p-4">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 text-warning-500" />
                            <div>
                                <h4 class="text-sm font-medium text-warning-800 dark:text-warning-200">Transfer ownership first</h4>
                                <p class="text-sm text-warning-600 dark:text-warning-400 mt-1">
                                    As the owner, you must transfer ownership to another coach before you can leave. Use the <strong>Transfer Ownership</strong> button in the header.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
