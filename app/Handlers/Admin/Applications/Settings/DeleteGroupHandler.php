<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\Group;
use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\DeleteGroupEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Group\GroupRepository;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class DeleteGroupHandler
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

    public function handle(User $admin, string $group): void
    {
        $group = $this->getGroup($group);

        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        if (!isset($settings['statuses'][$group->getName()])) {
            throw new Exception('Эта группа еще не добавлена в список заявок!');
        }

        $old = $settings['statuses'][$group->getName()];

       unset( $settings['statuses'][$group->getName()]);

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new DeleteGroupEvent($admin, $group, $old));
    }
}