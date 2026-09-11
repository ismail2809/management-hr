<?php

return [

    'title' => 'Réinitialiser votre mot de passe',

    'heading' => 'Mot de passe oublié ?',

    'actions' => [

        'login' => [
            'label' => 'retour à la connexion',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresse Email',
        ],

        'actions' => [

            'request' => [
                'label' => "Envoyer l'email",
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'title' => 'Lien de réinitialisation envoyé',
            'body' => 'Si un compte correspond à cette adresse email, vous recevrez un lien de réinitialisation dans quelques instants.',
        ],

        'throttled' => [
            'title' => 'Trop de requêtes',
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
