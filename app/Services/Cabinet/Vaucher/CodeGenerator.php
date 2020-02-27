<?php


namespace App\Services\Cabinet\Vaucher;


use App\Repository\Site\Vaucher\VaucherRepository;

class CodeGenerator
{
    private $vaucherRepository;

    public function __construct(VaucherRepository $vaucherRepository)
    {
        $this->vaucherRepository = $vaucherRepository;
    }

    public function checkCode(string $code): bool
    {
        return is_null($this->vaucherRepository->findByCode($code));
    }

    public function generate(): string
    {
        do {
            $code = 'RealMine-' . str_random(11);
        } while (!$this->checkCode($code));

        return $code;
    }
}