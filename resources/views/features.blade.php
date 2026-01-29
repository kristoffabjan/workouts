<x-public-layout :title="__('pages.features.title')">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-zinc-900 dark:text-white mb-4">{{ __('pages.features.title') }}</h1>
            <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                {{ __('pages.features.subtitle') }}
            </p>
        </div>

        <div class="space-y-16">
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-[#5A9CB5]/10 dark:bg-[#5A9CB5]/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-[#5A9CB5]">
                            <path d="M15.98 1.804a1 1 0 0 0-1.96 0l-.24 1.192a1 1 0 0 1-.784.785l-1.192.238a1 1 0 0 0 0 1.962l1.192.238a1 1 0 0 1 .785.785l.238 1.192a1 1 0 0 0 1.962 0l.238-1.192a1 1 0 0 1 .785-.785l1.192-.238a1 1 0 0 0 0-1.962l-1.192-.238a1 1 0 0 1-.785-.785l-.238-1.192ZM6.949 5.684a1 1 0 0 0-1.898 0l-.683 2.051a1 1 0 0 1-.633.633l-2.051.683a1 1 0 0 0 0 1.898l2.051.684a1 1 0 0 1 .633.632l.683 2.051a1 1 0 0 0 1.898 0l.683-2.051a1 1 0 0 1 .633-.633l2.051-.683a1 1 0 0 0 0-1.898l-2.051-.683a1 1 0 0 1-.633-.633L6.95 5.684ZM13.949 13.684a1 1 0 0 0-1.898 0l-.184.551a1 1 0 0 1-.632.633l-.551.183a1 1 0 0 0 0 1.898l.551.183a1 1 0 0 1 .633.633l.183.551a1 1 0 0 0 1.898 0l.184-.551a1 1 0 0 1 .632-.633l.551-.183a1 1 0 0 0 0-1.898l-.551-.184a1 1 0 0 1-.633-.632l-.183-.551Z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('pages.features.exercise_library.title') }}</h2>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        {{ __('pages.features.exercise_library.description') }}
                    </p>
                    <ul class="space-y-3 text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.exercise_library.rich_descriptions.title') }}</strong> - {{ __('pages.features.exercise_library.rich_descriptions.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.exercise_library.video_references.title') }}</strong> - {{ __('pages.features.exercise_library.video_references.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.exercise_library.tag_system.title') }}</strong> - {{ __('pages.features.exercise_library.tag_system.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.exercise_library.global_library.title') }}</strong> - {{ __('pages.features.exercise_library.global_library.description') }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-[#5A9CB5]/10 dark:bg-[#5A9CB5]/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-[#5A9CB5]">
                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('pages.features.training_scheduling.title') }}</h2>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        {{ __('pages.features.training_scheduling.description') }}
                    </p>
                    <ul class="space-y-3 text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.training_scheduling.calendar_view.title') }}</strong> - {{ __('pages.features.training_scheduling.calendar_view.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.training_scheduling.bulk_scheduling.title') }}</strong> - {{ __('pages.features.training_scheduling.bulk_scheduling.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.training_scheduling.weekly_patterns.title') }}</strong> - {{ __('pages.features.training_scheduling.weekly_patterns.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.training_scheduling.status_tracking.title') }}</strong> - {{ __('pages.features.training_scheduling.status_tracking.description') }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-[#5A9CB5]/10 dark:bg-[#5A9CB5]/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-[#5A9CB5]">
                            <path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('pages.features.team_management.title') }}</h2>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        {{ __('pages.features.team_management.description') }}
                    </p>
                    <ul class="space-y-3 text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.team_management.multi_team_support.title') }}</strong> - {{ __('pages.features.team_management.multi_team_support.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.team_management.role_based_access.title') }}</strong> - {{ __('pages.features.team_management.role_based_access.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.team_management.email_invitations.title') }}</strong> - {{ __('pages.features.team_management.email_invitations.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.team_management.personal_teams.title') }}</strong> - {{ __('pages.features.team_management.personal_teams.description') }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-[#5A9CB5]/10 dark:bg-[#5A9CB5]/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-[#5A9CB5]">
                            <path fill-rule="evenodd" d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.517V17.25a.75.75 0 0 0 1.075.676L10 15.082l5.925 2.844A.75.75 0 0 0 17 17.25V4.517c0-1.103-.806-2.068-1.93-2.207A41.403 41.403 0 0 0 10 2Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('pages.features.feedback_tracking.title') }}</h2>
                </div>
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                        {{ __('pages.features.feedback_tracking.description') }}
                    </p>
                    <ul class="space-y-3 text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.feedback_tracking.completion_tracking.title') }}</strong> - {{ __('pages.features.feedback_tracking.completion_tracking.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.feedback_tracking.training_feedback.title') }}</strong> - {{ __('pages.features.feedback_tracking.training_feedback.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.feedback_tracking.coach_notifications.title') }}</strong> - {{ __('pages.features.feedback_tracking.coach_notifications.description') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500 shrink-0 mt-0.5">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>{{ __('pages.features.feedback_tracking.dashboard_stats.title') }}</strong> - {{ __('pages.features.feedback_tracking.dashboard_stats.description') }}</span>
                        </li>
                    </ul>
                </div>
            </section>
        </div>

        <div class="mt-16 text-center">
            <a href="{{ route('filament.app.auth.login') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-[#5A9CB5] hover:bg-[#4A8CA5] text-white font-semibold rounded-lg transition-colors duration-200">
                {{ __('pages.common.get_started') }}
            </a>
        </div>
    </div>
</x-public-layout>
