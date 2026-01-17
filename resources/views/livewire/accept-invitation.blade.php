<div class="flex flex-col gap-6">
    @if($error)
        <x-auth-header title="Invalid Invitation" description="This invitation link is not valid." />
        <flux:callout variant="danger">
            {{ $error }}
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            Go to Login
        </flux:button>
    @elseif($isExpired)
        <x-auth-header title="Invitation Expired" description="This invitation has expired." />
        <flux:callout variant="warning">
            This invitation link has expired. Please contact the person who invited you to request a new invitation.
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            Go to Login
        </flux:button>
    @elseif($isAccepted)
        <x-auth-header title="Invitation Accepted" description="This invitation has already been used." />
        <flux:callout variant="info">
            This invitation has already been accepted. You can log in to access the app.
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            Go to Login
        </flux:button>
    @else
        <x-auth-header
            title="Accept Invitation"
            :description="$teamName ? 'You have been invited to join ' . $teamName : 'You have been invited to join Workouts App'"
        />

        <form wire:submit="accept" class="flex flex-col gap-6">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    <p><strong>Email:</strong> {{ $email }}</p>
                    @if($teamName)
                        <p><strong>Team:</strong> {{ $teamName }}</p>
                        <p><strong>Role:</strong> {{ $roleName }}</p>
                    @endif
                </div>
            </div>

            @if(!$userExists)
                <flux:text class="text-center text-sm">
                    Create a password to complete your account setup.
                </flux:text>

                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input
                        wire:model="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Password"
                        viewable
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm Password</flux:label>
                    <flux:input
                        wire:model="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        viewable
                    />
                    <flux:error name="password_confirmation" />
                </flux:field>
            @else
                <flux:callout variant="info">
                    You already have an account. Click below to join the team.
                </flux:callout>
            @endif

            <flux:button type="submit" variant="primary" class="w-full">
                {{ $userExists ? 'Join Team' : 'Create Account & Join' }}
            </flux:button>
        </form>
    @endif
</div>
