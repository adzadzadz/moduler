<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../../common/config/strepz/main.php'),
    require(__DIR__ . '/../modules/v1/account/config/main.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);

// Strepz stuff!!! BACK OFF NERDS!!!
$application->strepzConfig->loadAllConfig();

if (!$application->user->isGuest && !$application->strepzConfig->isTempUser) {
    $application->authManager->ruleTable = '{{%' . $application->strepzConfig->company_id . '_auth_rule}}';
    $application->authManager->itemTable = '{{%' . $application->strepzConfig->company_id . '_auth_item}}';
    $application->authManager->itemChildTable = '{{%' . $application->strepzConfig->company_id . '_auth_item_child}}';
    $application->authManager->assignmentTable = '{{%' . $application->strepzConfig->company_id . '_auth_assignment}}';
    $application->authManager->db = $application->strepzDbManager->getFncDb();
}

$application->run();