<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php'),
    require(__DIR__ . '/../../common/config/strepz/params.php')
);

return [
    'layout' => false,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        // VERY IMPORTANT
        'user' => [
            // 'className' => 'yii\web\User',
            'identityClass' => 'common\models\TmpUser', // User must implement the IdentityInterface
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => true,          
        ],

        /**
         * Strepz custom components
         */
        'strepzConfig' => [
            'class' => 'api\components\StrepzConfigManager',
        ],
        'strepzDbManager' => [
            'class' => 'common\components\StrepzDbManager',
        ],
        'request' => [
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            // ...
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
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
                'see/<module:\w+>' => '<module>/default/index',
                'see/<module:\w+>/<controller>' => '<module>/<controller>/index',
                'see/<module:\w+>/<controller:\w+>/<action>' => '<module>/<controller>/<action>',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/users' => 'accounts/user'
                    ],
                    'except' => ['delete'],
                ],
            ],
        ],
    ],
    'modules' => [
        'accounts' => [
            'class' => 'api\modules\v1\account\Account',
        ],
        'admin' => [
            'class' => 'api\modules\admin\Admin'
        ]
    ],
    'params' => $params,
];