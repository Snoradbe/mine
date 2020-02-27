<?php


namespace App\Handlers\Admin\Cabinet\Settings\Prefix;


use App\Entity\Site\User;
use App\Events\Admin\Cabinet\Settings\Prefix\PrefixSettingsEvent;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class PrefixSettingsHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, array $colors, int $min, int $max, string $regex): void
    {
        $settings = settings('cabinet', DataType::JSON);

        $old = $settings['prefix'] ?? [];

        $settings['prefix'] = [
            'colors' => $colors,
            'min' => $min,
            'max' => $max,
            'regex' => $regex
        ];

        $this->settings->set('cabinet', $settings);
        $this->settings->save();

        event(new PrefixSettingsEvent($admin, $old, $settings['prefix']));
    }
}