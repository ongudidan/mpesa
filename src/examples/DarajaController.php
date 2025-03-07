<?php

namespace app\controllers;

use app\models\Daraja;
use app\models\DarajaSearch;
use Mpesa\Mpesa;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\httpclient\Client;

/**
 * DarajaController implements the CRUD actions for Daraja model.
 */
class DarajaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Daraja models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DarajaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Initiates STK Push payment
     * @return mixed
     */
    public function actionInitiate()
    {
        $BusinessShortCode = $_SERVER['MPESA_BUSINESS_SHORTCODE'];
        $LipaNaMpesaPasskey = $_SERVER['LIPA_NA_MPESA_PASSKEY'];
        $TransactionType = 'CustomerPayBillOnline';
        $Amount = 1;
        $PartyA = '254768540720';
        $PartyB = '174379';
        $PhoneNumber = '254768540720';
        $CallBackURL = 'https://webhook.site/callback';
        $AccountReference = '2255';
        $TransactionDesc = 'Test Payment';
        $Remarks = 'Test Payment';

        $mpesa = new Mpesa();

        $stkPushSimulation = $mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);

        // Decode response as an object (default behavior of json_decode)
        $stk_response = json_decode($stkPushSimulation);

        if (empty($stk_response->MerchantRequestID) || empty($stk_response->CheckoutRequestID)) {
            \Yii::error('Invalid STK response: ' . json_encode($stk_response));
            throw new \Exception($stk_response->errorMessage ?? 'Invalid STK push response');
        } else {
            $model = new Daraja();
            $model->MerchantRequestID = $stk_response->MerchantRequestID;
            $model->CheckoutRequestID = $stk_response->CheckoutRequestID;
            $model->ResponseCode = $stk_response->ResponseCode;
            $model->ResponseDescription = $stk_response->ResponseDescription;
            $model->CustomerMessage = $stk_response->CustomerMessage;
            $model->amount = $Amount; // Ensure this is defined
            $model->phone_number = $PartyA; // Ensure this is defined
            $model->status = 'PENDING';
            $model->created_at = time();
            $model->updated_at = time();

            if (!$model->save()) {
                \Yii::error('Payment save error: ' . json_encode($model->getErrors()));
                throw new \Exception('Failed to save payment details');
            } else {
                echo "Payment STK initiated successfully";
            }
        }
    }



    public function actionStatus()
    {
        $checkoutRequestID = 'ws_CO_07032025173300577768540720';
        $businessShortCode = $_SERVER['MPESA_BUSINESS_SHORTCODE'];
        $BusinessShortCode = $_SERVER['MPESA_BUSINESS_SHORTCODE'];
        $LipaNaMpesaPasskey = $_SERVER['LIPA_NA_MPESA_PASSKEY'];


        $mpesa = new Mpesa();

        $STKPushRequestStatus = $mpesa->STKPushQuery($checkoutRequestID, $businessShortCode, $BusinessShortCode, $LipaNaMpesaPasskey);

        echo $STKPushRequestStatus;
    }


    /**
     * Displays a single Daraja model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Daraja model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Daraja();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Daraja model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Daraja model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Daraja model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Daraja the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Daraja::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
