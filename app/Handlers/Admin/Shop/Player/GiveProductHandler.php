<?php


namespace App\Handlers\Admin\Shop\Player;


use App\DataObjects\Shop\PipelineObject;
use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
use App\Events\Admin\Shop\Player\GiveProductEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Repository\Site\User\UserRepository;
use Illuminate\Pipeline\Pipeline;

class GiveProductHandler
{
    private $productRepository;

    private $userRepository;

    private $serverRepository;

    public function __construct(ProductRepository $productRepository, UserRepository $userRepository, ServerRepository $serverRepository)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
    }

    private function getProduct(int $id): Product
    {
        $product = $this->productRepository->find($id);
        if (is_null($product)) {
            throw new Exception('Товар не найден!');
        }

        return $product;
    }

    private function getUser(string $name): User
    {
        $user = $this->userRepository->findByName($name);
        if (is_null($user)) {
            throw new Exception('Игрок не найден!');
        }

        return $user;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(User $admin, int $serverId, string $name, int $productId, int $amount)
    {
        $target = $this->getUser($name);
        $product = $this->getProduct($productId);
        $server = $this->getServer($serverId);

        if (!is_null($product->getServer()) && $product->getServer()->getId() !== $server->getId()) {
            throw new Exception('Вы не можете выдать этот товар на указанный сервер!');
        }

        /* @var PipelineObject $po */
        $po = app(Pipeline::class)
            ->send(new PipelineObject($target, $server, $product, $amount, 'rub'))
            ->through([
                \App\Services\Shop\Pipelines\ToStoragePipeline::class,
            ])
            ->then(function ($po) {
                return $po;
            });

        event(new GiveProductEvent(
            $admin,
            $target,
            $product,
            $server,
            $amount
        ));
    }
}