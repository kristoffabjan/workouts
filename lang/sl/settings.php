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
        'personal' => [
            'heading' => 'Osebni prostor',
            'description' => 'To je vaš osebni prostor. Nastavitve osebne ekipe se upravljajo samodejno in jih ni mogoče prilagoditi. Uporabite spodnje razdelke za ogled informacij o vašem prostoru.',
        ],
    ],

    'user' => [
        'navigation_label' => 'Uporabniške nastavitve',
        'title' => 'Uporabniške nastavitve',
        'subheading' => 'Upravljanje osebnih nastavitev',
        'security_section' => 'Varnost',
        'security_description' => 'Posodobite geslo za varnost vašega računa',
        'fields' => [
            'avatar' => 'Profilna slika',
            'preferred_language' => 'Prednostni jezik',
            'weight_unit' => 'Enota teže',
            'current_password' => 'Trenutno geslo',
            'new_password' => 'Novo geslo',
            'confirm_password' => 'Potrdite geslo',
        ],
        'messages' => [
            'saved' => 'Vaše nastavitve so bile uspešno shranjene.',
            'password_changed' => 'Vaše geslo je bilo uspešno spremenjeno.',
        ],
        'language_options' => [
            'system' => 'Sistemske privzete',
            'en' => 'English',
            'sl' => 'Slovenščina',
        ],
    ],
];
