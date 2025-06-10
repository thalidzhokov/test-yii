<?php

namespace app\services;

use Yii;

/**
 * TODO: cервис для работы с кешем и очередями, 
 * можно добавить общую абстракцию для работы с кешем и вебхуками, на случай отработки без кешей, проверки на дубликаты и т.д.
 */
class RedisService
{
    private $redis;
    
    public function __construct()
    {
        $this->redis = Yii::$app->redis;
    }
    
    public function isDuplicateMessage($externalMessageId): bool
    {
        return false;
    }

    // TODO: другие методы    
}