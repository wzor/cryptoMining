<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Payouts */

$this->title = 'Create Payouts';
$this->params['breadcrumbs'][] = ['label' => 'Payouts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payouts-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>