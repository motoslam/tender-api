# Zebra тестовое задание

## Быстрый старт

### 1. Клонирование и настройка

```bash
# Создайте папку проекта
git clone https://github.com/motoslam/tender-api.git
cd tender-api
```

Если импорт не запускается из-за невозможности получить данные по сети
```bash
# Поместите ваш CSV файл в:
cp /path/to/your/test_task_data.csv storage/app/csv/
```

### 2. Запуск окружения

```bash
# Копируем конфигурацию окружения
cp .env.example .env

# Запускаем контейнеры
docker-compose up -d --build

# Внутри контейнера
php artisan key:generate
php artisan migrate --seed
php artisan tenders:import
```
Будет создан тестовый пользователь для авторизации <br>
Email: **test@example.com** <br>
Password: **password123**

## API Endpoints

### 0. Авторизация
Нужна только для POST запроса <br><br>
**POST** `/api/login`
**Параметры:**
- `email` - Email
- `password` - Пароль

**Успешный ответ:**
```json
{
    "message": "Успешный вход",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-13T12:52:30.000000Z",
        "updated_at": "2025-11-13T12:52:30.000000Z"
    },
    "token": "1|r9KcBxJ1SPKAhFUQj6NdPZnQdiuLWyiap0HHCY6F87f843a5"
}
```

Для авторизованного запроса token необходимо подставить в заголовок <br> **Authorization: Bearer YOUR_TOKEN**

### 1. Получить список тендеров
**GET** `/api/tenders`

**Параметры:**
- `name` - фильтр по названию (LIKE)
- `date_start` - начальная дата диапазона (YYYY-MM-DD)
- `date_end` - конечная дата диапазона (YYYY-MM-DD)

### 2. Получить тендер по ID
**GET** `/api/tenders/{id}`


### 3. Создать новый тендер
**POST** `/api/tenders`

**Тело запроса:**
```json
{
    "external_code": 999999999,
    "number": "TEST-001",
    "status": "Открыто",
    "name": "Тестовый тендер",
    "updated_at": "2024-01-15 10:00:00"
}
```

## Импорт данных

Для импорта данных из CSV файла используется команда:

```bash
# Основной импорт (файл по умолчанию или получение данных из GitHub)
php artisan tenders:import

# Импорт конкретного файла
php artisan tenders:import custom_data.csv
```

**Требования к CSV:**
- Формат: `Внешний код,Номер,Статус,Название,Дата изм.`
- Кодировка: UTF-8
- Разделитель: запятая
- Формат даты: `dd.mm.yyyy HH:MM:SS`


## Примеры ответов API

### Успешное создание тендера:
```json
{
  "message": "Тендер успешно создан",
  "data": {
    "id": 5501,
    "external_code": 999999999,
    "number": "TEST-001",
    "status": "Открыто",
    "name": "Тестовый тендер",
    "updated_at": "2024-01-15 10:00:00"
  }
}
```

### Ответ с ошибкой валидации:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "external_code": ["The external code has already been taken."],
    "status": ["The selected status is invalid."]
  }
}
```

### Успешный список тендеров:
```json
{
  "data": [
    {
      "id": 1,
      "external_code": 999999999,
      "number": "99999-0",
      "status": "Закрыто",
      "name": "Лабораторная посуда",
      "updated_at": "13.11.2025 12:16:04"
    }
  ],
  "links": {
    "first": "http://localhost/api/tenders?page=1",
    "last": "http://localhost/api/tenders?page=110",
    "prev": null,
    "next": "http://localhost/api/tenders?page=2"
  },
  "meta": {
    "current_page": 1,
    "per_page": 50,
    "total": 5500
  }
}
```
