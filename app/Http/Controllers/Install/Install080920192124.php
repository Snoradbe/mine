<?php


namespace App\Http\Controllers\Install;


class Install080920192124 extends InstallController
{
    public function install(): string
    {
		if (!$this->check()) {
			return 'no';
		}
		
        $tops = [];

        $reward = function (int $amount, bool $bonus7) {
            return [$amount, $bonus7];
        };

        $addTop = function(string $instance, string $name, string $img, string $url, string $secret, array $money, array $coins) use (&$tops) {
            $tops[$name] = [
                'instance' => $instance,
                'img' => $img, //ссылка на картинку топа
                'url' => $url, //ссылка на голосование
                'secret' => $secret, //секретный ключ
                'rewards' => [
                    'money' => [
                        'amount' => $money[0], //количество денег | int
                        '7bonus' => $money[1], //выдавать x2 в первые 7 дней? | bool
                    ],
                    'coins' => [
                        'amount' => $coins[0],
                        '7bonus' => $coins[1]
                    ],
                ],
                'enabled' => true //включен топ
            ];
        };

        //TODO: сделать хандлеры для всех (\App\Services\Voting\Tops\TopCraftTop::class)
        $addTop(\App\Services\Voting\Tops\TopCraftTop::class, 'topcraft', 'http://topcraft.ru/media/projects/808/tops.png', 'http://topcraft.ru/servers/808/', 'lalala', $reward(1, false), $reward(1, true));
        $addTop('\App\Services\Voting\Tops\McRateTop', 'mcrate', 'http://mcrate.su/bmini.png', 'http://mcrate.su/rate/5130', 'lalala', $reward(1, false), $reward(1, true));
        $addTop('\App\Services\Voting\Tops\McTopTop', 'mctop', 'https://mctop.su/media/projects/777/tops.png', 'https://mctop.su/servers/777/', 'lalala', $reward(1, false), $reward(1, true));

        $this->settings->set('tops.base', [
            'month_rewards' => [600, 300, 150], //награды топа голосующих [1 место, 2 место, 3 место, ...]
            'month_give_max' => false, //выдача наград при одинаковом количестве голосов: true - выдавать бОльшую сумму, false - по порядку
        ]);

        $this->settings->set('tops.tops', $tops);
		$this->settings->save(); //сохраняем настройки в бд
        $this->complete(); //помечаем что инсталл был выполнен и больше не будет выполняться
		
		return 'ok';
    }
}