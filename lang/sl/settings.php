<?php

return [
    'application' => [
        'navigation_label' => 'Nastavitve aplikacije',
        'title' => 'Nastavitve aplikacije',
        'subheading' => 'Upravljanje globalnih nastavitev aplikacije',
        'fields' => [
            'application_name' => 'Ime aplikacije',
            'application_logo' => 'Logotip aplikacije',
            'default_language' => 'Privzeti jezik',
            'timezone' => 'Časovni pas',
        ],
    ],

    'team' => [
        'navigation_label' => 'Nastavitve ekipe',
        'title' => 'Nastavitve ekipe',
        'subheading' => 'Upravljanje nastavitev ekipe',
        'fields' => [
            'name' => 'Ime ekipe',
            'logo' => 'Logotip ekipe',
            'default_reminder_time' => 'Privzeti čas opomnika za vadbo',
        ],
        'messages' => [
            'saved' => 'Nastavitve ekipe so bile uspešno shranjene.',
        ],
    ],
];
