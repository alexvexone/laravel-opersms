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
            throw new Exception(__('opersms::messages.no_credentials_error'));
        }

        if (
            is_array($phone) &&
            is_array($text) &&
            count($phone) !== count($text)
        ) {
            throw new Exception(__('opersms::messages.quantify_of_phones_and_messages_error'));
        }

        if (is_array($phone) && is_null($text)) {
            $array = self::prepareCustomArray($phone);
        } else {
            if (is_array($phone)) {
                if (is_array($text)) {
                    $array = self::prepareManyToManyArray($phone, $text);
                } else {
                    $array = self::prepareManyToOneArray($phone, $text);
                }
            } else {
                if (is_array($text)) {
                    $array = self::prepareOneToManyArray($phone, $text);
                } else {
                    $array = self::prepareOneToOneArray($phone, $text);
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
     * Получить обработанный массив для отправки из пользовательского массива номеров и СМС.
     *
     * (Пользовательский массив с обязательными ключами phone и text)
     *
     * @param  array  $array
     *
     * @return array
     * @throws Exception
     */
    private static function prepareCustomArray(array $array): array
    {
        $result = [];

        foreach ($array as $subArray) {
            if (!is_array($subArray)) {
                throw new Exception(__('opersms::messages.must_be_array_error'));
            }

            if (!isset($subArray['phone'])) {
                throw new Exception(__('opersms::messages.missing_phone_error'));
            }

            if (!isset($subArray['text'])) {
                throw new Exception(__('opersms::messages.missing_message_error'));
            }

            $result[] = [
                'phone' => self::preparePhone($subArray['phone']),
                'text' => self::prepareText($subArray['text']),
            ];
        }

        return $result;
    }

    /**
     * Получить обработанный массив для отправки из массива номеров и массива СМС.
     *
     * (Много номеров - много СМС)
     *
     * @param  array  $phones
     * @param  array  $texts
     *
     * @return array
     * @throws Exception
     */
    private static function prepareManyToManyArray(array $phones, array $texts): array
    {
        $result = [];

        foreach ($phones as $key => $phone) {
            $result[] = [
                'phone' => self::preparePhone($phone),
                'text' => self::prepareText($texts[$key]),
            ];
        }

        return $result;
    }

    /**
     * Получить обработанный массив для отправки из массива номеров и СМС.
     *
     * (Много номеров - СМС)
     *
     * @param  array  $phones
     * @param  string  $text
     *
     * @return array
     * @throws Exception
     */
    private static function prepareManyToOneArray(array $phones, string $text): array
    {
        $result = [];
        $preparedText = self::prepareText($text);

        foreach ($phones as $phone) {
            $result[] = [
                'phone' => self::preparePhone($phone),
                'text' => $preparedText,
            ];
        }

        return $result;
    }

    /**
     * Получить обработанный массив для отправки из номера и массива СМС.
     *
     * (Номер - много СМС)
     *
     * @param  string  $phone
     * @param  array  $texts
     *
     * @return array
     * @throws Exception
     */
    private static function prepareOneToManyArray(string $phone, array $texts): array
    {
        $result = [];
        $preparedPhone = self::preparePhone($phone);

        foreach ($texts as $text) {
            $result[] = [
                'phone' => $preparedPhone,
                'text' => self::prepareText($text),
            ];
        }

        return $result;
    }

    /**
     * Получить обработанный массив для отправки из номера и СМС.
     *
     * (Номер - СМС)
     *
     * @param  string  $phone
     * @param  string  $text
     *
     * @return array[]
     * @throws Exception
     */
    private static function prepareOneToOneArray(string $phone, string $text): array
    {
        return [
            [
                'phone' => self::preparePhone($phone),
                'text' => self::prepareText($text),
            ],
        ];
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
            throw new Exception(__('opersms::messages.phone_format_validation_error'));
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
            throw new Exception(__('opersms::messages.message_length_validation_error'));
        }

        return $text;
    }
}
