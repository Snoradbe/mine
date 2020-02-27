<?php


namespace App\Handlers\Admin\Discounts;


use App\Entity\Site\Discount;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Discounts\AddDiscountEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Discount\DiscountRepository;
use App\Repository\Site\Server\ServerRepository;

class AddDiscountHandler
{
    private $serverRepository;

    private $discountRepository;

    public function __construct(ServerRepository $serverRepository, DiscountRepository $discountRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->discountRepository = $discountRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id, false);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, ?int $server, int $discount, string $type, string $date): void
    {
        if (!is_null($server)) {
            $server = $this->getServer($server);
        }

        try {
            $date = new \DateTimeImmutable($date);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if ($date->getTimestamp() <= time()) {
            throw new Exception('Дата должна быть больше чем сейчас!');
        }

        $discount = new Discount(
            $server,
            $type,
            $discount,
            $date->getTimestamp()
        );

        $this->discountRepository->create($discount);

        event(new AddDiscountEvent($admin, $discount));
    }
}