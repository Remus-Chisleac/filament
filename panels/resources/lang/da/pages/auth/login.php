<?php

return [

    'title' => 'Log ind',

    'heading' => 'Log ind på din konto',

    'actions' => [

        'authenticate' => [
            'label' => 'Log ind',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Adgangskode',
        ],

        'remember' => [
            'label' => 'Husk mig',
        ],

    ],

    'messages' => [
        'failed' => 'Den adgangskode, du har indtastet, er forkert.',
        'throttled' => 'For mange loginforsøg. Prøv venligst igen om :seconds sekunder.',
    ],

];
