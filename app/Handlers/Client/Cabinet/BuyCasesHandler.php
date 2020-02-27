<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\Cabinet\BuyCasesEvent;
use App\Exceptions\Exception;
use App\Helpers\RandomHelper;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\User\UserRepository;
use App\Services\Game\Rcon\Connection;
use App\Services\Game\Rcon\Connector;

class BuyCasesHandler
{
    private const UNDEFINED = 0;

    private const SUCCESS = 1;

    private const PLAYER_NOT_FOUND = 2;

    private const NOT_INVENTORY = 3;

    private $userRepository;

    private $serverRepository;

    private $connector;

    public function __construct(UserRepository $userRepository, ServerRepository $serverRepository, Connector $connector)
    {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->connector = $connector;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function randomCases(int $amount): array
    {
        $list = [];

        $cases = config('site.cases.random', []);
        foreach ($cases as $case => $chance)
        {
            $list[$case] = 0;
        }

        for ($i = 0; $i < $amount; $i++)
        {
            ++$list[RandomHelper::randomWithChance($cases)];
        }

        return $list;
    }

    private function giveOnServer(User $user, Connection $connection, array $cases): array
    {
        $count = 0;
        $status = static::UNDEFINED;

        foreach ($cases as $case => $amount)
        {
            if ($amount < 1) continue;

            $response = $connection->send('givebox ' . $user->getName() . ' ' . $case . ' ' . $amount, true)['body'] ?? '';
            if (strpos($response, 'успешно выдана') !== false) {
                $count += $amount;
                $status = static::SUCCESS;
            } elseif (strpos($response, 'игрок не найден') !== false) {
                $status = static::PLAYER_NOT_FOUND;
            } elseif (strpos($response, 'недостаточно места в инвентаре') !== false) {
                $status = static::NOT_INVENTORY;
            } else {
                $count += $amount;
            }
        }

        return [$count, $status, $cases];
    }

    public function handle(User $user, int $serverId, int $amount): array
    {
        $server = $this->getServer($serverId);

        if (!$user->hasCoins($amount)) {
            throw new Exception('Недостаточно монет на балансе!');
        }

        if ($this->connector->exists($server->getId())) {
            $connection = $this->connector->get($server->getId());
        } else {
            $this->connector->addByServer($server);
            $connection = $this->connector->get($server->getId());
        }

        [$count, $status, $cases] = $this->giveOnServer($user, $connection, $this->randomCases($amount));
        switch ($status)
        {
            case static::SUCCESS:
                $user->withdrawCoins($amount, false);
                $this->userRepository->update($user);
                $message = 'Вы успешно получили кейсы на сервер в количестве: ' . $amount . ' шт.';

                break;

            case static::PLAYER_NOT_FOUND:
                throw new Exception('Вы не зашли на сервер! Повторите попытку когда зайдете');

            case static::NOT_INVENTORY:
                $user->withdrawCoins($count, false);
                $this->userRepository->update($user);
                $amount = $count;
                $message = 'Вы успешно получили кейсы на сервер, но места в инвентаре хватило только для ' . $count . ' шт.';

                break;

            default:
                $user->withdrawCoins($amount, false);
                $this->userRepository->update($user);
                throw new Exception('Неизвестный ответ от сервера! Сообщите администрации');
        }

        event(new BuyCasesEvent($user, $server, $cases));

        return [$message, $amount];
    }
}