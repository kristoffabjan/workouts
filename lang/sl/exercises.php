<?php

return [
    'resource' => [
        'label' => 'Vaja',
        'plural_label' => 'Vaje',
        'navigation_label' => 'Vaje',
    ],

    'fields' => [
        'name' => 'Ime',
        'description' => 'Opis',
        'video_urls' => 'Video povezave',
        'tags' => 'Oznake',
        'created_by' => 'Ustvaril',
        'notes' => 'Opombe',
        'sort_order' => 'Vrstni red',
    ],

    'placeholders' => [
        'video_url' => 'https://youtube.com/watch?v=...',
        'tags' => 'Dodaj oznake...',
        'notes' => 'Neobvezne opombe za to vajo',
    ],

    'actions' => [
        'add_video_url' => 'Dodaj video povezavo',
        'add_from_library' => 'Dodaj iz knjižnice',
        'attach' => 'Pripni vajo',
    ],

    'filters' => [
        'tag' => 'Oznaka',
        'created_by' => 'Ustvaril',
    ],

    'library' => [
        'title' => 'Dodaj vaje iz knjižnice',
        'description' => 'Izberi vaje iz globalne knjižnice za dodajanje v ekipo.',
        'select_exercises' => 'Izberi vaje',
        'success' => 'Dodanih :count vaj v knjižnico',
        'no_exercises' => 'Ni novih vaj na voljo',
    ],

    'tags' => [
        'strength' => 'Moč',
        'cardio' => 'Kardio',
        'flexibility' => 'Fleksibilnost',
        'mobility' => 'Mobilnost',
        'core' => 'Jedro',
        'upper-body' => 'Zgornji del telesa',
        'lower-body' => 'Spodnji del telesa',
        'full-body' => 'Celotno telo',
        'compound' => 'Sestavljene',
        'isolation' => 'Izolirane',
        'olympic' => 'Olimpijske',
        'plyometric' => 'Pliometrične',
        'bodyweight' => 'Telesna teža',
        'machine' => 'Naprave',
        'dumbbell' => 'Ročke',
        'barbell' => 'Olimpijska palica',
        'kettlebell' => 'Kettlebell',
        'unilateral' => 'Enostransko',
        'bilateral' => 'Dvostransko',
        'push' => 'Potisk',
        'pull' => 'Vlek',
        'endurance' => 'Vzdržljivost',
        'power' => 'Eksplozivnost',
    ],

    'validation' => [
        'cannot_delete_attached' => 'Te vaje ni mogoče izbrisati, ker je pripeta enemu ali več treningom.',
        'invalid_video_url' => 'Prosimo, vnesite veljavno video povezavo.',
    ],
];
