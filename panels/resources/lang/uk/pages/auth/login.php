<?php

return [

    'title' => 'Логін',

    'heading' => 'Увійдіть у свій акаунт',

    'form' => [

        'email' => [
            'label' => 'Електронна пошта',
        ],

        'password' => [
            'label' => 'Пароль',
        ],

        'remember' => [
            'label' => 'Запам’ятай мене',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Увійти',
            ],

        ],

    ],

    'notifications' => [
        'failed' => 'Ці дані не відповідають нашим записам.',
        'throttled' => 'Забагато спроб входу в систему. Будь ласка, спробуйте ще раз через :seconds секунд.',
    ],

];
