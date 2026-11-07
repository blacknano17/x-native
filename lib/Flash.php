<?php

class Flash
{
    private const SESSION_KEY = '_flash_messages';

    public static function add(string $type, string $message): void
    {
        $messages = Session::get(self::SESSION_KEY, []);
        $messages[] = [
            'type'    => $type,
            'message' => $message
        ];
        Session::set(self::SESSION_KEY, $messages);
    }

    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    public static function warning(string $message): void
    {
        self::add('warning', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }

    /**
     * Ambil dan bersihkan seluruh pesan flash untuk ditampilkan
     */
    public static function all(): array
    {
        $messages = Session::get(self::SESSION_KEY, []);
        Session::remove(self::SESSION_KEY);
        return $messages;
    }
}
