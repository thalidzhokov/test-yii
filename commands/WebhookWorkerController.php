<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\services\RedisService;
use app\services\WebhookService;

/**
 * Консольная команда для обработки очереди webhook сообщений
 * Чтобы не делать бесконечные циклы делаем через супервизор или cron
 */
class WebhookWorkerController extends Controller
{
    private $redisService;
    private $webhookService;
    
    public function init()
    {
        parent::init();
        $this->redisService = new RedisService();
        $this->webhookService = new WebhookService();
    }
    
    /**
     * TODO: команда для запуска воркера 
     * php yii webhook-worker/process
     */
    public function actionProcess()
    {
        // TODO: реализовать
    }
} 