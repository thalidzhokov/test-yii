<?php

$db = require __DIR__ . '/db.php';
$redis = require __DIR__ . '/redis.php';

$config = [
    'id' => 'messenger-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => $db,
        'redis' => $redis,
        // Или
        // 'cache' => [
        //     'class' => 'yii\redis\Cache',
        //     'redis' => $redis,
        // ],
        'mutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
            'db' => 'db',
        ],
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