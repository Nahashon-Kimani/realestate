<?php

namespace app\controllers;

use Yii;
use app\models\OccupancyRent;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\utilities\DataHelper;
use app\models\OccupancyRentSearch;
use yii\web\Response;

/**
 * OccupancyRentController implements the CRUD actions for OccupancyRent model.
 */
class OccupancyRentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionPrintreceipt($id){
        $model = $this->findModel($id);
        $query = OccupancyRent::find()->where(['id'=>$model->id]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('receipt',[
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    public function actionReceivepay($occupancy_id = ''){
        $model = new OccupancyRent();
        $dh = new DataHelper;
        $keyword = 'occupancy-rent';
        if($occupancy_id != ''){
            $model->fk_occupancy_id = $occupancy_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->receivePay()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            if (Yii::$app->request->isAjax)
            {
               Yii::$app->response->format = Response::FORMAT_JSON;
                
                return array(
                        'div'=>"Successfully Recieved!",
                    
                    );               
            }
            
        } else {
               
               return $dh->processResponse($this, $model, 'receivepay', 'danger', 'Please fix the below errors!', 'pjax-'.$keyword, $keyword.'-form-alert-0');
               exit; 
                     
         }
    }

    /**
     * Lists all OccupancyRent models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $searchModel = new OccupancyRentSearch();
      //  $dataProvider = $searchModel->search(Yii::$app->request->get());
        $dataProvider = new ActiveDataProvider(['query' => OccupancyRent::getSearchQuery($searchModel,15)]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
		
        
    }
    
    public function actionOccupancyBills($id)
    {
        $model = \app\models\Occupancy::findOne($id);
        $searchModel = new OccupancyRentSearch();
        $searchModel->fk_occupancy_id = $model->id;
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return \yii\helpers\Json::encode($this->renderAjax('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'occupancy' => $model
            ]));
    }

    /**
     * Displays a single OccupancyRent model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
       $data = $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ],false,false);
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return array(
                'div'=>$data,
                
            );
        }
        else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new OccupancyRent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($occupancy_id)
    {
        if(($occupancy = \app\models\Occupancy::findOne($occupancy_id)) !== null) {
            $model = new OccupancyRent();
            $model->fk_occupancy_id = $occupancy->id;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //Set Flash Message..
                return $this->redirect(Yii::$app->request->referrer);
            } elseif(\yii::$app->request->isAjax) {
                return $this->renderAjax('create',[
                    'model' => $model,
                ]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Invalid Occupancy Record');
        }
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dh = new DataHelper;
        $keyword = 'occupancy-rent';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            if (Yii::$app->request->isAjax)
            {   
                return $dh->processResponse($this, $model, 'update', 'success', 'Successfully Saved!', 'pjax-'.$keyword, $keyword.'-form-alert-'.$model->id);                
            }
        } else {
            if (Yii::$app->request->isAjax)
            {
                return $dh->processResponse($this, $model, 'update', 'danger', 'Please fix the below errors!', 'pjax-'.$keyword, $keyword.'-form-alert-'.$model->id);   
            }
            else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Deletes an existing OccupancyRent model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OccupancyRent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OccupancyRent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OccupancyRent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
