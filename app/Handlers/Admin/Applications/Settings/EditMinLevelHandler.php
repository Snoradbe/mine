<?php


namespace App\Handlers\Admin\Applications\Settings;


use App\Entity\Site\User;
use App\Events\Admin\Applications\Settings\EditMinLevelEvent;
use App\Exceptions\Exception;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class EditMinLevelHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, int $minLevel): void
    {
        $settings = settings('applications', DataType::JSON);
        if (is_null($settings)) {
            throw new Exception('Settings `applications` not found!');
        }

        $old = $settings['min_level'] ?? 0;
        $settings['min_level'] = $minLevel;

        $this->settings->set('applications', $settings);
        $this->settings->save();

        event(new EditMinLevelEvent($admin, $old, $minLevel));
    }
}