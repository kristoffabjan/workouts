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
            'save_failed' => 'Shranjevanje nastavitev ekipe ni uspelo',
            'save_failed_body' => 'Pri shranjevanju nastavitev ekipe je prišlo do napake. Prosimo, poskusite znova ali se obrnite na podporo, če težava ne izgine.',
        ],
        'personal' => [
            'heading' => 'Osebni prostor',
            'description' => 'To je vaš osebni profil. Nastavitve osebne ekipe se upravljajo samodejno in jih ni mogoče prilagoditi. Uporabite spodnje razdelke za ogled informacij o vašem prostoru.',
        ],
        'overview' => [
            'heading' => 'Pregled ekipe',
            'description' => 'Osnovne informacije o tej ekipi',
            'team_name' => 'Ime ekipe',
            'team_type' => 'Vrsta ekipe',
            'type_personal' => 'Osebna',
            'type_organization' => 'Organizacija',
            'team_owner' => 'Lastnik ekipe',
            'no_owner' => 'Brez lastnika',
            'created' => 'Ustvarjeno',
        ],
        'membership' => [
            'heading' => 'Vaše članstvo',
            'description' => 'Vaša vloga in dovoljenja v tej ekipi',
            'your_role' => 'Vaša vloga',
            'owner' => 'Lastnik',
            'member_since' => 'Član od',
            'unknown' => 'Neznano',
            'permissions' => 'Dovoljenja',
            'permissions_coach' => 'Upravljanje vaj, treningov in strank',
            'permissions_client' => 'Ogled dodeljenih treningov',
        ],
        'statistics' => [
            'heading' => 'Statistika ekipe',
            'total_members' => 'Skupno članov',
            'coaches' => 'Trenerji',
            'clients' => 'Stranke',
        ],
        'about_personal' => [
            'heading' => 'O osebnih ekipah',
            'description' => 'To je vaš osebni prostor. Uporabite ga za:',
            'item_exercises' => 'Ustvarjanje in organiziranje lastnih vaj',
            'item_trainings' => 'Načrtovanje osebnih vadb',
            'item_progress' => 'Sledenje individualnemu napredku',
            'note' => 'Osebnih ekip ni mogoče izbrisati ali zapustiti. Trajno so povezane z vašim računom.',
        ],
        'danger_zone' => [
            'heading' => 'Nevarno območje',
            'description' => 'Nepovratna dejanja za to ekipo',
            'leave_team' => 'Zapusti to ekipo',
            'leave_team_description' => 'Izgubili boste dostop do vseh virov ekipe. Za nadaljevanje uporabite gumb <strong>Zapusti ekipo</strong> v glavi.',
            'transfer_first' => 'Najprej prenesite lastništvo',
            'transfer_first_description' => 'Kot lastnik morate najprej prenesti lastništvo na drugega trenerja, preden lahko zapustite ekipo. Uporabite gumb <strong>Prenesi lastništvo</strong> v glavi.',
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
            'save_failed' => 'Shranjevanje ni uspelo',
            'save_failed_body' => 'Pri shranjevanju nastavitev je prišlo do napake. Prosimo, poskusite znova ali se obrnite na podporo, če težava ne izgine.',
        ],
        'language_options' => [
            'system' => 'Sistemske privzete',
            'en' => 'English',
            'sl' => 'Slovenščina',
        ],
    ],
];
