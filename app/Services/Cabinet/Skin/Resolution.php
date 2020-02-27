<?php


namespace App\Services\Cabinet\Skin;


use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;
use Intervention\Image\Image;

class Resolution
{
    public function isSD(Image $image): bool
    {
        return
            $image->getWidth() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, CabinetEnum::WIDTH_TYPE)
            &&
            $image->getHeight() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, CabinetEnum::HEIGHT_TYPE);
    }

    public function isHD(Image $image): bool
    {
        return
            $image->getWidth() > CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, CabinetEnum::WIDTH_TYPE)
                && $image->getWidth() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, 'w', true)
            &&
            $image->getHeight() > CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, CabinetEnum::HEIGHT_TYPE)
                && $image->getHeight() <= CabinetSettings::getSkinCloakResolution(CabinetEnum::SKIN_TYPE, 'h', true);
    }

    public function isAny(Image $image): bool
    {
        return $this->isSD($image) || $this->isHD($image);
    }
}