<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\services\WebhookService;

/**
 * API контроллер для обработки webhook-запросов мессенджера
 */
class ApiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Обработка webhook-запроса от внешнего мессенджера
     */
    public function actionWebhook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // TODO: делаем лимиты
        
        try {
            $data = Yii::$app->request->getBodyParams();
            
            // TODO:отправляем в очередь
            
            $service = new WebhookService();
            return $service->processWebhook($data);
        } catch (\Exception $e) {
            // TODO: логируем
            
            Yii::$app->response->statusCode = 500;
            return ['status' => 'error', 'message' => 'Internal server error'];
        }
    }
} 