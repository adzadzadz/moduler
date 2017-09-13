<?php

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        // Middleware db
        'miw_db_01' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=strepz_backend_miw_db_01',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'miw_',
            'charset' => 'utf8',
        ],
    ],
];