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
	        'dsn' => 'mysql:unix_socket=/cloudsql/tactile-pulsar-88009:europe-west1-d:eu-sql-adm-01;dbname=miw_db_01',
	        'username' => 'root',
	        'password' => 'CoDzLvoNb6HSEgf5RqMSY9Q_nlaB5R',
	        'tablePrefix' => 'miw_',
	        'charset' => 'utf8',
	    ],
    ],
];