<?php
namespace app\controllers;

use \Yii;
use app\models\Redirect;
use app\models\RedirectLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RedirectController extends Controller
{
    public function actionRedirect($code)
    {
        /**
         * @var Redirect $redirect
         */
        if (!$redirect = Redirect::findOne(['code' => $code])) {
            throw new NotFoundHttpException('Страница не найдена.');
        } else {
            $redirect->updateCounters(['counter' => 1]);
            $log=new RedirectLog();
            $log->redirect_id=$redirect->id;
            $log->ip=Yii::$app->request->getUserIP();
            $log->user_agent=Yii::$app->request->getUserAgent();
            $log->headers=json_encode(Yii::$app->request->getHeaders()->toArray());
            $log->save();

            return $this->redirect($redirect->url);
        }
    }
}