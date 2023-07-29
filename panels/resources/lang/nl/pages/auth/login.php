<?php

return [

    'title' => 'Inloggen',

    'heading' => 'Inloggen op je account',

    'actions' => [

        'register' => [
            'before' => 'of',
            'label' => 'maak een account aan',
        ],

        'request_password_reset' => [
            'label' => 'Wachtwoord vergeten?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Wachtwoord',
        ],

        'remember' => [
            'label' => 'Herinner mij',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Inloggen',
            ],

        ],

    ],

    'notifications' => [
        'failed' => 'Onjuiste inloggegevens.',
        'throttled' => 'Te veel inlogpogingen. Probeer opnieuw over :seconds seconden.',
    ],

];
