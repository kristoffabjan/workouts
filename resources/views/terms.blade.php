<x-public-layout title="Terms of Service">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-8">Terms of Service</h1>

        <div class="prose prose-zinc dark:prose-invert max-w-none">
            <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                <em>Last updated: {{ date('F j, Y') }}</em>
            </p>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">1. Acceptance of Terms</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    By accessing and using {{ config('app.name', 'Workouts') }} ("the Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">2. Description of Service</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    {{ config('app.name', 'Workouts') }} is a training management platform that allows coaches to create, schedule, and manage workout programs, and athletes to view and track their assigned trainings.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">3. User Accounts</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    To use certain features of the Service, you must create an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>You must provide accurate and complete information when creating an account</li>
                    <li>You must be at least 18 years old to create an account</li>
                    <li>You are responsible for all content you submit through your account</li>
                    <li>You must notify us immediately of any unauthorized use of your account</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">4. Acceptable Use</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    You agree not to use the Service for any unlawful purpose or in any way that could damage, disable, or impair the Service. Prohibited activities include:
                </p>
                <ul class="list-disc list-inside text-zinc-600 dark:text-zinc-400 space-y-2">
                    <li>Violating any applicable laws or regulations</li>
                    <li>Infringing on the rights of others</li>
                    <li>Transmitting harmful or malicious code</li>
                    <li>Attempting to gain unauthorized access to the Service</li>
                    <li>Interfering with other users' enjoyment of the Service</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">5. Content Ownership</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    You retain ownership of any content you submit to the Service. By submitting content, you grant us a license to use, store, and display that content as necessary to provide the Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">6. Disclaimer of Warranties</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    The Service is provided "as is" without warranties of any kind. We do not guarantee that the Service will be uninterrupted, secure, or error-free. Use of the Service is at your own risk.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">7. Limitation of Liability</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    To the maximum extent permitted by law, we shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the Service.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">8. Changes to Terms</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    We reserve the right to modify these Terms of Service at any time. We will notify users of any material changes by posting the updated terms on this page. Your continued use of the Service after changes are posted constitutes acceptance of the modified terms.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-semibold text-zinc-900 dark:text-white mb-4">9. Contact Information</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                    If you have any questions about these Terms of Service, please contact us.
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
