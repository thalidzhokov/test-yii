<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'messenger-api',
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-secret-key-here',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'db' => $db,
        //'redis' => $redis,
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
    ],
];

return $config; 