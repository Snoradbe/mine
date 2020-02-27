<?php


namespace App\Services\Shop\Enchants;


use App\Entity\Site\Shop\Packet;
use App\Entity\Site\Shop\Product;
use App\Exceptions\Exception;

class Enchanting
{
    private function __construct(){}

    /**
     * @param Product $product
     * @param array $enchants - [id => level, id => level...]
     * @throws Exception
     */
    public static function enchantProduct(Product $product, array $enchants): void
    {
        $productData = $product->getArrayData();

        if (empty($enchants)) {
            if (isset($productData['enchants'])) {
                unset($productData['enchants']);

                $product->setData($productData);
            }

            return;
        }

        $productData['enchants'] = [];

        foreach ($enchants as $enchantId => $level)
        {
            $enchant = Enchants::getById($enchantId);
            if (is_null($enchant)) {
                throw new Exception("Зачаровывание $enchantId не найдено!");
            }

            $productData['enchants']['e_' . $enchant->getId()] = $level;
        }

        $product->setData($productData);
    }

    /**
     * @param Packet $packet
     * @param array $enchants
     */
    public static function enchantPacket(Packet $packet, array $enchants): void
    {
        $packetData = $packet->getDataArray();

        if (empty($enchants)) {
            if (isset($packetData['enchants'])) {
                unset($packetData['enchants']);

                $packet->setData($packetData);
            }

            return;
        }

        $packetData['enchants'] = [];

        foreach ($enchants as $enchantId => $level)
        {
            $enchant = Enchants::getById($enchantId);
            if (is_null($enchant)) {
                //throw new Exception("Зачаровывание $enchantId не найдено!");
                continue;
            }

            $packetData['enchants']['e_' . $enchant->getId()] = $level;
        }

        if (empty($packetData['enchants'])) {
            unset($packetData['enchants']);
        }

        $packet->setData($packetData);
    }
}