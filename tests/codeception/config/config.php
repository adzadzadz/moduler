<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Application configuration shared by all applications and test types
 */
return [
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/common/fixtures/data',
            'templatePath' => '@tests/codeception/common/templates/fixtures',
            'namespace' => 'tests\codeception\common\fixtures',
        ],
    ],
    'components' => [
        // 'db' => [
        //     'dsn' => 'mysql:host=localhost;dbname=yii2_advanced_tests',
        // ],
        "strepz_test_fnc_db_01" => [ 
            "class" => "yii\db\Connection",
            "dsn" => "mysql:host=localhost;dbname=strepz_test_fnc_db_01", 
            "username" => "root", 
            "password" => "", 
            "tablePrefix" => "",
            "charset" => "utf8", 
            "attributes" => [] 
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
