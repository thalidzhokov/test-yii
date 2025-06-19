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
     * @var WebhookService Сервис для обработки webhook-запросов
     */
    private $webhookService;

    /**
     * Конструктор контроллера
     * 
     * @param string $id ID контроллера
     * @param \yii\base\Module $module Модуль
     * @param WebhookService $webhookService Сервис обработки webhook
     * @param array $config Конфигурация
     */
    public function __construct($id, $module, WebhookService $webhookService, $config = [])
    {
        $this->webhookService = $webhookService;
        parent::__construct($id, $module, $config);
    }

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
            
            // Используем внедренный сервис вместо создания через new
            return $this->webhookService->processWebhook($data);
        } catch (\Exception $e) {
            // TODO: логируем
            
            Yii::$app->response->statusCode = 500;
            return ['status' => 'error', 'message' => 'Internal server error'];
        }
    }
} 