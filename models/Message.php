<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель сообщения мессенджера
 *
 * @property int $id ID сообщения
 * @property string $external_message_id Внешний ID сообщения
 * @property int $dialog_id ID диалога
 * @property string $message_text Текст сообщения
 * @property string $send_at Время отправки сообщения
 * @property string $created_at Дата создания записи
 *
 * @property Dialog $dialog Диалог сообщения
 */
class Message extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%messages}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_message_id', 'dialog_id', 'message_text', 'send_at'], 'required'],
            [['dialog_id'], 'integer'],
            [['external_message_id'], 'string', 'length' => 32],
            [['external_message_id'], 'unique'],
            [['message_text'], 'string', 'max' => 4096],
        ];
    }

    /**
     * Получить диалог сообщения
     */
    public function getDialog()
    {
        return $this->hasOne(Dialog::class, ['id' => 'dialog_id']);
    }

    /**
     * Найти сообщение по внешнему ID
     */
    public static function findByExternalId($externalMessageId)
    {
        // TODO: проверяем redis set, если есть, то возвращаем
        
        return static::findOne(['external_message_id' => $externalMessageId]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($insert) {
            // TODO: отмечаем сообщение как обработанное 
            
            // TODO: инкрементируем счетчики
        }
    }
} 