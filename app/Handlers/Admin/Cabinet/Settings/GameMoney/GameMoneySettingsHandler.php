<?php


namespace App\Handlers\Admin\Cabinet\Settings\GameMoney;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Cabinet\Settings\GameMoney\GameMoneySettingsEvent;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;
use Doctrine\Common\Collections\ArrayCollection;

class GameMoneySettingsHandler
{
    private $serverRepository;

    private $settings;

    private $servers;

    public function __construct(ServerRepository $serverRepository, Settings $settings)
    {
        $this->serverRepository = $serverRepository;
        $this->settings = $settings;

        $this->servers = new ArrayCollection($serverRepository->getAll(false));
    }

    private function getServer(int $id): ?Server
    {
        $server = $this->servers->filter(function (Server $server) use ($id) {
            return $server->getId() == $id;
        })->first();

        return $server instanceof Server ? $server : null;
    }

    public function filterRates(array $rates): array
    {
        $result = [];

        foreach ($rates as $serverId => $rate)
        {
            $rate = (int) $rate;
            if ($rate < 1) continue;

            $server = $this->getServer($serverId);
            if (!is_null($server)) {
                $result[$server->getId()] = $rate;
            }
        }

        return $result;
    }

    public function filterManagers(array $managers): array
    {
        $result = [];

        $managersList = config('site.game_money.managers', []);

        foreach ($managers as $serverId => $manager)
        {
            if (empty($manager) || !isset($managersList[$manager])) continue;

            $server = $this->getServer($serverId);
            if (!is_null($server)) {
                $result[$server->getId()] = $managersList[$manager];
            }
        }

        return $result;
    }

    public function handle(User $admin, array $rates, array $managers): void
    {
        $settings = settings('game_money', DataType::JSON);

        $oldRates = $settings['rate'] ?? [];
        $oldManagers = $settings['manager'] ?? [];

        $settings['rate'] = $rates;
        $settings['manager'] = $managers;

        $this->settings->set('game_money', $settings);
        $this->settings->save();

        event(new GameMoneySettingsEvent($admin, $oldRates, $settings['rate'], $oldManagers, $settings['manager']));
    }
}