<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\EditFormEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class EditFormHandler
{
    private $groupRepository;

    private $settings;

    public function __construct(GroupRepository $groupRepository, Settings $settings)
    {
        $this->groupRepository = $groupRepository;
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

    public function handle(User $admin, string $group, string $form): void
    {
        $group = $this->getGroup($group);

        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        if (!isset($settings['statuses'][$group->getName()])) {
            throw new Exception('Эта группа еще не добавлена в список заявок! Сначала добавьте её');
        }

        $old = $settings[$group->getName()]['form'] ?? [];

        $settings['statuses'][$group->getName()]['form'] = explode("\n", $form);

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new EditFormEvent($admin, $group, $old, $settings['statuses'][$group->getName()]['form']));
    }
}