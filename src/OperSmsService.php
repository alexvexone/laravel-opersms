<?php

namespace Alexvexone\LaravelOperSms;


/**
 * Сервис для работы с OperSMS
 *
 * https://opersms.uz
 */
class OperSmsService
{
    /**
     * Отправить СМС
     *
     * @param  string  $phone
     * @param  string  $text
     *
     * @return void
     */
    public static function send(string $phone, string $text): void
    {
        $ch = curl_init(config('opersms.url'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "login=" . config('opersms.login') . "&password=" . config('opersms.password') . "&data=".json_encode([
                [
                    'phone' => str_replace('+', '', $phone),
                    'text' => utf8_encode($text),
                ]
            ])
        );

        curl_exec($ch);
        curl_close($ch);
    }
}
