<?php

return [
    'resource' => [
        'label' => 'Ekipa',
        'plural_label' => 'Ekipe',
        'navigation_label' => 'Ekipe',
    ],

    'fields' => [
        'name' => 'Ime',
        'slug' => 'Slug',
        'is_personal' => 'Osebna ekipa',
        'owner' => 'Lastnik',
        'members_count' => 'Člani',
        'created_at' => 'Ustvarjeno',
    ],

    'filters' => [
        'type' => 'Vrsta ekipe',
        'personal' => 'Osebna',
        'organization' => 'Organizacija',
    ],

    'pages' => [
        'register' => 'Registriraj novo ekipo',
        'settings' => 'Nastavitve ekipe',
    ],

    'actions' => [
        'leave' => 'Zapusti ekipo',
        'transfer_ownership' => 'Prenesi lastništvo',
    ],

    'leave' => [
        'confirm' => 'Ali ste prepričani, da želite zapustiti to ekipo? Izgubili boste dostop do vseh virov ekipe.',
        'success' => 'Zapustili ste ekipo.',
        'cannot_leave_owner' => 'Lastniki ekipe ne morejo zapustiti. Najprej prenesite lastništvo.',
    ],

    'transfer' => [
        'confirm' => 'Ali ste prepričani, da želite prenesti lastništvo? Novi lastnik bo imel popoln nadzor nad to ekipo.',
        'select_user' => 'Izberite novega lastnika',
        'success' => 'Lastništvo uspešno preneseno.',
    ],

    'relation_manager' => [
        'members' => 'Člani ekipe',
    ],
];
