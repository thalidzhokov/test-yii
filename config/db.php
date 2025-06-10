<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=' . (getenv('DB_HOST') ?: 'postgres') . ';dbname=' . (getenv('DB_NAME') ?: 'messenger_db'),
    'username' => getenv('DB_USER') ?: 'messenger_user',
    'password' => getenv('DB_PASSWORD') ?: 'messenger_pass',
]; 