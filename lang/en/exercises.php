<?php

return [
    'resource' => [
        'label' => 'Exercise',
        'plural_label' => 'Exercises',
        'navigation_label' => 'Exercises',
    ],

    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'video_urls' => 'Video URLs',
        'tags' => 'Tags',
        'created_by' => 'Created By',
        'notes' => 'Notes',
        'sort_order' => 'Sort Order',
    ],

    'placeholders' => [
        'video_url' => 'https://youtube.com/watch?v=...',
        'tags' => 'Add tags...',
        'notes' => 'Optional notes for this exercise',
    ],

    'actions' => [
        'add_video_url' => 'Add Video URL',
        'add_from_library' => 'Add from Library',
        'attach' => 'Attach Exercise',
    ],

    'filters' => [
        'tag' => 'Tag',
        'created_by' => 'Created By',
    ],

    'library' => [
        'title' => 'Add Exercises from Library',
        'description' => 'Select exercises from the global library to add to your team.',
        'select_label' => 'Select exercises from the global library',
        'select_exercises' => 'Select Exercises',
        'helper_text' => 'Search by name or tag. Exercises with the same name already in your team will be skipped.',
        'success' => 'Added :count exercise(s) to your library',
        'no_exercises' => 'No new exercises available',
        'added_title' => 'Exercises added',
        'added_body' => ':count exercise(s) added to your team.',
        'none_added_title' => 'No exercises added',
        'none_added_body' => 'Selected exercises already exist in your team. Try selecting different exercises or create a new custom exercise.',
    ],

    'tags' => [
        'strength' => 'Strength',
        'cardio' => 'Cardio',
        'flexibility' => 'Flexibility',
        'mobility' => 'Mobility',
        'core' => 'Core',
        'upper-body' => 'Upper Body',
        'lower-body' => 'Lower Body',
        'full-body' => 'Full Body',
        'compound' => 'Compound',
        'isolation' => 'Isolation',
        'olympic' => 'Olympic',
        'plyometric' => 'Plyometric',
        'bodyweight' => 'Bodyweight',
        'machine' => 'Machine',
        'dumbbell' => 'Dumbbell',
        'barbell' => 'Barbell',
        'kettlebell' => 'Kettlebell',
        'unilateral' => 'Unilateral',
        'bilateral' => 'Bilateral',
        'push' => 'Push',
        'pull' => 'Pull',
        'endurance' => 'Endurance',
        'power' => 'Power',
    ],

    'validation' => [
        'cannot_delete_attached' => 'This exercise cannot be deleted because it is attached to one or more trainings.',
        'invalid_video_url' => 'Please enter a valid video URL.',
    ],
];
