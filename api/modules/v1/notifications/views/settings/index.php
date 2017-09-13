<?php 

    use yii\helpers\Url;
    
?>

<var>
    <?php var_dump($result); ?>
</var>
<h3>Create Template</h3>

<form action="<?= Url::toRoute(['/page/notifier/settings/create'])?>" method="POST">
    <input type="text" name="Template[name]" placeholder="Name"><br>
    <textarea name="Template[content]" id="" cols="30" rows="10" placeholder="content"></textarea><br>
    <input type="submit" value="Submit">
</form>
<br><br>

<h3>Currently in use</h3>
<div>Template</div>

<h3>Select Template</h3>
<?php if (isset($templates)): ?>
    <?php foreach ($templates as $each): ?>
        <?= $each->name ?> <button>Select</button> <button>Update</button> <br>    
    <?php endforeach ?>    
<?php endif ?>
