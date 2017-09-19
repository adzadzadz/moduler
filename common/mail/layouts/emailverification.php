<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>

<?= 'Your account is almost ready. Copy the code below to Strepz.com' ?>
<?= "\n\r\n" ?>
<?= $code ?>

<?= $content ?>
<?php $this->endBody() ?>
<?php $this->endPage() ?>