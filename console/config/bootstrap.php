<?php
Yii::setAlias('root_path', dirname(dirname(__DIR__)));
Yii::setAlias('root_parent', dirname(dirname(dirname(__DIR__))));

// Development environment fixtures
if (YII_ENV_DEV) {
    
} else {

}

// Windows fixtures
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	define('CONNECTOR', 'host');
	Yii::setAlias('dsn_con', '');
    define('BEFORE_CMD', '');
} else {
	define('CONNECTOR', 'unix_socket');
	define('BEFORE_CMD', 'sudo ');
	Yii::setAlias('dsn_con', '/cloudsql/tactile-pulsar-88009:europe-west1-d:');
}