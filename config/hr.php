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
    /*
    |--------------------------------------------------------------------------
    | Photocopie — Natures en conflit
    |--------------------------------------------------------------------------
    | Pour un même groupe + date, une seule demande parmi ces natures est
    | autorisée (Examen / Contrôle ne peuvent pas être planifiés deux fois
    | le même jour pour le même groupe).
    */
    'photocopie_conflicting_natures' => [
        'Examen',
        'Contrôle continu',
        "Contrôle d'essai",
    ],

    'rencontre_direction_excluded_professions' => [
        'Enseignante',
        'Enseignant',
        'Femme de ménage',
        'Chauffeur',
        'Gardien',
        'Assistante de transport',
    ],

];
