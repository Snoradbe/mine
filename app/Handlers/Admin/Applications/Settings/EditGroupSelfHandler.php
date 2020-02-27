<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\EditGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Permissions\Permissions;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class EditGroupSelfHandler
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

    public function handle(User $admin, string $group, array $enabled): void
    {
        $group = $this->getGroup($group);

        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        if (!isset($settings['statuses'][$group->getName()])) {
            throw new Exception('Эта группа еще не добавлена в список заявок! Сначала добавьте её');
        }

        $oldEnabled = $settings['statuses'][$group->getName()]['enabled'] ?? [];

        if (!isset($settings['statuses'][$group->getName()]['enabled'])) {
            $settings['statuses'][$group->getName()]['enabled'] = [];
        }
        $servers = $admin->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_FORMS_ALL)
            ? $this->serverRepository->getAll()
            : $admin->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_FORMS_SERVER);

        if (is_null($servers)) {
            $servers = $this->serverRepository->getAll();
        }
        foreach ($servers as $server)
        {
            if (isset($enabled[$server->getId()])) {
                $settings['statuses'][$group->getName()]['enabled'][$server->getId()] = (bool) $enabled[$server->getId()];
            } else {
                $settings['statuses'][$group->getName()]['enabled'][$server->getId()] = false;
            }
        }

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new EditGroupEvent(
            $admin, $group,
            $settings['statuses'][$group->getName()]['name'], $settings['statuses'][$group->getName()]['name'],
            $oldEnabled, $settings['statuses'][$group->getName()]['enabled']
        ));
    }
}