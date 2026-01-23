<?php

return [
    'resource' => [
        'label' => 'Team',
        'plural_label' => 'Teams',
        'navigation_label' => 'Teams',
    ],

    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'is_personal' => 'Personal Team',
        'owner' => 'Owner',
        'members_count' => 'Members',
        'created_at' => 'Created At',
    ],

    'filters' => [
        'type' => 'Team Type',
        'personal' => 'Personal',
        'organization' => 'Organization',
    ],

    'pages' => [
        'register' => 'Register new team',
        'settings' => 'Team Settings',
    ],

    'actions' => [
        'leave' => 'Leave Team',
        'transfer_ownership' => 'Transfer Ownership',
    ],

    'leave' => [
        'confirm' => 'Are you sure you want to leave this team? You will lose access to all team resources.',
        'success' => 'You have left the team.',
        'cannot_leave_owner' => 'Team owners cannot leave. Transfer ownership first.',
    ],

    'transfer' => [
        'confirm' => 'Are you sure you want to transfer ownership? The new owner will have full control over this team.',
        'select_user' => 'Select New Owner',
        'success' => 'Ownership transferred successfully.',
    ],

    'relation_manager' => [
        'members' => 'Team Members',
    ],
];
