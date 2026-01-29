<x-public-layout :title="__('pages.terms.title')">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-8">{{ __('pages.terms.title') }}</h1>

        <div class="prose prose-zinc dark:prose-invert max-w-none">
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                <em>{{ __('pages.common.last_updated', ['date' => date('F j, Y')]) }}</em>
            </p>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.acceptance.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.acceptance.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.description.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.description.content', ['app_name' => config('app.name', 'Workouts')]) }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.user_accounts.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.user_accounts.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>{{ __('pages.terms.user_accounts.items.accurate_info') }}</li>
                    <li>{{ __('pages.terms.user_accounts.items.age_requirement') }}</li>
                    <li>{{ __('pages.terms.user_accounts.items.responsible_content') }}</li>
                    <li>{{ __('pages.terms.user_accounts.items.notify_unauthorized') }}</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.acceptable_use.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.acceptable_use.content') }}
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>{{ __('pages.terms.acceptable_use.items.violating_laws') }}</li>
                    <li>{{ __('pages.terms.acceptable_use.items.infringing_rights') }}</li>
                    <li>{{ __('pages.terms.acceptable_use.items.transmitting_harmful') }}</li>
                    <li>{{ __('pages.terms.acceptable_use.items.unauthorized_access') }}</li>
                    <li>{{ __('pages.terms.acceptable_use.items.interfering') }}</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.content_ownership.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.content_ownership.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.disclaimer.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.disclaimer.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.limitation.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.limitation.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.changes.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.changes.content') }}
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">{{ __('pages.terms.contact.title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ __('pages.terms.contact.content') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
