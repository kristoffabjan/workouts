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
    ],
];
