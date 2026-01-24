<div class="flex flex-col gap-6">
    @if($submitted)
        <x-auth-header
            :title="__('auth.request_access.success_title')"
            :description="__('auth.request_access.success_description')"
        />
        <flux:callout variant="success">
            {{ __('auth.request_access.success_message') }}
        </flux:callout>
        <flux:button href="/app/login" variant="primary" class="w-full">
            {{ __('auth.request_access.back_to_login') }}
        </flux:button>
    @else
        <x-auth-header
            :title="__('auth.request_access.title')"
            :description="__('auth.request_access.description')"
        />

        <form wire:submit="submit" class="flex flex-col gap-6">
            <flux:field>
                <flux:label>{{ __('auth.request_access.name') }}</flux:label>
                <flux:input
                    wire:model="name"
                    type="text"
                    required
                    autocomplete="name"
                    :placeholder="__('auth.request_access.name_placeholder')"
                />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('auth.request_access.email') }}</flux:label>
                <flux:input
                    wire:model="email"
                    type="email"
                    required
                    autocomplete="email"
                    :placeholder="__('auth.request_access.email_placeholder')"
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('auth.request_access.message') }} <span class="text-zinc-500">({{ __('auth.request_access.optional') }})</span></flux:label>
                <flux:textarea
                    wire:model="message"
                    rows="3"
                    :placeholder="__('auth.request_access.message_placeholder')"
                />
                <flux:error name="message" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('auth.request_access.submit') }}
            </flux:button>
        </form>

        <div class="text-center">
            <flux:text class="text-sm">
                {{ __('auth.request_access.already_have_account') }}
                <flux:link href="/app/login">{{ __('auth.request_access.sign_in') }}</flux:link>
            </flux:text>
        </div>
    @endif
</div>
