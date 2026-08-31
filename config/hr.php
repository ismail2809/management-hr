<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demande Photocopie
    |--------------------------------------------------------------------------
    | Nombre de jours minimum avant la date souhaitée pour déposer une demande
    | de photocopie. Configurable via PHOTOCOPIE_DELAY_DAYS dans le .env.
    */
    'photocopie_delay_days' => (int) env('PHOTOCOPIE_DELAY_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Rencontre Direction — Professions exclues
    |--------------------------------------------------------------------------
    | Liste des noms de profession à exclure du sélecteur d'employés pour
    | une demande de type "Rencontre direction".
    | Pour en ajouter, ajoutez simplement un élément à ce tableau.
    */
    'rencontre_direction_excluded_professions' => [
        'Enseignante',
        'Enseignant',
        'Femme de ménage',
        'Chauffeur',
        'Gardien',
        'Assistante de transport',
    ],

];
