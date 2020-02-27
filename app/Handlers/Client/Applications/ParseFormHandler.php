<?php


namespace App\Handlers\Client\Applications;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Settings\DataType;

class ParseFormHandler
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function sortRules(array $baseRules): array
    {
        $rules = [
            'left' => [],
            'right' => []
        ];

        foreach ($baseRules as $i => $rule)
        {
            if ($i % 2 === 1) {
                $rules['right'][] = $rule;
            } else {
                $rules['left'][] = $rule;
            }
        }

        /*$j = count($baseRules) / 2;

        foreach ($baseRules as $i => $rule)
        {
            if ($i <= $j) {
                $rules['left'][] = $rule;
            } else {
                $rules['right'][] = $rule;
            }
        }*/

        return $rules;
    }

    public function handle(string $group, int $serverId): array
    {
        $server = $this->getServer($serverId);

        $settings = settings('applications', DataType::JSON, []);
        if (!isset($settings['statuses'][$group])) {
            throw new Exception('Должность не найдена!');
        }

        $status = $settings['statuses'][$group];
        if (!isset($status['enabled'][$server->getId()]) || !$status['enabled'][$server->getId()]) {
            throw new Exception('На выбранный сервер нет набора!');
        }

        unset($status['enabled']);

        $status['rules'] = $this->sortRules($status['rules']);
        $status['server'] = $status['server'][$server->getId()] ?? [];

        return [$status, $group];
    }
}