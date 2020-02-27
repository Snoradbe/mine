<?php


namespace App\Helpers;


class LogsHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function getShopLogs(): array
    {
        return array_filter(config('sitelogs', []), function ($data) {
            return $data['type'] == 'client_shop';
        });
    }

    public static function getCabinetLogs(): array
    {
        return array_filter(config('sitelogs', []), function ($data) {
            return $data['type'] == 'client_cabinet';
        });
    }

    public static function getAllClientLogs(): array
    {
        return array_filter(config('sitelogs', []), function ($data) {
            return in_array($data['type'], ['client', 'client_shop', 'client_cabinet']);
        });
    }

    public static function getModerLogs(): array
    {
        return array_filter(config('sitelogs', []), function ($data) {
            return $data['type'] == 'moder';
        });
    }

    public static function getAdminLogs(): array
    {
        return array_filter(config('sitelogs', []), function ($data) {
            return $data['type'] == 'admin';
        });
    }
}