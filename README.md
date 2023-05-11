# Сервис для интеграции с OperSMS

## Установка

1. Выполните в консоли: `composer require alexvexone/laravel-opersms`
2. В `.env` необходимо настроить директивы для подключения к OperSMS:

`OPERSMS_LOGIN=YOUR_LOGIN`\
`OPERSMS_PASSWORD=YOUR_PASSWORD`

По умолчанию запросы отправляются на `http://83.69.139.182:8080`, если необходимо изменить URL, переопределите директиву:

`OPERSMS_URL=YOUR_URL`

## Использование

Можно использовать алиас:

    public function via(object $notifiable)
    {
        return ['opersms'];
    }

Или вызывать класс драйвера напрямую:

    public function via(object $notifiable)
    {
        return [\Alexvexone\LaravelOperSms\Channels\OperSmsChannel::class];
    }

## Публикация конфигурации

Выполните в консоли:

`php artisan vendor:publish --provider="Alexvexone\LaravelOperSms\Providers\OperSmsServiceProvider"`
