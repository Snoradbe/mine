<?php


namespace App\Http\Controllers\Install;


use App\Services\Applications\DefaultApplicationSetting;
use App\Services\Permissions\Permissions;

class Install170420191644 extends InstallController
{
    public function install(): string
    {
        if ($this->check()) {
            $this->defaultCabinet();
            $this->defaultShop();
            $this->defaultGameMoney();
            $this->defaultApplications();
            $this->defaultTops();
            $this->defaultUnban();

            $this->settings->save(); //сохраняем настройки в бд
            $this->complete(); //помечаем что инсталл был выполнен и больше не будет выполняться

            return 'ok';
        }

        return 'no';
    }

    private function defaultCabinet(): void
    {
        $this->settings->set('cabinet', [
            'default_permissions' => [ //права у дефолтной группы
                Permissions::CABINET_SKIN_UPLOAD, //загрузка скина
            ],
            'groups' => [ //продаваемые группы (которые есть в таблице pr_groups)
                1 => [ //id сервера (pr_servers)
                    'vip' => [ //группа
                        30 => 150, //период в днях => цена в рублях
                        60 => 250,
                        -1 => 1500 //-1 навсегда
                    ]
                ]
            ],
            'other_groups' => [ //дополнительные группы (fly, god... которые есть в таблице pr_groups)
                1 => [
                    'fly' => [
                        30 => 50
                    ]
                ]
            ],
            'prefix' => [
                'colors' => [ //разрешенные цвета префикса
                    'f' => '#fff', //цвет в игре => цвет на сайте
                    '0' => '#000',
                    '1' => '#0000bf',
                    '2' => '#00bf00',
                    '3' => '#00bfbf',
                    '4' => '#bf0000',
                    '5' => '#bf00bf',
                    '6' => '#bfbf00',
                    '7' => '#bfbfbf',
                    '8' => '#404040',
                    '9' => '#4040ff',
                    'a' => '#40ff40',
                    'b' => '#40ffff',
                    'c' => '#ff4040',
                    'd' => '#ff40ff',
                    'e' => '#ffff40',
                ],

                'min' => 0, //минимальная длина (если будет введено 0 сиволов убирает сам префикс и скобки)
                'max' => 6, //максимальная длина
                'regex' => 'A-Za-zА-Яа-я0-9',
            ],
            'skin' => [
                'w' => 64, //максимальная ширина простого скина
                'h' => 32, //максимальная высота простого скина
                'hd_w' => 1024, //максимальная ширина hd скина
                'hd_h' => 768, //максимальная высота hd скина
                'size' => 1024, //максимальный вес файла в КБ
            ],
            'cloak' => [
                'w' => 64, //максимальная ширина простого плаща
                'h' => 32, //максимальная высота простого плаща
                'hd_w' => 512, //максимальная ширина hd плаща
                'hd_h' => 256, //максимальная высота hd плаща
                'size' => 1024, //максимальный вес файла в КБ
            ],
        ]);
    }

    private function defaultShop(): void
    {
        $this->settings->set('shop', [
            'random_discounts' => [ //Способы рандомной скидки
                [
                    'sql' => 'ORDER BY RAND() LIMIT 5',
                    'name' => 'рандомным способом лимитом в 5 предметов'
                ],
                [
                    'sql' => 'ORDER BY RAND() LIMIT 10',
                    'name' => 'рандомным способом лимитом в 10 предметов'
                ],
                [
                    'sql' => 'ORDER BY RAND() LIMIT 30',
                    'name' => 'рандомным способом лимитом в 30 предметов'
                ],

                [
                    'sql' => 'ORDER BY product.price DESC LIMIT 5',
                    'name' => 'на самые дорогие лимитом в 5 предметов'
                ],
                [
                    'sql' => 'ORDER BY product.price DESC LIMIT 10',
                    'name' => 'на самые дорогие лимитом в 10 предметов'
                ],
                [
                    'sql' => 'ORDER BY product.price DESC LIMIT 30',
                    'name' => 'на самые дорогие лимитом в 30 предметов'
                ],

                [
                    'sql' => '',
                    'name' => 'на все предметы'
                ],
            ],
            'cancel_types' => [ //способы удаления со склада игрока
                'without' => 'Удалить без возврата средств',
                'full' => 'Удалить с полным возвратом средств',
                'half' => 'Удалить с возвращением половины стоимости',
                'standard' => 'Удалить со стандартным снятием средств',
            ],
            'cancel_time' => 3600, //сколько времени дается на возврат товара
            'cancel_fee' => 5, //сколько процентов будет вычтено при отмене покупки
        ]);
    }

    private function defaultGameMoney(): void
    {
        $this->settings->set('game_money', [
            'manager' => [
                'default' => \App\Services\Game\GameMoney\Fe\FeGameMoneyManager::class
            ],
            'rate' => [
                'default' => 8
            ]
        ]);
    }

    private function defaultApplications(): void
    {
        $this->settings->set('applications', [
            'statuses' => [
                'helper' => DefaultApplicationSetting::getData('Хелпер', [])
            ],

            'cooldown' => 999999, //сколько дней должно пройти после последней поданной заявки
        ]);

        return;

        $this->settings->set('applications', [
            'statuses' => [ //на какие должности можно подавать заявки
                'helper' => [ //выдаваемая группа
                    'name' => 'Хелпер', //название должности
                    'enabled' => [ //включена ли подача заявок на сервере
                        1 => true, //id сервера => true|false
                    ],
                    'descr' => 'Типа описание......',
                    'form' => [ //основные вопросы
                        'Первый вопрос',
                        'Второй вопрос',
                        'Ну и так далее...'
                    ],
                    'rules' => [
                        'Первое правило',
                        'Второе правило'
                    ],
                    'server' => [ //серверные вопросы
                        1 => [ //id сервера => вопросы
                            'Вопросы...'
                        ]
                    ]
                ],
                'moder' => [ //выдаваемая группа
                    'name' => 'Модератор', //название должности
                    'enabled' => [ //включена ли подача заявок на сервере
                        1 => true, //id сервера => true|false
                    ],
                    'descr' => 'Типа описание......',
                    'form' => [ //основные вопросы
                        'Первый вопрос',
                        'Второй вопрос',
                        'Ну и так далее...'
                    ],
                    'rules' => [
                        'Первое правило',
                        'Второе правило'
                    ],
                    'server' => [ //серверные вопросы
                        1 => [ //id сервера => вопросы
                            'Вопросы...'
                        ]
                    ]
                ],
                'iventor' => [ //выдаваемая группа
                    'name' => 'Ивентор', //название должности
                    'enabled' => [ //включена ли подача заявок на сервере
                        1 => true, //id сервера => true|false
                    ],
                    'descr' => 'Типа описание......',
                    'form' => [ //основные вопросы
                        'Первый вопрос',
                        'Второй вопрос',
                        'Ну и так далее...'
                    ],
                    'rules' => [
                        'Первое правило',
                        'Второе правило'
                    ],
                    'server' => [ //серверные вопросы
                        1 => [ //id сервера => вопросы
                            'Вопросы...'
                        ]
                    ]
                ],
            ],

            'cooldown' => 999999, //сколько дней должно пройти после последней поданной заявки
        ]);
    }

    private function defaultTops(): void
    {
        $tops = [];

        $reward = function (int $amount, bool $bonus7) {
            return [$amount, $bonus7];
        };

        $addTop = function(string $instance, string $name, string $img, string $secret, array $money, array $coins) use (&$tops) {
            $tops[$name] = [
                'instance' => $instance,
                'img' => $img, //ссылка на картинку топа
                'url' => null, //ссылка на голосование
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
        $addTop(\App\Services\Voting\Tops\TopCraftTop::class, 'topcraft', 'http://topcraft.ru/media/projects/808/tops.png', 'lalala', $reward(1, false), $reward(1, true));
        $addTop('\App\Services\Voting\Tops\McRateTop', 'mcrate', 'http://mcrate.su/bmini.png', 'lalala', $reward(1, false), $reward(1, true));
        $addTop('\App\Services\Voting\Tops\McTopTop', 'mctop', 'https://mctop.su/media/projects/777/tops.png', 'lalala', $reward(1, false), $reward(1, true));

        $this->settings->set('tops.base', [
            'month_rewards' => [600, 300, 150], //награды топа голосующих [1 место, 2 место, 3 место, ...]
            'month_give_max' => false, //выдача наград при одинаковом количестве голосов: true - выдавать бОльшую сумму, false - по порядку
        ]);

        $this->settings->set('tops.tops', $tops);
    }

    private function defaultUnban(): void
    {
        $this->settings->set('unban', 100); //Цена разбана
    }
}