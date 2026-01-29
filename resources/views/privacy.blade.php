<x-public-layout :title="__('pages.privacy.title')">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4 tracking-tight">{{ __('pages.privacy.title') }}</h1>
        <p class="text-[#627d98] mb-10">
            <em>{{ __('pages.common.last_updated', ['date' => date('F j, Y')]) }}</em>
        </p>

        <div class="space-y-6">
            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.introduction.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.introduction.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.information_collected.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.privacy.information_collected.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_collected.items.account_info') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_collected.items.profile_info') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_collected.items.training_data') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_collected.items.team_info') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_collected.items.communications') }}</strong>
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.how_we_use.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.privacy.how_we_use.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.provide_service') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.manage_account') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.process_data') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.notifications') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.support') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.how_we_use.items.analytics') }}
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.information_sharing.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.privacy.information_sharing.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_sharing.items.team_members') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_sharing.items.service_providers') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        <strong class="text-white">{{ __('pages.privacy.information_sharing.items.legal') }}</strong>
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.data_security.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.data_security.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.data_retention.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.data_retention.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.your_rights.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed mb-4">
                    {{ __('pages.privacy.your_rights.content') }}
                </p>
                <ul class="space-y-2 text-[#829ab1]">
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.your_rights.items.access') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.your_rights.items.correction') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.your_rights.items.deletion') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.your_rights.items.portability') }}
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5A9CB5] mt-2 shrink-0"></span>
                        {{ __('pages.privacy.your_rights.items.objection') }}
                    </li>
                </ul>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.cookies.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.cookies.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.changes.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.changes.content') }}
                </p>
            </section>

            <section class="p-6 rounded-2xl bg-[#1a2744]/80 border border-[#243b53]">
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('pages.privacy.contact.title') }}</h2>
                <p class="text-[#9fb3c8] leading-relaxed">
                    {{ __('pages.privacy.contact.content') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
