<?php

namespace app\services;

use Yii;
use app\models\Client;
use app\models\Dialog;
use app\models\Message;

/**
 * Сервис для обработки вебхук-запросов мессенджера
 */
class WebhookService
{
    /**
     * Обработать webhook-запрос
     * 
     * @param array $data Данные запроса
     * @return array Результат обработки
     */
    public function processWebhook(array $data): array
    {
        try {
            // TODO: проверяем дубликаты через redis set, тк фильтруем
            
            // Валидация
            $this->validateData($data);
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // TODO: кеш клиентов через redis hash, тк одни и те же клиенты отправляют много сообщений подряд
                $client = Client::findOrCreate(
                    $data['external_client_id'], 
                    $data['client_phone']
                );
                
                // TODO: кеш диалогов через redis, нужно думать над tll, или рассматривать другие возможности
                $dialog = $client->getOrCreateDialog();
                
                // Создать сообщение
                $message = new Message();
                $message->external_message_id = $data['external_message_id'];
                $message->dialog_id = $dialog->id;
                $message->message_text = $data['message_text'];
                $message->send_at = date('Y-m-d H:i:s', $data['send_at']);
                
                if (!$message->save()) {
                    // Проверяем ошибки валидации - если дубликат external_message_id
                    if (isset($message->errors['external_message_id'])) {
                        $transaction->rollBack();
                        
                        // TODO: делаем отметку в redis что сообщение дубликат
                        
                        return ['status' => 'success', 'message' => 'Duplicate ignored'];
                    }
                    
                    $transaction->rollBack();
                    throw new \Exception('Ошибка валидации: ' . json_encode($message->errors));
                }
                
                // TODO: делаем инкремент счетчиков в redis, нужна стата в реальном времени по количеству обработанных сообщений в час/день
                
                $transaction->commit();
                
                // TODO: отмечаем сообщение как успешно обработанное в redis,если придет повторный запрос, сразу ответим из кеша
                
                return ['status' => 'success', 'message_id' => $message->id];
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            // TODO: логируем, лучше через stdour/stderr в грейлог
            
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Валидация данных
     */
    private function validateData(array $data): void
    {
        $required = ['external_message_id', 'external_client_id', 'client_phone', 'message_text', 'send_at'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Поле {$field} обязательно");
            }
        }
        
        // TODO: стоит подумать над кешированием проверок регулярками, тк они дорогие и частые
        
        if (!preg_match('/^[a-f0-9]{32}$/i', $data['external_message_id'])) {
            throw new \Exception('Неверный формат external_message_id');
        }
        
        if (!preg_match('/^[a-f0-9]{32}$/i', $data['external_client_id'])) {
            throw new \Exception('Неверный формат external_client_id');
        }
        
        if (!preg_match('/^\+7\d{10}$/', $data['client_phone'])) {
            throw new \Exception('Неверный формат номера телефона');
        }
        
        if (mb_strlen($data['message_text']) > 4096) {
            throw new \Exception('Текст сообщения слишком длинный');
        }
        
        if (!is_numeric($data['send_at']) || $data['send_at'] <= 0) {
            throw new \Exception('Неверный формат времени');
        }
    }
} 