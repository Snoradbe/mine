<?php


namespace App\Handlers\Client\Shop;


use App\DataObjects\Shop\PipelineObject;
use App\Entity\Site\Server;
use App\Entity\Site\Shop\Product;
use App\Entity\Site\User;
//use App\Events\Client\Shop\BuyEvent;
use App\Events\Client\Shop\BuyProductEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use Illuminate\Pipeline\Pipeline;

class BuyHandler
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

    private function getProduct(int $id): Product
    {
        $product = $this->productRepository->find($id);
        if (is_null($product) || !$product->isEnabled() || (!is_null($product->getCategory()->getServer()) && !$product->getCategory()->getServer()->isEnabled())) {
            throw new Exception('Товар не найден!');
        }

        return $product;
    }

    public function handle(User $user, int $serverId, int $productId, int $amount, string $valute): PipelineObject
    {
        $server = $this->getServer($serverId);
        $product = $this->getProduct($productId);

        if (!is_null($product->getServer()) && $product->getServer()->getId() != $server->getId()) {
            throw new Exception('Неправильный сервер!');
        }

        if ($valute == 'rub' && $product->getPrice() < 1) {
            throw new Exception('Этот товар не продается за рубли!');
        } elseif ($valute == 'coins' && $product->getPriceCoins() < 1) {
            throw new Exception('Этот товар не продается за монеты!');
        }

        /* @var PipelineObject $po */
        $po = app(Pipeline::class)
            ->send(new PipelineObject($user, $server, $product, $amount, $valute))
            ->through([
                \App\Services\Shop\Pipelines\Buy\CheckGroupPipeline::class,
                \App\Services\Shop\Pipelines\Buy\DiscountPipeline::class,
                \App\Services\Shop\Pipelines\Buy\PaymentPipeline::class,
                \App\Services\Shop\Pipelines\Buy\ToStatisticsPipeline::class,
                \App\Services\Shop\Pipelines\ToStoragePipeline::class,
            ])
            ->then(function ($po) {
                return $po;
            });

        $product->addBuy($po->getAmount());

        $this->productRepository->update($product);

        event(new BuyProductEvent($user, $server, $po->getProduct(), $amount, $po->getValute(), $po->getResultSum(), $po->getDiscount()));

        return $po;
    }
}