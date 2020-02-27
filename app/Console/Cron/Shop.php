<?php


namespace App\Console\Cron;


use App\Repository\Site\Shop\Product\ProductRepository;
use Illuminate\Console\Command;

class Shop extends Command
{
    protected $signature = 'rm:cron:shop';

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();

        $this->productRepository = $productRepository;
    }

    public function handle()
    {
        $this->productRepository->removeExpiredDiscounts();
    }
}