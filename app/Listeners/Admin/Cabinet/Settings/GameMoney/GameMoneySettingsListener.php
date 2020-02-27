<?php


namespace App\Listeners\Admin\Cabinet\Settings\GameMoney;


use App\Entity\Site\Log;
use App\Events\Admin\Cabinet\Settings\GameMoney\GameMoneySettingsEvent;
use App\Listeners\Listener;
use App\Listeners\SiteLogListener;

class GameMoneySettingsListener implements Listener
{
    use SiteLogListener;

    /**
     * @param GameMoneySettingsEvent $event
     */
    public function writeToLogs(GameMoneySettingsEvent $event): void
    {
        $this->create(new Log(
            $event->getAdmin(),
            null,
            10,
            $event->getIp(),
            $event->toArray()
        ));
    }

    /**
     * @param GameMoneySettingsEvent $event
     */
    public function handle($event): void
    {
        $this->writeToLogs($event);
    }
}