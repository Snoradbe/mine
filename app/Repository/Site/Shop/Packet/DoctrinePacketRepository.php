<?php


namespace App\Repository\Site\Shop\Packet;


use App\Entity\Site\Shop\Packet;
use App\Entity\Site\Shop\Product;
use App\Repository\DoctrineConstructor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePacketRepository implements PacketRepository
{
    use DoctrineConstructor;

    public function create(Packet $packet, bool $flush = true): EntityManagerInterface
    {
        $this->em->persist($packet);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function update(Packet $packet, bool $flush = true): EntityManagerInterface
    {
        if ($flush) {
            $this->em->flush($packet);
        }

        return $this->em;
    }

    public function delete(Packet $packet, bool $flush = true): EntityManagerInterface
    {
        $this->em->remove($packet);
        if ($flush) {
            $this->em->flush();
        }

        return $this->em;
    }

    public function deleteAll(Product $product): void
    {
        $this->er->createQueryBuilder('shop_packet')
            ->delete()
            ->where('shop_packet.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->execute();
    }
}