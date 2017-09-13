<?php
return [
    'components' => [
        'db_rep' => [
            'class' => 'common\components\DbDataReplicator',
            'glbUser_dbs' => [
                // 'glb_sys_db_02' => [
                //     'class' => 'yii\db\Connection',
                //     'dsn' => 'mysql:host=173.194.228.43;dbname=glb_sys_db_02',
                //     'username' => 'eu_usr_01',
                //     'password' => '10_rsu_ue',
                //     'tablePrefix' => 'glb_',
                // ],
                // 'glb_sys_db_03' => [
                //     'class' => 'yii\db\Connection',
                //     'dsn' => 'mysql:host=173.194.228.43;dbname=glb_sys_db_03',
                //     'username' => 'eu_usr_01',
                //     'password' => '10_rsu_ue',
                //     'tablePrefix' => 'glb_',
                //     'charset' => 'utf8',
                // ],
            ],
        ],

        // Monitoring db
        'mon_db_01' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:unix_socket=/cloudsql/tactile-pulsar-88009:europe-west1-d:eu-sql-adm-01;dbname=mon_db_01',
            'username' => 'root',
            'password' => 'CoDzLvoNb6HSEgf5RqMSY9Q_nlaB5R',
            'tablePrefix' => 'mon_',
            'charset' => 'utf8',
        ],

        // 
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@frontend/mail',
            // 'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'eu-vm-mail-01.c.tactile-pulsar-88009.internal',
                'port' => '25',
                //'encryption' => 'tls',
            ],
        ],
    ],
];