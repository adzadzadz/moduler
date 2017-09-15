<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'placeholder' => 'Username',
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'placeholder' => 'Pasword',
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<style>
    .wrap {
        max-width: 500px;
        margin: 150px auto 0;
    }
</style>

<div class="wrap">
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <?= Html::beginForm(['auth/login'], 'post', ['enctype' => 'multipart/form-data']) ?>
        
        <?= Html::input('text', 'username', null, $fieldOptions1) ?>

        <?= Html::input('password', 'password', null, $fieldOptions2) ?>

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?= Html::endForm() ?>
    </div>
</div>