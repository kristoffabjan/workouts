<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upcoming Trainings Days
    |--------------------------------------------------------------------------
    |
    | The number of days to show upcoming trainings for in the dashboard widget.
    | This can be configured per team in settings later.
    |
    */
    'upcoming_days' => env('WORKOUTS_UPCOMING_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Feedback Submission Deadline
    |--------------------------------------------------------------------------
    |
    | The number of days after the scheduled training date within which
    | athletes can submit feedback. After this deadline, feedback submission
    | will be disabled.
    |
    */
    'feedback_deadline_days' => env('WORKOUTS_FEEDBACK_DEADLINE_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Missed Training Deadline
    |--------------------------------------------------------------------------
    |
    | The number of days after the scheduled training date within which
    | the training must be marked as completed. After this deadline, if not
    | completed, the training will be automatically marked as missed/skipped.
    |
    */
    'missed_deadline_days' => env('WORKOUTS_MISSED_DEADLINE_DAYS', 3),
];
