<?php


namespace App\Repository\Site\Shop\Packet;


use App\Entity\Site\Shop\Packet;
use App\Entity\Site\Shop\Product;
use Doctrine\ORM\EntityManagerInterface;

interface PacketRepository
{
    public function create(Packet $packet, bool $flush = true): EntityManagerInterface;

    public function update(Packet $packet, bool $flush = true): EntityManagerInterface;

    public function delete(Packet $packet, bool $flush = true): EntityManagerInterface;

    public function deleteAll(Product $product): void;
}