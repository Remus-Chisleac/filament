<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    |
    | By enabling this setting, your notifications will be ready for Tailwind's
    | Dark Mode feature.
    |
    | https://tailwindcss.com/docs/dark-mode
    |
    */

    'dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | This is the configuration for the general layout of notifications.
    |
    | You may set a maximum horizontal position using the max width setting,
    | corresponding to Tailwind screens from `sm` to `2xl`.
    | Leave it empty for no maximum.
    |
    */

    'layout' => [

        'alignment' => [
            'horizontal' => 'right',
            'vertical' => 'top',
        ],

        'max_width' => null,

    ],

];
