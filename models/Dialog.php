<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель диалога мессенджера
 *
 * @property int $id ID диалога
 * @property int $client_id ID клиента
 * @property string|null $last_message_at Время последнего сообщения
 * @property int $messages_count Количество сообщений
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 *
 * @property Client $client Клиент диалога
 */
class Dialog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialogs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id'], 'required'],
            [['client_id', 'messages_count'], 'integer'],
            [['messages_count'], 'default', 'value' => 0],
        ];
    }

    /**
     * Получить клиента диалога
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            // TODO: кешируем связь client_id->dialog_id
        }
    }
    
    /**
     * Получить статистику диалога
     */
    public function getStats()
    {
        // TODO: кешируем статистику диалога
        
        return [
            'messages_count' => $this->messages_count,
            'last_message_at' => $this->last_message_at,
        ];
    }
} 