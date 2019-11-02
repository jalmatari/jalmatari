<?php

return [

    'permissions' => [
        'log_with_user' => 'إمكانية تسجيل الدخول كأي مستخدم في النظام',
    ],
    'api'         => [
        'allowable_tables' => [ "users", "sync" ]
    ],
    'undeletable_rows' => [
        'users'      => 1,
        'languages'  => 1,
        'groups'     => 1,
        'contents'   => 1,
        'categories' => 7
    ]
];
