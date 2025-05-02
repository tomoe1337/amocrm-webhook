# Проект интеграции с amoCRM

Этот проект реализует интеграцию с API amoCRM для обработки вебхуков и управления примечаниями (заметками). Он позволяет автоматически создавать примечания в amoCRM при добавлении или обновлении сделок и контактов.

## Основные функции
- Обработка вебхуков от amoCRM.
- Добавление примечаний (заметок) в сделки и контакты.

## Требования
Для работы с проектом необходимо:
- PHP >= 8.2
- Laravel >= 11.x
- Composer
- Доступ к API amoCRM (client_id, client_secret, subdomain)

## Установка
1. **Клонируйте репозиторий**
   ```bash
   git clone https://github.com/tomoe1337/amocrm-webhook.git
   cd amocrm-webhook
   ```

2. **Установите зависимости**
   ```bash
   composer install
   ```

3. **Настройте окружение**
   Создайте файл `.env` из примера `.env.example`:
   ```bash
   cp .env.example .env
   ```
   Заполните следующие переменные в `.env`:
   ```env
   AMOCRM_CLIENT_ID=your_client_id
   AMOCRM_CLIENT_SECRET=your_client_secret
   AMOCRM_SUBDOMAIN=your_subdomain
   AMOCRM_REDIRECT_URI=your_redirect_uri
   AMOCRM_ACCESS_TOKEN=
   AMOCRM_REFRESH_TOKEN=
   ```

4. **Сгенерируйте ключ приложения**
   ```bash
   php artisan key:generate
   ```

5. **Запустите локальный сервер**
   ```bash
   php artisan serve
   ```

## Получение токенов доступа
Для получения токенов доступа выполните следующие шаги:

1. Скопируйте Authorization Code и параметров интеграции (обновляется каждый 20 мин):


2. Используйте консольную команду чтобы с помощью Authorization Code получить токены доступа:
   ```bash
   php artisan amocrm:get-tokens {code}
   ```
   Замените `{code}` на ваш Authorization Code.

3. Токены будут сохранены в файл `.env` автоматически.
