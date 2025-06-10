# Messenger API

API для обработки webhook-сообщений мессенджера на Yii2 + PostgreSQL

## Запуск

```bash
docker compose up -d --build
docker exec -ti test-yii-php sh -c 'php yii migrate --interactive=0'
```

## API

**POST /api/v1/webhook** - обработка сообщений

### Пример запроса:

```json
{
    "external_message_id": "1a2b3c4d5e6f7890abcdef1234567890",
    "external_client_id": "fedcba0987654321abcdef1234567890",
    "client_phone": "+79194961182",
    "message_text": "Привет!",
    "send_at": 1640995200
}
```

```bash
curl -X POST http://localhost/api/v1/webhook -H "Content-Type: application/json" -d '{"external_message_id":"1a2b3c4d5e6f7890abcdef1234567890", "external_client_id":"fedcba0987654321abcdef1234567890","client_phone":"+79194961182","message_text":"Привет!","send_at":1640995200}'
```

