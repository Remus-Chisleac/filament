<?php

return [

    'title' => 'Logowanie',

    'heading' => 'Zaloguj się',

    'actions' => [

        'register' => [
            'before' => 'lub',
            'label' => 'zarejestruj się',
        ],

        'request_password_reset' => [
            'label' => 'Nie pamiętam hasła',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'password' => [
            'label' => 'Hasło',
        ],

        'remember' => [
            'label' => 'Zapamiętaj mnie',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Zaloguj się',
            ],

        ],

    ],

    'notifications' => [
        'failed' => 'Błędny login lub hasło.',
        'throttled' => 'Za dużo nieudanych prób logowania. Proszę spróbować za :seconds sekund.',
    ],

];
