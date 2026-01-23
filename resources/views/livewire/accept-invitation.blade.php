<div class="flex flex-col gap-6">
    @if($error)
        <x-auth-header :title="__('invitation.invalid.title')" :description="__('invitation.invalid.message')" />
        <flux:callout variant="danger">
            {{ $error }}
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            {{ __('invitation.invalid.action') }}
        </flux:button>
    @elseif($isExpired)
        <x-auth-header :title="__('invitation.expired.title')" :description="__('invitation.expired.message')" />
        <flux:callout variant="warning">
            {{ __('invitation.expired.description') }}
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            {{ __('invitation.invalid.action') }}
        </flux:button>
    @elseif($isAccepted)
        <x-auth-header :title="__('invitation.accepted.title')" :description="__('invitation.accepted.message')" />
        <flux:callout variant="info">
            {{ __('invitation.accepted.description') }}
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            {{ __('invitation.invalid.action') }}
        </flux:button>
    @else
        <x-auth-header
            :title="__('invitation.accept.title')"
            :description="$teamName ? __('invitation.accept.team_message', ['team_name' => $teamName]) : __('invitation.accept.individual_message')"
        />

        <form wire:submit="accept" class="flex flex-col gap-6">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    <p><strong>{{ __('invitation.accept.email') }}</strong> {{ $email }}</p>
                    @if($teamName)
                        <p><strong>{{ __('invitation.accept.team') }}</strong> {{ $teamName }}</p>
                        <p><strong>{{ __('invitation.accept.role') }}</strong> {{ $roleName }}</p>
                    @endif
                </div>
            </div>

            @if(!$userExists)
                <flux:text class="text-center text-sm">
                    {{ __('invitation.accept.password_instruction') }}
                </flux:text>

                <flux:field>
                    <flux:label>{{ __('invitation.accept.password') }}</flux:label>
                    <flux:input
                        wire:model="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('invitation.accept.password')"
                        viewable
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('invitation.accept.password_confirmation') }}</flux:label>
                    <flux:input
                        wire:model="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('invitation.accept.password_confirmation')"
                        viewable
                    />
                    <flux:error name="password_confirmation" />
                </flux:field>
            @else
                <flux:callout variant="info">
                    {{ __('invitation.accept.existing_user') }}
                </flux:callout>
            @endif

            <flux:button type="submit" variant="primary" class="w-full">
                {{ $userExists ? __('invitation.accept.join_team') : __('invitation.accept.create_account') }}
            </flux:button>
        </form>
    @endif
</div>
