<?php


namespace App\Handlers\Admin\Unban;


use App\Entity\Site\User;
use App\Events\Admin\Unban\UnbanSettingsEvent;
use App\Services\Settings\Settings;

class UnbanSettingsHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, int $price): void
    {
        $this->settings->set('unban', $price);
        $this->settings->save();

        event(new UnbanSettingsEvent($admin, $price));
    }
}