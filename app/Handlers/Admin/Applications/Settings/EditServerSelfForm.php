<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\EditServerSelfFormEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Permissions\Permissions;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class EditServerSelfForm
{
    private $groupRepository;

    private $settings;

    private $serverRepository;

    public function __construct(GroupRepository $groupRepository, Settings $settings, ServerRepository $serverRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->settings = $settings;
        $this->serverRepository = $serverRepository;
    }

    private function getGroup(string $name): Group
    {
        $group = $this->groupRepository->findByName($name);
        if (is_null($group)) {
            throw new Exception('Группа не найдена!');
        }

        return $group;
    }

    public function handle(User $admin, string $group, array $forms): void
    {
        $group = $this->getGroup($group);

        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        if (!isset($settings['statuses'][$group->getName()])) {
            throw new Exception('Эта группа еще не добавлена в список заявок! Сначала добавьте её');
        }

        $old = $settings['statuses'][$group->getName()]['server'] ?? [];

        $servers = $admin->permissions()->hasMPPermission(Permissions::MP_APPLICATIONS_FORMS_ALL)
            ? $this->serverRepository->getAll()
            : $admin->permissions()->getServersWithPermission(Permissions::MP_APPLICATIONS_FORMS_SERVER);

        if (is_null($servers)) {
            $servers = $this->serverRepository->getAll();
        }

        foreach ($servers as $server)
        {
            if (isset($forms[$server->getId()])) {
                $settings['statuses'][$group->getName()]['server'][$server->getId()] = explode("\n", $forms[$server->getId()]);
            } else {
                $settings['statuses'][$group->getName()]['server'][$server->getId()] = [];
            }
        }

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new EditServerSelfFormEvent($admin, $group, $old, $settings['statuses'][$group->getName()]['server']));
    }
}