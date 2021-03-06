<?php

return [
    'provider' => [
        'driver' => 'eloquent',
        'model'  => Soda\Cms\Database\Models\User::class,
    ],
    'guard'    => [
        'driver'   => 'session',
        'provider' => 'soda',
    ],
    'password' => [
        'provider' => 'soda',
        'email'    => 'auth.emails.password',
        'table'    => 'password_resets',
        'expire'   => 60,
    ],
];
