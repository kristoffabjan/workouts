<x-public-layout :title="__('pages.terms.title')">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4 tracking-tight">{{ __('pages.terms.title') }}</h1>
        <p class="text-[#627d98] mb-10">
            <em>{{ __('pages.common.last_updated', ['date' => date('F j, Y')]) }}</em>
        </p>

        <div class="space-y-6">
            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.acceptance.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.acceptance.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.description.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.description.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.user_accounts.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.terms.user_accounts.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.user_accounts.items.accurate_info') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.user_accounts.items.age_requirement') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.user_accounts.items.responsible_content') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.user_accounts.items.notify_unauthorized') }}
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.acceptable_use.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.terms.acceptable_use.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.acceptable_use.items.violating_laws') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.acceptable_use.items.infringing_rights') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.acceptable_use.items.transmitting_harmful') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.acceptable_use.items.unauthorized_access') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.terms.acceptable_use.items.interfering') }}
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.content_ownership.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.content_ownership.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.disclaimer.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.disclaimer.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.limitation.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.limitation.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.changes.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.changes.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.terms.contact.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.terms.contact.content') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
