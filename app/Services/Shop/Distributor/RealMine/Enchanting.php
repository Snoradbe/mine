<?php


namespace App\Services\Shop\Distributor\RealMine;


use App\Entity\Game\Shop\RealMine;
use App\Entity\Site\Shop\Item;

class Enchanting
{
    public function enchant(Item $item, RealMine $realMine): void
    {
        return; //TODO: сделать все
        $extra = $item->getExtraArray();
        if(!isset($extra['enchants']) || empty($extra['enchants'])) {
            return;
        }

        $enchants = '';

        foreach ($extra['enchants'] as $enchant => $level)
        {
            $level = (int) $level;
            if($level > 0) {
                $enchants .= "_{$enchant}e{$level}";
            }
        }

        if(!empty($enchants)) {
            $fleynaro->setEnchants(substr($enchants, 1));
        }
    }
}