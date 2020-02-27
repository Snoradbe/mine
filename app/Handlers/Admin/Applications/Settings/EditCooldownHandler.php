<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\EditCooldownEvent;
use App\Exceptions\Exception;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class EditCooldownHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, int $cooldown): void
    {
        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        $old = $settings['cooldown'] ?? 0;
        $settings['cooldown'] = $cooldown;

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new EditCooldownEvent($admin, $old, $cooldown));
    }
}