# Сервис для интеграции с [OperSMS](https://opersms.uz/ru)

## Установка

```bash
composer require alexvexone/laravel-opersms
```

Пакет использует Laravel Package Discovery, поэтому необязательно явно объявлять сервис-провайдер. 

В `.env` необходимо настроить директивы для подключения к OperSMS:

`OPERSMS_LOGIN=YOUR_LOGIN`\
`OPERSMS_PASSWORD=YOUR_PASSWORD`

По умолчанию запросы отправляются на `http://83.69.139.182:8080`, если необходимо изменить URL, переопределите директиву:

`OPERSMS_URL=YOUR_URL`

Если необходимо соединяться по SSL:

`OPERSMS_SSL_VERIFICATION=true`

## Использование

### Через Laravel Notifications

```php
public function via(object $notifiable)
{
    return ['opersms'];
}
```

или

```php
public function via(object $notifiable)
{

    return [\Alexvexone\LaravelOperSms\Channels\OperSmsChannel::class];
}
```

### Через сервис напрямую

Отправка ОДНОГО сообщения ОДНОМУ телефонному номеру:

```php
\Alexvexone\LaravelOperSms\OperSmsService::send('YOUR_PHONE', 'YOUR_TEXT');
```

Массовая отправка РАЗНЫХ сообщений РАЗНЫМ телефонным номерам:

```php
\Alexvexone\LaravelOperSms\OperSmsService::send(['YOUR_PHONE1', ...], ['YOUR_TEXT1', ...]);
```

Массовая отправка РАЗНЫХ сообщений ОДНОМУ телефонному номеру:

```php
\Alexvexone\LaravelOperSms\OperSmsService::send('YOUR_PHONE', ['YOUR_TEXT1', ...]);
```

Массовая отправка через собственный массив:

```php
\Alexvexone\LaravelOperSms\OperSmsService::send([['phone' => 'YOUR_PHONE1', 'text' => 'YOUR_TEXT1'], ...]);
```

## Публикация (необязательно)

```bash
php artisan vendor:publish --provider="Alexvexone\LaravelOperSms\Providers\OperSmsServiceProvider"
```
