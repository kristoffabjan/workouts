<?php

return [
    'resource' => [
        'label' => 'Training',
        'plural_label' => 'Trainings',
        'navigation_label' => 'Trainings',
    ],

    'fields' => [
        'title' => 'Title',
        'content' => 'Content',
        'status' => 'Status',
        'scheduled_at' => 'Scheduled At',
        'created_by' => 'Created By',
        'assigned_users' => 'Assigned To',
        'exercises_count' => 'Exercises',
    ],

    'sections' => [
        'training_details' => 'Training Details',
        'content' => 'Content',
        'your_completion' => 'Your Completion',
    ],

    'filters' => [
        'status' => 'Status',
        'date_range' => 'Date Range',
        'from' => 'From',
        'until' => 'Until',
        'assigned_to' => 'Assigned To',
    ],

    'actions' => [
        'schedule' => 'Schedule',
        'mark_complete' => 'Mark Training as Complete',
        'edit_feedback' => 'Edit Your Feedback',
    ],

    'schedule' => [
        'title' => 'Schedule Training',
        'single_date' => 'Single Date',
        'multiple_dates' => 'Multiple Dates',
        'weekly_pattern' => 'Weekly Pattern',
        'schedule_date_time' => 'Schedule Date & Time',
        'start_date_time' => 'Start Date & Time',
        'number_of_weeks' => 'Number of Weeks',
        'days_of_week' => 'Days of Week',
        'options' => 'Options',
        'copy_exercises' => 'Copy Exercises',
        'copy_exercises_description' => 'Include all exercises with notes and order',
        'assign_to' => 'Assign To',
        'assign_to_placeholder' => 'Leave empty to keep original assignments',
    ],

    'days' => [
        'sunday' => 'Sunday',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ],

    'notifications' => [
        'scheduled' => 'Training scheduled',
        'created_count' => 'Created :count training(s)',
        'marked_complete' => 'Training marked as complete',
        'feedback_updated' => 'Feedback updated',
    ],

    'completion' => [
        'confirm_message' => 'Confirm that you have completed this training. You can optionally leave feedback for your coach.',
        'edit_feedback_message' => 'Update your feedback for this completed training.',
        'feedback_label' => 'Feedback',
        'feedback_placeholder' => 'How was the training? Any notes for your coach?',
        'completed_at' => 'Completed At',
        'not_completed' => 'Not completed yet',
        'no_feedback' => 'No feedback provided',
    ],

    'validation' => [
        'scheduled_date_required' => 'A scheduled date is required when status is Scheduled.',
        'scheduled_date_in_past' => 'The scheduled date cannot be in the past.',
        'no_date_selected' => 'Please select at least one date for scheduling.',
        'feedback_deadline_passed' => 'The feedback deadline has passed. You can no longer submit feedback for this training.',
        'user_not_in_team' => 'One or more selected users are not members of this team.',
        'cannot_edit_past_training' => 'You cannot edit a training that has already been scheduled in the past.',
        'cannot_assign_past_training' => 'You cannot assign users to a training that has already been scheduled in the past.',
    ],

    'calendar' => [
        'navigation_label' => 'Calendar',
        'title' => 'Training Calendar',
        'legend' => [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'skipped' => 'Skipped',
        ],
        'coach_description' => 'Viewing all team trainings. Click on a training to view details.',
        'client_description' => 'Viewing your assigned trainings. Click on a training to view details.',
    ],
];
