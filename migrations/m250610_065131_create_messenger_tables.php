<?php

use yii\db\Migration;

/**
 * Создание таблиц для мессенджера
 */
class m250610_065131_create_messenger_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица клиентов
        $this->createTable('{{%clients}}', [
            'id' => $this->bigPrimaryKey(),
            'external_client_id' => $this->string(32)->notNull()->unique(),
            'client_phone' => $this->string(12)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Таблица диалогов
        $this->createTable('{{%dialogs}}', [
            'id' => $this->bigPrimaryKey(),
            'client_id' => $this->integer()->notNull(),
            'last_message_at' => $this->timestamp()->null(),
            'messages_count' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Таблица сообщений
        $this->createTable('{{%messages}}', [
            'id' => $this->bigPrimaryKey(),
            'external_message_id' => $this->string(32)->notNull()->unique(),
            'dialog_id' => $this->integer()->notNull(),
            'message_text' => $this->text()->notNull(),
            'send_at' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Внешние ключи
        $this->addForeignKey('fk-dialogs-client_id', '{{%dialogs}}', 'client_id', '{{%clients}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-messages-dialog_id', '{{%messages}}', 'dialog_id', '{{%dialogs}}', 'id', 'CASCADE');

        // Индексы для производительности
        $this->createIndex('idx-clients-external_id', '{{%clients}}', 'external_client_id');
        $this->createIndex('idx-messages-external_id', '{{%messages}}', 'external_message_id');
        $this->createIndex('idx-messages-dialog_send_at', '{{%messages}}', ['dialog_id', 'send_at']);

        // Триггер для обновления статистики диалога
        $this->execute("
            CREATE OR REPLACE FUNCTION update_dialog_stats()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE dialogs 
                SET messages_count = messages_count + 1,
                    last_message_at = NEW.send_at,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = NEW.dialog_id;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        $this->execute("
            CREATE TRIGGER trigger_update_dialog_stats
            AFTER INSERT ON messages
            FOR EACH ROW
            EXECUTE FUNCTION update_dialog_stats();
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP TRIGGER IF EXISTS trigger_update_dialog_stats ON messages');
        $this->execute('DROP FUNCTION IF EXISTS update_dialog_stats()');
        
        $this->dropTable('{{%messages}}');
        $this->dropTable('{{%dialogs}}');
        $this->dropTable('{{%clients}}');
    }
}
