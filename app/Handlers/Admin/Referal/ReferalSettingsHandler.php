<?php


namespace App\Handlers\Admin\Referal;


use App\Entity\Site\User;
use App\Events\Admin\Referal\ReferalSettingsEvent;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class ReferalSettingsHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, int $percent, array $levels): void
    {
        $old = settings('referal.handlers', DataType::JSON, []);
        $result = [];

        $result['percent'] = $percent;

        foreach ($levels as $level => $amount)
        {
            $result['level_' . $level] = [
                'money' => $amount
            ];
        }

        $this->settings->set('referal.handlers', $result);
        $this->settings->save();

        event(new ReferalSettingsEvent($admin, $old, $result));
    }
}