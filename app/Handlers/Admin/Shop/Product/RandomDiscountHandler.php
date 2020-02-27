<?php


namespace App\Handlers\Admin\Shop\Product;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Product\Discount\RandomDiscountEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Settings\DataType;

class RandomDiscountHandler
{
    private $productRepository;

    private $serverRepository;

    public function __construct(ProductRepository $productRepository, ServerRepository $serverRepository)
    {
        $this->productRepository = $productRepository;
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

    public function handle(User $admin, int $min, int $max, int $serverId, int $days, int $method): void
    {
        if ($serverId != 0) {
            $server = $this->getServer($serverId);
        } else {
            $server = null;
        }

        try {
            $this->productRepository->randomDiscounts(
                $server,
                $min,
                $max,
                $days,
                settings('shop', DataType::JSON, [])['random_discounts'][$method]['sql']
            );
        } catch (\Exception $exception) {
            if ($exception->getMessage() !== 'SQLSTATE[HY000]: General error') {
                throw $exception;
            }
        }

        event(new RandomDiscountEvent($admin, $server, $min, $max, $days, settings('shop', DataType::JSON, [])['random_discounts'][$method]['name']));
    }
}