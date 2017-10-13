<?php

$params = require(__DIR__ . '/params.php');

return [
    'layout' => false,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        // VERY IMPORTANT
        'user' => [
            'identityClass' => 'common\models\TmpUser', // User must implement the IdentityInterface
            'enableSession' => false,
            'loginUrl' => null,
        ],
        /**
         * Strepz custom components
         */
        'config' => [
            'class' => 'api\modules\v1\account\components\StrepzConfigManager',
        ],
        'strepzDbManager' => [
            'class' => 'common\components\StrepzDbManager',
        ],
        'restTemplate' => [
            'class' => 'api\modules\v1\account\components\RestResponseTemplate',
        ],
        'request' => [
            'enableCsrfValidation' => true,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'authManager' => [ // yii migrate --migrationPath=@app/migrations/migrations_rbac --db=adm_db_01
            'class' => 'common\components\rbac\DbManager',
            'defaultRoles' => ['guest'],
            // This "db" property is just a placeholder. Doesn't suppose to do any shit really
            'db' => 'glb_sys_db_01',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => '812656492191-h7ad5i2q21dtadedh1vbnh6mm0qh09fl.apps.googleusercontent.com',
                    'clientSecret' => 'gbk4Xyn2UrurP6eCYiR2ayqr',
                ],
            ],
        ],
        'strepzCorsFilter' => [
            'class' => '\yii\filters\Cors',
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'see/<module>/<controller>/<action>' => '<module>/<controller>/<action>',
                [
                    'class' => 'api\modules\v1\account\rest\AuthUrlRule',
                    'pluralize' => true,
                    'controller' => [
                        'v1/auth' => 'accounts/auth',
                    ],
                    'except' => ['delete'],
                ],
                [
                    'class' => 'api\modules\v1\account\rest\DefaultUrlRule',
                    'pluralize' => true,
                    'controller' => [
                        'v1/accounts' => 'accounts/default',
                    ],
                    'except' => ['delete'],
                ],
                [
                    'class' => 'api\modules\v1\account\rest\UserUrlRule',
                    'pluralize' => true,
                    'controller' => [
                        'v1/users' => 'accounts/user',
                    ],
                    'except' => ['delete'],
                ],
            ],
        ],
    ],
    'params' => $params,
];