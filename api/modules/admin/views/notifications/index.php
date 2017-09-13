<?php 

use yii\helpers\Html;
use kartik\datetime\DateTimePicker;

$scopeOptions = [
  'global' => 'Global',
  'company' => 'Company',
  'group' => 'Group',
  'user' => 'User'
];

$typeOptions = [
  '---',
  '---',
  '---'
];

?>

<div class="container">
  <div class="col-sm-12">
    <h3>Create Notification</h3>
    <?= Html::beginForm(['/admin/notifications/create'], 'post', ['enctype' => 'multipart/form-data']) ?>
      <div class="row">
        <div class="col-sm-6">
          <h4>Basic Information</h4>
          <div class="form-group">
            Executed by: John Doe (Admin)
          </div>
          <div class="form-group">
            <?= Html::input('input', 'Notifications[subject]', '', ['class' => 'form-control', 'placeholder' => 'Notification title']); ?>
          </div>
          <div class="form-group">
            <?= Html::textarea('Notifications[text]', '', ['class' => 'form-control', 'placeholder' => 'Notification content', 'rows' => 10]); ?>
          </div>
        </div>
        <div class="col-sm-6">
          <h4>Rules</h4>
          <div class="form-group">
            <label for="type">Type</label>
            <?= Html::dropDownList('Notifications[type]', $selection = null, $typeOptions, ['class' => 'form-control']); ?>
          </div>
          <div class="form-group">
            <label for="scope">Scope</label>
            <?= Html::dropDownList('Notifications[scope]', $selection = null, $scopeOptions, ['class' => 'form-control']); ?>
          </div>
          <div class="form-group">
            <label for="schedule">Publish Schedule</label>
            <?= DateTimePicker::widget([
                'name' => 'schedule',
                'options' => ['placeholder' => 'Select operating time ...'],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'mm/dd/yyyy hh:ii:ss',
                    'startDate' => new DateTime(),
                    'todayHighlight' => true
                ]
            ]) ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            <?= Html::submitButton("Submit", ['class' => 'btn btn-primary', 'style' => 'margin-top: 15px;']); ?>
          </div>
        </div>
      </div>
    <?= Html::endForm() ?>
  </div>
</div>