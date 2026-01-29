<x-public-layout :title="__('pages.privacy.title')">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-8">{{ __('pages.privacy.title') }}</h1>

        <div class="prose prose-zinc dark:prose-invert max-w-none">
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                <em>{{ __('pages.common.last_updated', ['date' => date('F j, Y')]) }}</em>
            </p>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.introduction.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.introduction.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.information_collected.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.information_collected.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li><strong>{{ __('pages.privacy.information_collected.items.account_info') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_collected.items.profile_info') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_collected.items.training_data') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_collected.items.team_info') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_collected.items.communications') }}</strong></li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.how_we_use.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.how_we_use.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>{{ __('pages.privacy.how_we_use.items.provide_service') }}</li>
                    <li>{{ __('pages.privacy.how_we_use.items.manage_account') }}</li>
                    <li>{{ __('pages.privacy.how_we_use.items.process_data') }}</li>
                    <li>{{ __('pages.privacy.how_we_use.items.notifications') }}</li>
                    <li>{{ __('pages.privacy.how_we_use.items.support') }}</li>
                    <li>{{ __('pages.privacy.how_we_use.items.analytics') }}</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.information_sharing.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.information_sharing.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li><strong>{{ __('pages.privacy.information_sharing.items.team_members') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_sharing.items.service_providers') }}</strong></li>
                    <li><strong>{{ __('pages.privacy.information_sharing.items.legal') }}</strong></li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.data_security.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.data_security.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.data_retention.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.data_retention.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.your_rights.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.your_rights.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>{{ __('pages.privacy.your_rights.items.access') }}</li>
                    <li>{{ __('pages.privacy.your_rights.items.correction') }}</li>
                    <li>{{ __('pages.privacy.your_rights.items.deletion') }}</li>
                    <li>{{ __('pages.privacy.your_rights.items.portability') }}</li>
                    <li>{{ __('pages.privacy.your_rights.items.objection') }}</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.cookies.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.cookies.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.changes.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.changes.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.privacy.contact.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.privacy.contact.content') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
