<?php


namespace App\Http\Controllers\Client\Cabinet;


use App\Entity\Site\Group;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Auth\Auth;
use App\Services\Cabinet\CabinetEnum;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Response\JsonResponse;

class CabinetController extends Controller
{
    public function load(GroupRepository $groupRepository)
    {
        $path = config('site.skin_cloak.path');

        $skin = FileHelper::imageToBase64(
            sprintf($path . '/skins/%s.png', Auth::getUser()->getName())
        );
        $cloak = FileHelper::imageToBase64(
            sprintf($path . '/cloaks/%s.png', Auth::getUser()->getName())
        );

        $skinSettings = CabinetSettings::getSkinCloakSettings(CabinetEnum::SKIN_TYPE);
        $skinSettings['url'] = $skin;

        $cloakSettings = CabinetSettings::getSkinCloakSettings(CabinetEnum::CLOAK_TYPE);
        $cloakSettings['url'] = $cloak;

        return new JsonResponse([
            'settings' => [
                //module_id => [data]
                'skin' => [
                    'default' => 'uploads/default_skin.png',
                    'skin' => $skinSettings,
                    'cloak' => $cloakSettings,
                ],
                'groups' => [
                    'groups' => array_map(function (Group $group) {
                        return $group->toArray();
                    }, $groupRepository->getAllDonate(false, 'ASC')),
                    'configGroups' => CabinetSettings::getGroupsSettings(true),
                    'configOtherGroups' => CabinetSettings::getGroupsSettings(false),
                ],
                'prefix' => CabinetSettings::getPrefixSettings()
            ],
        ]);
    }
}