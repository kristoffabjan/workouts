<?php

return [
    'application' => [
        'navigation_label' => 'Application Settings',
        'title' => 'Application Settings',
        'subheading' => 'Manage global application settings',
        'fields' => [
            'application_name' => 'Application Name',
            'application_logo' => 'Application Logo',
            'default_language' => 'Default Language',
            'timezone' => 'Timezone',
        ],
    ],

    'team' => [
        'navigation_label' => 'Team Settings',
        'title' => 'Team Settings',
        'subheading' => 'Manage team-specific settings',
        'fields' => [
            'name' => 'Team Name',
            'logo' => 'Team Logo',
            'default_reminder_time' => 'Default Training Reminder Time',
        ],
        'messages' => [
            'saved' => 'Team settings saved successfully.',
        ],
        'personal' => [
            'heading' => 'Personal Workspace',
            'description' => 'This is your personal workspace. Personal team settings are managed automatically and cannot be customized. Use the sections below to view your workspace information.',
        ],
    ],

    'user' => [
        'navigation_label' => 'User Settings',
        'title' => 'User Settings',
        'subheading' => 'Manage your personal preferences',
        'fields' => [
            'avatar' => 'Profile Photo',
            'preferred_language' => 'Preferred Language',
            'weight_unit' => 'Weight Unit',
        ],
        'messages' => [
            'saved' => 'Your settings have been saved successfully.',
        ],
        'language_options' => [
            'system' => 'System Default',
            'en' => 'English',
            'sl' => 'Slovenščina',
        ],
    ],
];
