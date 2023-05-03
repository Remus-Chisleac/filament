<?php

return [

    'title' => 'Registrierung',

    'heading' => 'Registrieren',

    'buttons' => [

        'login' => [
            'before' => 'oder',
            'label' => 'mit Konto anmelden',
        ],

        'register' => [
            'label' => 'Registrieren',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [
            'label' => 'Passwort',
            'validation_attribute' => 'password',
        ],

        'passwordConfirmation' => [
            'label' => 'Passwort bestätigen',
        ],

    ],

    'messages' => [
        'throttled' => 'Zu viele Anmeldeversuche. Versuchen Sie es bitte in :seconds Sekunden nochmal.',
    ],

];
