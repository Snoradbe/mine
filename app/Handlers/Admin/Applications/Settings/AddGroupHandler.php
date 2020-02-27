<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\AddGroupEvent;
use App\Exceptions\Exception;
use App\Helpers\GroupsHelper;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Applications\DefaultApplicationSetting;
use App\Services\Permissions\Permissions;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class AddGroupHandler
{
    private $groupRepository;

    private $serverRepository;

    private $settings;

    public function __construct(GroupRepository $groupRepository, ServerRepository $serverRepository, Settings $settings)
    {
        $this->groupRepository = $groupRepository;
        $this->serverRepository = $serverRepository;
        $this->settings = $settings;
    }

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    public function handle(User $admin, string $group, string $name): void
    {
        $group = $this->getGroup($group);

        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        $old = $settings;

        if (isset($settings['statuses'][$group->getName()])) {
            throw new Exception('Эта группа уже добавлена в список заявок!');
        }
		
		if (
			!$admin->permissions()->hasMPPermission(Permissions::ALL)
			&&
			!in_array(
				$group,
				GroupsHelper::getAllowedManageGroups($admin, null, $this->groupRepository->getAllAdmin(true))
			)
		) {
			throw new Exception('Вы не можете добавить эту группу!');
		}

        $settings['statuses'][$group->getName()] = DefaultApplicationSetting::getData($name, $this->serverRepository->getAll(false));

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new AddGroupEvent($admin, $group));
    }
}