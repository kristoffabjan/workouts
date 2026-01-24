<?php

return [
    'navigation_label' => 'Access Requests',
    'model_label' => 'Access Request',
    'plural_model_label' => 'Access Requests',

    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'message' => 'Message',
        'status' => 'Status',
        'processed_by' => 'Processed By',
        'processed_at' => 'Processed At',
        'created_at' => 'Requested At',
    ],

    'actions' => [
        'approve' => 'Approve',
        'reject' => 'Reject',
        'approve_heading' => 'Approve Access Request',
        'approve_description' => 'Are you sure you want to approve this access request?',
        'reject_heading' => 'Reject Access Request',
        'reject_description' => 'Are you sure you want to reject this access request?',
    ],

    'messages' => [
        'approved' => 'Access request has been approved.',
        'rejected' => 'Access request has been rejected.',
        'invitation_sent' => 'An invitation email has been sent to the user.',
    ],
];
