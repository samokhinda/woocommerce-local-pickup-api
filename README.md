# WooCommerce Local Pickup Locations API

## Описание
Этот плагин для WordPress предоставляет REST API эндпоинт для получения данных точек самовывоза через WooCommerce. Он использует аутентификацию с помощью `consumer_key` и `consumer_secret`, предоставляемых WooCommerce.

## Установка
1. Скачайте архив с плагином.
2. Разархивируйте его в директорию `wp-content/plugins/`.
3. Активируйте плагин через меню "Плагины" в админ-панели WordPress.

## Использование
После активации плагина вы можете использовать следующие эндпоинты:

### Получение точек самовывоза

```
GET /wp-json/wc/v3/local-pickup-locations
```

### Создание новой точки самовывоза

```
POST /wp-json/wc/v3/local-pickup-locations
```

Пример тела запроса:
```json
{
  "name": "Магазин",
  "address": {
    "country": "RU",
    "state": "Московская",
    "city": "Москва",
    "address_1": "ул. Ленина, 23-4 (этаж 4)",
    "postcode": "121121"
  },
  "coordinates": {
    "lat": 50.45,
    "lng": 30.52
  },
  "details": "Время работы: 9:00-18:00",
  "enabled": true
}
```

### Обновление существующей точки самовывоза

```
PUT /wp-json/wc/v3/local-pickup-locations/{id}
```

Пример тела запроса:
```json
{
  "name": "Обновленное название",
  "address": {
    "country": "RU",
    "state": "Московская",
    "city": "Москва",
    "address_1": "ул. Пушкина, 10",
    "postcode": "121121"
  },
  "details": "Новый график работы: 10:00-19:00",
  "enabled": true
}
```

### Получение настроек точек самовывоза

```
GET /wp-json/wc/v3/local-pickup-settings
```

### Обновление настроек точек самовывоза

```
PUT /wp-json/wc/v3/local-pickup-settings
```

### Аутентификация
Для доступа к эндпоинту необходимо использовать аутентификацию Basic Auth с `consumer_key` и `consumer_secret`, которые можно получить в настройках WooCommerce.

### Ответы API

#### Успешный ответ для получения точек самовывоза
```json
{
  "success": true,
  "data": [
    {
      "name": "Магазин",
      "address": {
        "country": "RU",
        "state": "Московская",
        "city": "Москва",
        "address_1": "ул. Ленина, 23-4 (этаж 4)"
      },
      "coordinates": {
        "lat": null,
        "lng": null
      },
      "details": "часы работы: 10:00-20:00",
      "enabled": true
    },
    {
      "name": "Главный офис",
      "address": {
        "country": "RU",
        "city": "Архангельск",
        "address_1": "пр. Троицкий, 123-33"
      },
      "coordinates": {
        "lat": null,
        "lng": null
      },
      "details": "Часы работы: 10:00-20:00",
      "enabled": true
    }
  ]
}
```

### Обработка ошибок
- Если данные точек самовывоза не найдены:
```json
{
  "success": false,
  "error": "Данные точек самовывоза не найдены."
}
```

- При отсутствии прав доступа будет возвращен статус HTTP 403.
- При некорректных данных будет возвращен статус HTTP 400.

## Лицензия

Этот проект лицензирован под MIT License. Если вы используете этот плагин и он вам помогает, пожалуйста, рассмотрите возможность поддержки проекта.

Поддержка проекта

Вы можете поддержать проект, сделав донат на один из следующих кошельков:

USDT TRC20: TJqjMMUG1inWWYUGCW1vdUvrZioUFhGafz

TON: UQCb477bz6nFfEMIdgtUd6nl3bUdeg6I08j01_CIFZOp9Lj5

BTC: 1B7HtELv5eJAms2ThwbU5t2M645de4j296

DOGS: UQCb477bz6nFfEMIdgtUd6nl3bUdeg6I08j01_CIFZOp9Lj5


