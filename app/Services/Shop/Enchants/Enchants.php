<?php


namespace App\Services\Shop\Enchants;


use Doctrine\Common\Collections\ArrayCollection;

class Enchants
{
    private const LIST = [
        34 => ['name' => 'Прочность', 'enchant_name' => ''],

        0 => ['name' => 'Защита', 'enchant_name' => ''],
        1 => ['name' => 'Огнеупорность', 'enchant_name' => ''],
        2 => ['name' => 'Невесомость', 'enchant_name' => ''],
        3 => ['name' => 'Взрывоустойчивость', 'enchant_name' => ''],
        4 => ['name' => 'Снарядостойкость', 'enchant_name' => ''],
        5 => ['name' => 'Подводное дыхание', 'enchant_name' => ''],
        6 => ['name' => 'Подводник', 'enchant_name' => ''],
        7 => ['name' => 'Шипы', 'enchant_name' => ''],

        16 => ['name' => 'Острота', 'enchant_name' => ''],
        17 => ['name' => 'Небесная кара', 'enchant_name' => ''],
        18 => ['name' => 'Гибель насекомых', 'enchant_name' => ''],
        19 => ['name' => 'Отдача', 'enchant_name' => ''],
        20 => ['name' => 'Заговор огня', 'enchant_name' => ''],
        21 => ['name' => 'Добыча', 'enchant_name' => ''],
        48 => ['name' => 'Сила', 'enchant_name' => ''],
        49 => ['name' => 'Откидывание', 'enchant_name' => ''],
        50 => ['name' => 'Горящая стрела', 'enchant_name' => ''],
        51 => ['name' => 'Бесконечность', 'enchant_name' => ''],

        32 => ['name' => 'Эффективность', 'enchant_name' => ''],
        33 => ['name' => 'Шёлковое касание', 'enchant_name' => ''],
        35 => ['name' => 'Удача', 'enchant_name' => ''],

        61 => ['name' => 'Морская удача', 'enchant_name' => ''],
        62 => ['name' => 'Приманка', 'enchant_name' => ''],
    ];

    private static $cacheObjects = [];

    private function __construct(){}

    private static function fromCache($key): ?Enchant
    {
        return static::$cacheObjects[$key] ?? null;
    }

    private static function toCache($key, Enchant $enchant): void
    {
        static::$cacheObjects[$key] = $enchant;
    }

    public static function getById(int $id): ?Enchant
    {
        $enchant = self::fromCache($id);
        if (is_null($enchant)) {
            $enchant = static::LIST[$id] ?? null;
            if (!is_null($enchant)) {
                $enchant = new Enchant($id, $enchant['name'], $enchant['enchant_name']);
                self::toCache($id, $enchant);
            }
        }

        return $enchant;
    }

    public static function getByName(string $name): ?Enchant
    {
        $enchant = self::fromCache($name);
        if (!is_null($enchant)) {
            return $enchant;
        }

        foreach (static::LIST as $id => $data)
        {
            if ($data['enchant_name'] == $name) {
                $enchant = new Enchant($id, $data['name'], $data['enchant_name']);
                self::toCache($name, $enchant);

                return $enchant;
            }
        }

        return null;
    }

    public static function all(): ArrayCollection
    {
        $list = new ArrayCollection();

        foreach (static::LIST as $id => $data)
        {
            $enchant = self::fromCache($id);
            if (is_null($enchant)) {
                $enchant = new Enchant($id, $data['name'], $data['enchant_name']);
                self::toCache($id, $enchant);
            }

            $list->add($enchant);
        }

        return $list;
    }
}