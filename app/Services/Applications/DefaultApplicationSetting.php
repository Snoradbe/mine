<?php


namespace App\Services\Applications;


class DefaultApplicationSetting
{
    private function __construct(){}

    private static $descr = 'Описание...';

    private static $form = [
        'Первый вопрос',
        'Второй вопрос',
        'Ну и так далее...'
    ];

    private static $rules = [
        'Первое правило',
        'Второе правило'
    ];

    private static function parseDefaultValuesOnServers(array $servers, $value): array
    {
        $data = [];

        foreach ($servers as $server)
        {
            $data[$server->getId()] = $value;
        }

        return $data;
    }

    public static function getData(string $groupName, array $servers): array
    {
        return [
            'name' => $groupName,
            'enabled' => self::parseDefaultValuesOnServers($servers, true),
            'descr' => static::$descr,
            'form' => static::$form,
            'rules' => static::$rules,
            'server' => []
        ];
    }
}