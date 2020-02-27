<?php


namespace App\Handlers\Admin\Cabinet\Settings;


use App\Entity\Site\User;
use App\Events\Admin\Cabinet\Settings\SkinCloakSettingsEvent;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class SkinCloakSettingsHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, array $skin, array $cloak)
    {
        $settings = settings('cabinet', DataType::JSON);

        /*$skin['path'] = $settings['skin']['path'];
        $cloak['path'] = $settings['cloak']['path'];*/

        $oldSkin = $settings['skin'] ?? [];
        $oldCloak = $settings['cloak'] ?? [];

        $settings['skin'] = $skin;
        $settings['cloak'] = $cloak;

        $this->settings->set('cabinet', $settings);
        $this->settings->save();

        event(new SkinCloakSettingsEvent($admin, $oldSkin, $settings['skin'], $oldCloak, $settings['cloak']));
    }
}