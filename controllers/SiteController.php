<?php

namespace app\controllers;

use app\models\subtitlesManager\SubtitleForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\HttpException;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {

        //test
        function getWidthHeight($font, $fontSize, $text) {
            //fontsize 26points equals 35 px
            $bbox = imagettfbbox($fontSize, 0, $font, $text);

            //calculate x baseline
            if($bbox[0] >= -1) {
                $bbox['x'] = abs($bbox[0] + 1) * -1;
            } else {
                //$bbox['x'] = 0;
                $bbox['x'] = abs($bbox[0] + 2);
            }

            //calculate actual text width
            $bbox['width'] = abs($bbox[2] - $bbox[0]);
            if($bbox[0] < -1) {
                $bbox['width'] = abs($bbox[2]) + abs($bbox[0]) - 1;
            }

            //calculate y baseline
            $bbox['y'] = abs($bbox[5] + 1);

            //calculate actual text height
            $bbox['height'] = abs($bbox[7]) - abs($bbox[1]);
            if($bbox[3] > 0) {
                $bbox['height'] = abs($bbox[7] - $bbox[1]) - 1;
            }
            echo $bbox['width'];
            echo '<br>';
            echo $bbox['height'];
        }


        getWidthHeight('c:\windows\fonts\verdana.ttf', 35, '♪ I\'m goin\' down to South');






        //return $this->render('about');
    }
    
    public function actionSubtitles() {
       $model = new SubtitleForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->firstSubtitleFile = UploadedFile::getInstance($model, 'firstSubtitleFile');
            $model->secondSubtitleFile = UploadedFile::getInstance($model, 'secondSubtitleFile');
            if ($model->validate()) {
                $model->mergeSubtitles();
                $model->sendSubs();
            }
        } else {
            return $this->render('subtitlesForm', [
                'model' => $model
            ]);
        }
    }
}
