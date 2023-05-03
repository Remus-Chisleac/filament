<?php

return [

    'title' => 'Passwort zurücksetzen',

    'heading' => 'Passwort zurücksetzen',

    'buttons' => [

        'reset' => [
            'label' => 'Passwort zurücksetzen',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-Mail-Adresse',
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
        'throttled' => 'Zu viele Versuche. Versuchen Sie es bitte in :seconds Sekunden nochmal.',
    ],

];
