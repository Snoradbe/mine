<?php


namespace App\Handlers\Client\Shop;


use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\Shop\Product\ProductRepository;
use App\Services\Shop\Search;
use Illuminate\Pagination\LengthAwarePaginator;

class LoadProductsHandler
{
    private $serverRepository;

    private $productRepository;

    public function __construct(ServerRepository $serverRepository, ProductRepository $productRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->productRepository = $productRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    public function handle(Search $search, int $page): LengthAwarePaginator
    {
        $server = $this->getServer($search->getServer());

        return $this->productRepository->getAll($search,true, $page);
    }
}