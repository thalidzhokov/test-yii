<?php

namespace app\services;

use Yii;
use app\models\Client;
use app\models\Dialog;
use app\models\Message;
use app\models\forms\WebhookForm;

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
            // Валидация через форму
            $form = new WebhookForm();
            $form->load($data, ''); // '' - потому что данные приходят без обертки
            
            if (!$form->validate()) {
                return [
                    'status' => 'error', 
                    'message' => 'Ошибка валидации',
                    'errors' => $form->getFirstErrors()
                ];
            }
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // TODO: кеш клиентов через redis hash, тк одни и те же клиенты отправляют много сообщений подряд
                $client = Client::findOrCreate(
                    $form->external_client_id, 
                    $form->client_phone
                );
                
                // TODO: кеш диалогов через redis, нужно думать над tll, или рассматривать другие возможности
                $dialog = $client->getOrCreateDialog();
                
                // Создать сообщение
                $message = new Message();
                $message->external_message_id = $form->external_message_id;
                $message->dialog_id = $dialog->id;
                $message->message_text = $form->message_text;
                $message->send_at = date('Y-m-d H:i:s', $form->send_at);
                
                if (!$message->save()) {
                    // Проверяем ошибки валидации - если дубликат external_message_id
                    if (isset($message->errors['external_message_id'])) {
                        $transaction->rollBack();
                        
                        // TODO: делаем отметку в redis что сообщение дубликат
                        
                        return ['status' => 'success', 'message' => 'Duplicate ignored'];
                    }
                    
                    $transaction->rollBack();
                    return ['status' => 'error', 'message' => 'Ошибка сохранения: ' . json_encode($message->errors)];
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
} 