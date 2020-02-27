<?php


namespace App\Services\Cabinet\Cloak;


use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;
use Intervention\Image\Image;

class Resolution
{
    public function isSD(Image $image): bool
    {
        return
            $image->getWidth() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::WIDTH_TYPE)
            &&
            $image->getHeight() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::HEIGHT_TYPE);
    }

    public function isHD(Image $image): bool
    {
        return
            $image->getWidth() > CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::WIDTH_TYPE) && $image->getWidth() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::WIDTH_TYPE, true)
            &&
            $image->getHeight() > CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::HEIGHT_TYPE) && $image->getHeight() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::CLOAK_TYPE, CabinetEnum::HEIGHT_TYPE, true);
    }

    public function isAny(Image $image): bool
    {
        return $this->isSD($image) || $this->isHD($image);
    }
}