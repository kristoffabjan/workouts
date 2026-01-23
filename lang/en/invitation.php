<?php

return [
    'invalid' => [
        'title' => 'Invalid Invitation',
        'message' => 'This invitation link is not valid.',
        'action' => 'Go to Login',
    ],

    'expired' => [
        'title' => 'Invitation Expired',
        'message' => 'This invitation has expired.',
        'description' => 'This invitation link has expired. Please contact the person who invited you to request a new invitation.',
    ],

    'accepted' => [
        'title' => 'Invitation Accepted',
        'message' => 'This invitation has already been used.',
        'description' => 'This invitation has already been accepted. You can log in to access the app.',
    ],

    'accept' => [
        'title' => 'Accept Invitation',
        'team_message' => 'You have been invited to join :team_name',
        'individual_message' => 'You have been invited to join Workouts App',
        'email' => 'Email:',
        'team' => 'Team:',
        'role' => 'Role:',
        'password_instruction' => 'Create a password to complete your account setup.',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'create_account' => 'Create Account & Join',
        'join_team' => 'Join Team',
        'existing_user' => 'You already have an account. Click below to join the team.',
    ],
];
