<?php


namespace App\Services\Payment\Payers;


class Pool
{
    private $payers;

    public function __construct(array $payers)
    {
        $this->payers = $payers;
    }

    public function find(string $name): ?Payer
    {
        foreach ($this->payers as $payer)
        {
            if($payer->getName() === $name) {
                return $payer;
            }
        }

        return null;
    }

    public function all(): array
    {
        return $this->payers;
    }
}