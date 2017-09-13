<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

// $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
$resetLink = Yii::$app->params['appUrl'] . "/reset-password?t=$user->password_reset_token&e=$user->email";
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>