<?php

/* @var $model app\models\subtitlesManager\SubtitleForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'subtitles manager';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-subtitles">
    <h2><?=Html::encode($this->title)?></h2>

    <p>Upload both subtitles</p>

    <?php $form = ActiveForm::begin([
        'id' => 'subsSearch',
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'firstSubtitleFile')->input('file') ?>

    <?= $form->field($model, 'secondSubtitleFile')->input('file') ?>


    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Merge', ['class' => 'btn btn-primary', 'name' => 'merge-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
