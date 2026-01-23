<?php

return [
    'resource' => [
        'label' => 'Trening',
        'plural_label' => 'Treningi',
        'navigation_label' => 'Treningi',
    ],

    'fields' => [
        'title' => 'Naslov',
        'content' => 'Vsebina',
        'status' => 'Status',
        'scheduled_at' => 'Načrtovan ob',
        'created_by' => 'Ustvaril',
        'assigned_users' => 'Dodeljeno',
        'exercises_count' => 'Vaje',
    ],

    'sections' => [
        'training_details' => 'Podrobnosti treninga',
        'content' => 'Vsebina',
        'your_completion' => 'Vaš zaključek',
    ],

    'filters' => [
        'status' => 'Status',
        'date_range' => 'Časovno obdobje',
        'from' => 'Od',
        'until' => 'Do',
        'assigned_to' => 'Dodeljeno',
    ],

    'actions' => [
        'schedule' => 'Načrtuj',
        'mark_complete' => 'Označi trening kot zaključen',
        'edit_feedback' => 'Uredi povratno informacijo',
    ],

    'schedule' => [
        'title' => 'Načrtuj trening',
        'single_date' => 'En datum',
        'multiple_dates' => 'Več datumov',
        'weekly_pattern' => 'Tedenski vzorec',
        'schedule_date_time' => 'Datum in čas',
        'start_date_time' => 'Začetni datum in čas',
        'number_of_weeks' => 'Število tednov',
        'days_of_week' => 'Dnevi v tednu',
        'options' => 'Možnosti',
        'copy_exercises' => 'Kopiraj vaje',
        'copy_exercises_description' => 'Vključi vse vaje z opombami in vrstnim redom',
        'assign_to' => 'Dodeli',
        'assign_to_placeholder' => 'Pusti prazno za ohranitev obstoječih dodelitev',
    ],

    'days' => [
        'sunday' => 'Nedelja',
        'monday' => 'Ponedeljek',
        'tuesday' => 'Torek',
        'wednesday' => 'Sreda',
        'thursday' => 'Četrtek',
        'friday' => 'Petek',
        'saturday' => 'Sobota',
    ],

    'notifications' => [
        'scheduled' => 'Trening načrtovan',
        'created_count' => 'Ustvarjenih :count treningov',
        'marked_complete' => 'Trening označen kot zaključen',
        'feedback_updated' => 'Povratna informacija posodobljena',
    ],

    'completion' => [
        'confirm_message' => 'Potrdite, da ste zaključili ta trening. Po želji lahko dodate povratno informacijo za trenerja.',
        'edit_feedback_message' => 'Posodobite povratno informacijo za ta zaključen trening.',
        'feedback_label' => 'Povratna informacija',
        'feedback_placeholder' => 'Kako je bil trening? Kakšne opombe za trenerja?',
        'completed_at' => 'Zaključeno ob',
        'not_completed' => 'Še ni zaključeno',
        'no_feedback' => 'Brez povratne informacije',
    ],

    'validation' => [
        'scheduled_date_required' => 'Datum je obvezen, ko je status nastavljen na Načrtovano.',
        'scheduled_date_in_past' => 'Datum ne more biti v preteklosti.',
        'no_date_selected' => 'Prosimo, izberite vsaj en datum za načrtovanje.',
        'feedback_deadline_passed' => 'Rok za oddajo povratne informacije je potekel. Ne morete več oddati povratne informacije za ta trening.',
        'user_not_in_team' => 'Eden ali več izbranih uporabnikov ni član te ekipe.',
        'cannot_edit_past_training' => 'Ne morete urejati treninga, ki je bil že načrtovan v preteklosti.',
        'cannot_assign_past_training' => 'Ne morete dodeljevati uporabnikov treningu, ki je bil že načrtovan v preteklosti.',
    ],
];
