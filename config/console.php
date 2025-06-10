<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'messenger-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => getenv('REDIS_HOST') ?: 'redis',
                'port' => 6379,
                'database' => 0,
            ]
        ],
        'db' => $db,
        'redis' => $redis,
    ],
    
    
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@app/migrations',
        ],
        // TODO: воркеры для обработки очередей
        // 'webhook-worker' => 'app\commands\WebhookWorkerController',
    ],
];

return $config; 