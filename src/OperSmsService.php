<?php

namespace Alexvexone\LaravelOperSms;


use Exception;

/**
 * Сервис для работы с OperSMS.
 *
 * https://opersms.uz
 */
class OperSmsService
{
    /**
     * Отправить СМС.
     *
     * @param  array|string  $phone
     * @param  string  $text
     *
     * @return void
     * @throws Exception
     */
    public static function send(array|string $phone, string $text): void
    {
        $preparedText = self::prepareText($text);

        if (is_array($phone)) {
            $array = [];

            foreach ($phone as $value) {
                $array[] = [
                    'phone' => self::preparePhone($value),
                    'text' => $preparedText,
                ];
            }

            $chunked = array_chunk($array, 50, true);
        } else {
            $chunked = [
                [
                    [
                        'phone' => self::preparePhone($phone),
                        'text' => $preparedText,
                    ],
                ],
            ];
        }

        foreach ($chunked as $chunk) {
            $ch = curl_init(config('opersms.url'));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            if (config('opersms.ssl_verification')) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                'login=' . config('opersms.login') . '&password=' . config('opersms.password') . '&data=' . json_encode($chunk)
            );

            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * Получить обработанный номер телефона.
     *
     * @param  string  $raw
     *
     * @return string
     * @throws Exception
     */
    private static function preparePhone(string $raw): string
    {
        $phone = str_replace(['+', '(', ')', ' ', '-'], '', $raw);

        if (!preg_match('/^998[0-9]{9}+$/', $phone)) {
            throw new Exception('Номер телефона указан в неверном формате: ' . $phone);
        }

        return $phone;
    }

    /**
     * Получить обработанный текст.
     *
     * @param  string  $text
     *
     * @return string
     */
    private static function prepareText(string $text): string
    {
        return utf8_encode($text);
    }
}
