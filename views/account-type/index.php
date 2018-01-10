<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\utilities\DataHelper;
use yii\helpers\Url;
use app\models\AccountType;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AccountTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
	<?php
        $dh = new DataHelper();
						 $url=Url::to(['account-type/create']);
                       echo $dh->getModalButton(new AccountType, 'account-type/create', 'AccountType', 'btn btn-danger btn-create btn-new pull-right' , "New",$url);
               ?>
    </p>
	<?php Pjax::begin(['id'=>'pjax-account-type',]); ?> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'Description',
            'created_by',
            'modified_by',
            // 'created_on',
            // 'modified_on',

                                ['class' => 'yii\grid\ActionColumn',
                     'template' => '{view} {update}',
                     'buttons' => [
									'view' => function ($url, $model){
                                             $dh = new DataHelper();
                                             $url = Url::to(['account-type/view', 'id'=>$model->id]);
                                              $popup = $dh->getModalButton($model, "account-type/view", "AccountType", 'glyphicon glyphicon-eye-open','',$url);
                                              return $popup;
									},
											  
                                    'update' => function ($url, $model) {
                                            $dh = new DataHelper();
                                            $url = Url::to(['account-type/update','id'=>$model->id]);
                                           return $dh->getModalButton($model, "account-type/update", "AccountType", 'glyphicon glyphicon-edit','',$url);
                                            },
                            ], 
                    ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>
