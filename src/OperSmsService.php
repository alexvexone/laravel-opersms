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
     * @param  array|string|null  $text
     *
     * @throws Exception
     */
    public static function send(array|string $phone, array|string $text = null): void
    {
        if (is_null(config('opersms.login')) || is_null(config('opersms.password'))) {
            throw new Exception('Не указаны учетные данные от OperSMS.');
        }

        if (
            is_array($phone) &&
            is_array($text) &&
            count($phone) !== count($text)
        ) {
            throw new Exception('Количество номеров должно совпадать с количеством сообщений.');
        }

        if (is_array($phone) && is_null($text)) {
            $array = [];

            foreach ($phone as $subArray) {
                if (!is_array($subArray)) {
                    throw new Exception('');
                }

                
            }
        } else {
            if (is_array($phone)) {
                $array = [];

                if (is_array($text)) {
                    foreach ($phone as $key => $value) {
                        $array[] = [
                            'phone' => self::preparePhone($value),
                            'text' => self::prepareText($text[$key]),
                        ];
                    }
                } else {
                    $preparedText = self::prepareText($text);

                    foreach ($phone as $value) {
                        $array[] = [
                            'phone' => self::preparePhone($value),
                            'text' => $preparedText,
                        ];
                    }
                }
            } else {
                $preparedPhone = self::preparePhone($phone);

                if (is_array($text)) {
                    $array = [];

                    foreach ($text as $value) {
                        $array[] = [
                            'phone' => $preparedPhone,
                            'text' => self::prepareText($value),
                        ];
                    }
                } else {
                    $array = [
                        [
                            'phone' => $preparedPhone,
                            'text' => self::prepareText($text),
                        ],
                    ];
                }
            }
        }

        $chunked = array_chunk($array, 50, true);

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
        $phone = preg_replace('/\D/', '', trim($raw));

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
     * @throws Exception
     */
    private static function prepareText(string $text): string
    {
        if (strlen($text) === 0) {
            throw new Exception('Сообщение или одно из сообщений не может быть пустым.');
        }

        return $text;
    }
}
