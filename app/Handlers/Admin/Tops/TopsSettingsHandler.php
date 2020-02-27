<?php


namespace App\Handlers\Admin\Tops;


use App\Entity\Site\User;
use App\Events\Admin\Tops\TopsSettingsEvent;
use App\Services\Settings\DataType;
use App\Services\Settings\Settings;

class TopsSettingsHandler
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(User $admin, array $monthRewards, bool $monthGiveMax, ?array $enabled, array $rewards): void
    {
        $base = settings('tops.base', DataType::JSON, []);

        $oldBase = $base;

        $base['month_rewards'] = $monthRewards;
        $base['month_give_max'] = $monthGiveMax;

        $this->settings->set('tops.base', $base);

        $tops = settings('tops.tops', DataType::JSON, []);
        $oldTops = $tops;

        foreach ($rewards as $top => $data)
        {
            $tops[$top] = [
                'instance' => strip_tags($tops[$top]['instance']), //обработчик
                'img' => strip_tags($data['img']), //ссылка на картинку топа
                'url' => strip_tags($data['url']), //ссылка на голосование
                'secret' => strip_tags($data['secret']), //секретный ключ
                'rewards' => [
                    'money' => [
                        'amount' => (int) $data['money'], //количество денег | int
                        '7bonus' => (bool) ($data['money_7bonus'] ?? false), //выдавать x2 в первые 7 дней? | bool
                    ],
                    'coins' => [
                        'amount' => (int) $data['coins'],
                        '7bonus' => (bool) ($data['coins_7bonus'] ?? false)
                    ],
                ],
                'enabled' => (bool) ($enabled[$top] ?? false) //включен топ
            ];
        }

        $this->settings->set('tops.tops', $tops);
        $this->settings->save();

        event(new TopsSettingsEvent($admin, $oldBase, $base, $oldTops, $tops));
    }
}