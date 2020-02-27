<?php


namespace App\Services\Settings;


use App\Entity\Site\Setting;
use App\Repository\Site\Settings\SettingsRepository;

class Driver
{
    private $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    /**
     * @param Setting[] $oldData
     * @param Setting[] $newData
     */
    public function write(array $oldData, array $newData): void
    {
        foreach ($oldData as $oldDatum) {
            $f = false;
            foreach ($newData as $newDatum) {
                if ($oldDatum->getKey() === $newDatum->getKey()) {
                    $f = true;
                    break;
                }
            }

            if (!$f) {
                $this->repository->delete($oldDatum);
                continue;
            }

            foreach ($newData as $newDatum) {
                if (
                    $oldDatum->getKey() === $newDatum->getKey()
                    &&
                    $oldDatum->getValue() != $newDatum->getValue()
                ) {
                    $this->repository->update($newDatum);
                }
            }
        }

        foreach ($newData as $newDatum) {
            $f = false;
            foreach ($oldData as $oldDatum) {
                if ($newDatum->getKey() === $oldDatum->getKey()) {
                    $f = true;
                    break;
                }
            }

            if (!$f) {
                $this->repository->create($newDatum);
            }
        }
    }
}