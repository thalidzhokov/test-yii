<?php

$db = require __DIR__ . '/db.php';
$redis = require __DIR__ . '/redis.php';

$config = [
    'id' => 'messenger-api',
    'basePath' => dirname(__DIR__),
    'container' => [
        'definitions' => [
            // Регистрируем WebhookService для внедрения зависимостей
            'app\services\WebhookService' => 'app\services\WebhookService',
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'your-secret-key-here',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'db' => $db,
        'redis' => $redis,
        // Или
        // 'cache' => [
        //     'class' => 'yii\redis\Cache',
        //     'redis' => $redis,
        // ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/v1/webhook' => 'api/webhook',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'mutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
            'db' => 'db',
        ],
    ],
];

return $config; 