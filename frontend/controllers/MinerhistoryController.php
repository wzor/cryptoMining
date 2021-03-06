<?php

namespace frontend\controllers;

use yii\helpers\ArrayHelper;
use Yii;
use common\models\Minerhistory;
use common\models\MinerhistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MinerhistoryController implements the CRUD actions for Minerhistory model.
 */
class MinerhistoryController extends Controller
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

    /**
     * Lists all Minerhistory models.
     * @return mixed
     */
    public function actionIndex()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-zcash.flypool.org/miner/t1MZ9MUkTBQ57x8Rx6AmED9gHD9tqFwHrTp/history",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        $response = ArrayHelper::toArray($response);
        $array = ArrayHelper::getValue($response,'data');

        foreach ($array as $history){
            $time = ArrayHelper::getValue($history,'time');
            $time = date('Y-m-d H:i:s', $time);
            $find = Minerhistory::find()->where(['time' => $time])->exists();

            if ($find == null){
                $model = new Minerhistory();

                $model->time = $time;
                $hashRate = ArrayHelper::getValue($history,'currentHashrate');
                $model->current_hashrate = (int)$hashRate;
                $model->valid_shares = ArrayHelper::getValue($history,'validShares');
                $model->invalid_shares = ArrayHelper::getValue($history,'invalidShares');
                $model->stale_shares = ArrayHelper::getValue($history,'staleShares');
                $averageHashrate = ArrayHelper::getValue($history,'averageHashrate');
                $model->average_hashrate = (int)$averageHashrate;
                $model->active_workers = ArrayHelper::getValue($history,'activeWorkers');
                $model->save();

            }
        }

        $searchModel = new MinerhistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Minerhistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Minerhistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Minerhistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Minerhistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Minerhistory model.
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
     * Finds the Minerhistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Minerhistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Minerhistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
