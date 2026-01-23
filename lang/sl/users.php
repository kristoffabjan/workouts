<?php

return [
    'resource' => [
        'label' => 'Član ekipe',
        'plural_label' => 'Člani ekipe',
        'navigation_label' => 'Člani ekipe',
    ],

    'fields' => [
        'name' => 'Ime',
        'email' => 'E-pošta',
        'role' => 'Vloga',
        'role_in_team' => 'Vloga v ekipi',
        'team_owner' => 'Lastnik ekipe',
        'created_at' => 'Pridružen',
    ],

    'filters' => [
        'role' => 'Vloga',
    ],

    'actions' => [
        'invite' => 'Povabi uporabnika',
        'invite_to_team' => 'Povabi uporabnika v ekipo',
        'remove' => 'Odstrani',
        'remove_from_team' => 'Odstrani iz ekipe',
    ],

    'invitation' => [
        'title' => 'Povabi uporabnika',
        'description' => 'Uporabnik bo prejel e-povabilo in zanj bo ustvarjena osebna ekipa.',
        'team_description' => 'Poslano bo e-povabilo. Obstoječi uporabniki se lahko pridružijo z enim klikom.',
        'email_label' => 'E-poštni naslov',
        'role_label' => 'Vloga',
        'success' => 'Povabilo poslano',
        'success_message' => 'Povabilo je bilo poslano na **:email**.',
    ],

    'remove' => [
        'confirm' => 'Ali ste prepričani, da želite odstraniti :name iz te ekipe?',
        'success' => 'Uporabnik odstranjen',
        'success_message' => ':name je bil odstranjen iz ekipe.',
    ],

    'relation_manager' => [
        'assigned_trainings' => 'Dodeljeni treningi',
    ],

    'validation' => [
        'cannot_delete_with_content' => 'Tega uporabnika ni mogoče izbrisati, ker je ustvaril treninge ali vaje. Najprej prenesite lastništvo.',
        'cannot_delete_self' => 'Ne morete izbrisati svojega računa.',
    ],
];
