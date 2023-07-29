<?php

return [

    'title' => 'Prisijungti',

    'heading' => 'Prisijunkite prie savo paskyros',

    'form' => [

        'email' => [
            'label' => 'El. paštas',
        ],

        'password' => [
            'label' => 'Slaptažodis',
        ],

        'remember' => [
            'label' => 'Prisiminti mane',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prisijungti',
            ],

        ],

    ],

    'notifications' => [
        'failed' => 'Neteisingi prisijungimo duomenys.',
        'throttled' => 'Per daug bandymų prisijungti. Bandykite po :seconds sekundžių.',
    ],

];
