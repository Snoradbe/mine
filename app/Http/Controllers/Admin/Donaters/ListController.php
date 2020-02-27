<?php


namespace App\Http\Controllers\Admin\Donaters;


use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Site\Server\ServerRepository;
use App\Repository\Site\UserGroup\UserGroupRepository;
use App\Services\Auth\Auth;
use App\Services\Permissions\Permissions;

class ListController extends Controller
{
    public function render(UserGroupRepository $userGroupRepository, ServerRepository $serverRepository)
    {
        NavMenu::$active = 'donaters';

        $list = [];
        $userGroups = $userGroupRepository->getAll();

        $servers = Auth::getUser()->permissions()->hasMPPermission(Permissions::MP_DONATERS_VIEW_ALL)
            ? null
            : Auth::getUser()->permissions()->getServersWithPermission(Permissions::MP_DONATERS_VIEW);

        foreach ($serverRepository->getAll(false) as $server)
        {
            if (is_array($servers) && !in_array($server, $servers)) {
                continue;
            }

            if (!isset($list[$server->getId()])) {
                $list[$server->getId()] = [
                    'server' => $server,
                    'groups' => []
                ];
            }

            foreach ($userGroups as $userGroup)
            {
                if ($userGroup->getServer() === $server) {
                    if (!isset($list[$server->getId()]['groups'][$userGroup->getGroup()->getId()])) {
                        $list[$server->getId()]['groups'][$userGroup->getGroup()->getId()] = [
                            'group' => $userGroup->getGroup(),
                            'users' => []
                        ];
                    }

                    $list[$userGroup->getServer()->getId()]['groups'][$userGroup->getGroup()->getId()]['users'][] = $userGroup;
                }
            }
        }

        return view('admin.donaters.list', [
            'list' => $list
        ]);
    }
}