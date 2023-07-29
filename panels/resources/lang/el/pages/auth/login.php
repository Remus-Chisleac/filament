<?php

return [

    'title' => 'Σύνδεση',

    'heading' => 'Συνδεθείτε στο λογαριασμό σας',

    'form' => [

        'email' => [
            'label' => 'Διεύθυνση ηλεκτρονικού ταχυδρομείου',
        ],

        'password' => [
            'label' => 'Κωδικός πρόσβασης',
        ],

        'remember' => [
            'label' => 'Θυμήσου με',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Συνδεθείτε',
            ],

        ],

    ],

    'notifications' => [
        'failed' => 'Τα στοιχέια που δώσατε δεν συνδέονται με λογαριασμό.',
        'throttled' => 'Πάρα πολλές προσπάθειες σύνδεσης. Δοκιμάστε ξανά μετά από :seconds δευτερόλεπτα.',
    ],

];
