<?php

return [

    'single' => [

        'label' => 'Sil',

        'modal' => [

            'heading' => ':label Sil',

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçilənləri sil',

        'modal' => [

            'heading' => 'Seçilənləri sil', // When ':label' is used here, the meaning is distorted.

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

        ],

    ],

];
