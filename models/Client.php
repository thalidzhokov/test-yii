<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель клиента мессенджера
 *
 * @property int $id ID клиента
 * @property string $external_client_id Внешний ID клиента
 * @property string $client_phone Номер телефона клиента
 * @property string $created_at Дата создания
 * @property string $updated_at Дата обновления
 *
 * @property Dialog $dialog Диалог клиента
 */
class Client extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%clients}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_client_id', 'client_phone'], 'required'],
            [['external_client_id'], 'string', 'length' => 32],
            [['external_client_id'], 'unique'],
            /*
            TODO: возможно, стоит рассмотреть применение числа для client_phone: 
            будут нормальные выборки по диапазонам
            ?использование памяти будет меньше
            ?индексы будут поменьше
            ?партиционирование будет более понятным
            */
            [['client_phone'], 'string', 'length' => 12],
            [['client_phone'], 'match', 'pattern' => '/^\+7\d{10}$/'],
        ];
    }

    /**
     * Получить диалог клиента
     */
    public function getDialog()
    {
        return $this->hasOne(Dialog::class, ['client_id' => 'id']);
    }

    /**
     * Найти или создать клиента
     */
    public static function findOrCreate($externalClientId, $clientPhone)
    {
        // TODO: проверяем redis hash, если есть, то возвращаем
        
        $client = static::findOne(['external_client_id' => $externalClientId]);
        
        if (!$client) {
            $client = new static();
            $client->external_client_id = $externalClientId;
            $client->client_phone = $clientPhone;
            $client->save();
            
            // TODO: добавляем нового клиента в redis hash
        }
        
        return $client;
    }

    /**
     * Получить или создать диалог для клиента
     */
    public function getOrCreateDialog()
    {
        // TODO: кешируем связи client_id->dialog_id
        
        if (!$this->dialog) {
            $dialog = new Dialog();
            $dialog->client_id = $this->id;
            $dialog->save();
            
            $this->populateRelation('dialog', $dialog);
            
            // TODO: добавляем связь в redis
        }
        
        return $this->dialog;
    }
}