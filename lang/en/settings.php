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
        'overview' => [
            'heading' => 'Team Overview',
            'description' => 'Basic information about this team',
            'team_name' => 'Team Name',
            'team_type' => 'Team Type',
            'type_personal' => 'Personal',
            'type_organization' => 'Organization',
            'team_owner' => 'Team Owner',
            'no_owner' => 'No owner',
            'created' => 'Created',
        ],
        'membership' => [
            'heading' => 'Your Membership',
            'description' => 'Your role and permissions in this team',
            'your_role' => 'Your Role',
            'owner' => 'Owner',
            'member_since' => 'Member Since',
            'unknown' => 'Unknown',
            'permissions' => 'Permissions',
            'permissions_coach' => 'Manage exercises, trainings & clients',
            'permissions_client' => 'View assigned trainings',
        ],
        'statistics' => [
            'heading' => 'Team Statistics',
            'total_members' => 'Total Members',
            'coaches' => 'Coaches',
            'clients' => 'Clients',
        ],
        'about_personal' => [
            'heading' => 'About Personal Teams',
            'description' => 'This is your personal workspace. Use it to:',
            'item_exercises' => 'Create and organize your own exercises',
            'item_trainings' => 'Plan personal training sessions',
            'item_progress' => 'Track your individual progress',
            'note' => 'Personal teams cannot be deleted or left. They are permanently linked to your account.',
        ],
        'danger_zone' => [
            'heading' => 'Danger Zone',
            'description' => 'Irreversible actions for this team',
            'leave_team' => 'Leave this team',
            'leave_team_description' => 'You will lose access to all team resources. Use the <strong>Leave Team</strong> button in the header to proceed.',
            'transfer_first' => 'Transfer ownership first',
            'transfer_first_description' => 'As the owner, you must transfer ownership to another coach before you can leave. Use the <strong>Transfer Ownership</strong> button in the header.',
        ],
    ],

    'user' => [
        'navigation_label' => 'User Settings',
        'title' => 'User Settings',
        'subheading' => 'Manage your personal preferences',
        'security_section' => 'Security',
        'security_description' => 'Update your password to keep your account secure',
        'fields' => [
            'avatar' => 'Profile Photo',
            'preferred_language' => 'Preferred Language',
            'weight_unit' => 'Weight Unit',
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
        ],
        'messages' => [
            'saved' => 'Your settings have been saved successfully.',
            'password_changed' => 'Your password has been changed successfully.',
        ],
        'language_options' => [
            'system' => 'System Default',
            'en' => 'English',
            'sl' => 'Slovenščina',
        ],
    ],
];
