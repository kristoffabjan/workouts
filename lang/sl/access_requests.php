<?php

return [
    'navigation_label' => 'Zahteve za dostop',
    'model_label' => 'Zahteva za dostop',
    'plural_model_label' => 'Zahteve za dostop',

    'fields' => [
        'name' => 'Ime',
        'email' => 'E-pošta',
        'message' => 'Sporočilo',
        'status' => 'Status',
        'processed_by' => 'Obdelal',
        'processed_at' => 'Obdelano',
        'created_at' => 'Zahtevano',
    ],

    'actions' => [
        'approve' => 'Odobri',
        'reject' => 'Zavrni',
        'approve_heading' => 'Odobri zahtevo za dostop',
        'approve_description' => 'Ali ste prepričani, da želite odobriti to zahtevo za dostop?',
        'reject_heading' => 'Zavrni zahtevo za dostop',
        'reject_description' => 'Ali ste prepričani, da želite zavrniti to zahtevo za dostop?',
    ],

    'messages' => [
        'approved' => 'Zahteva za dostop je bila odobrena.',
        'rejected' => 'Zahteva za dostop je bila zavrnjena.',
        'invitation_sent' => 'Uporabniku je bilo poslano e-poštno povabilo.',
    ],
];
