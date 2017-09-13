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
            'dsn' => 'mysql:host=localhost;dbname=strepz_monitoring',
            'username' => 'root',
            'password' => '',
            'tablePrefix' => 'mon_',
            'charset' => 'utf8',
        ],
        
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];