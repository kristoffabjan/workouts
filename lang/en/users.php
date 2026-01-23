<?php

return [
    'resource' => [
        'label' => 'Team Member',
        'plural_label' => 'Team Members',
        'navigation_label' => 'Team Members',
    ],

    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'role_in_team' => 'Role in Team',
        'team_owner' => 'Team Owner',
        'created_at' => 'Joined',
    ],

    'filters' => [
        'role' => 'Role',
    ],

    'actions' => [
        'invite' => 'Invite User',
        'invite_to_team' => 'Invite User to Team',
        'remove' => 'Remove',
        'remove_from_team' => 'Remove from Team',
    ],

    'invitation' => [
        'title' => 'Invite User',
        'description' => 'User will receive an invitation email and a personal team will be created for them.',
        'team_description' => 'An invitation email will be sent. Existing users can join with one click.',
        'email_label' => 'Email address',
        'role_label' => 'Role',
        'success' => 'Invitation sent',
        'success_message' => 'An invitation has been sent to **:email**.',
    ],

    'remove' => [
        'confirm' => 'Are you sure you want to remove :name from this team?',
        'success' => 'User removed',
        'success_message' => ':name has been removed from the team.',
    ],

    'relation_manager' => [
        'assigned_trainings' => 'Assigned Trainings',
    ],

    'validation' => [
        'cannot_delete_with_content' => 'This user cannot be deleted because they have created trainings or exercises. Consider transferring ownership first.',
        'cannot_delete_self' => 'You cannot delete your own account.',
    ],
];
