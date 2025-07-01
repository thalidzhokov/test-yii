<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * Форма валидации данных webhook
 */
class WebhookForm extends Model
{
    public $external_message_id;
    public $external_client_id;
    public $client_phone;
    public $message_text;
    public $send_at;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Обязательные поля
            [['external_message_id', 'external_client_id', 'client_phone', 'message_text', 'send_at'], 'required'],
            
            // Формат MD5 хешей
            [['external_message_id', 'external_client_id'], 'match', 
                'pattern' => '/^[a-f0-9]{32}$/i',
                'message' => 'Неверный формат {attribute}'
            ],
            
            // Формат телефона
            ['client_phone', 'match', 
                'pattern' => '/^\+7\d{10}$/',
                'message' => 'Неверный формат номера телефона'
            ],
            
            // Длина текста
            ['message_text', 'string', 'max' => 4096],
            
            // Время как число
            ['send_at', 'integer', 'min' => 1],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'external_message_id' => 'ID сообщения',
            'external_client_id' => 'ID клиента',
            'client_phone' => 'Телефон',
            'message_text' => 'Текст сообщения',
            'send_at' => 'Время отправки',
        ];
    }
} 