<?php


namespace App\Repository\Site\Settings;


use App\Entity\Site\Setting;
use App\Repository\DoctrineClearCache;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSettingsRepository implements SettingsRepository
{
    use DoctrineConstructor, DoctrineClearCache;

    public function getAll(): array
    {
        return $this->er->createQueryBuilder('s')
            ->select()
            ->getQuery()
            ->useResultCache(true, 86400)
            ->getResult();
    }

    public function create(Setting $setting): void
    {
        $this->clearResultCache();
        $this->em->persist($setting);
        $this->em->flush();
    }

    public function update(Setting $setting): void
    {
        $this->clearResultCache();
		$this->em->merge($setting);
        $this->em->flush();
    }

    public function delete(Setting $setting): void
    {
        $this->clearResultCache();
        $this->em->remove($setting);
        $this->em->flush();
    }

    public function deleteAll(): void
    {
        $this->clearResultCache();

        $this->er->createQueryBuilder('s')
            ->delete()
            ->getQuery()
            ->getResult();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}