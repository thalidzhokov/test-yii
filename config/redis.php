<?php

return [
    'class' => 'yii\redis\Connection',
    'hostname' => getenv('REDIS_HOST') ?: 'redis',
    'port' => getenv('REDIS_PORT') ?: 6379,
    'database' => getenv('REDIS_DB') ?: 0,
    'password' => getenv('REDIS_PASSWORD') ?: null,
];
