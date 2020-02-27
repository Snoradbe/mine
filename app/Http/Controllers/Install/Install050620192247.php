<?php


namespace App\Http\Controllers\Install;


class Install050620192247 extends InstallController
{
    public function install(): string
    {
        if ($this->check()) {
            $this->defaultReferal();

            $this->settings->save();
            $this->complete();

            return 'ok';
        }

        return 'no';
    }

    private function defaultReferal(): void
    {
        $this->settings->set('referal.handlers', [
            'level_2' => [ //При достижении 2 уровня (необязательно)
                'money' => 10, //выдавать рубли (необязательно)
                'coins' => 2 //выдавать монеты (необязательно)
            ],
            'percent' => 40, //количество процентов от пополняемой суммы идет рефереру 0 - 100 (необязательно)
        ]);
    }
}