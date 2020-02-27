<?php


namespace App\Helpers;


use App\Exceptions\Exception;

class RandomHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function randomWithChance(array $data)
    {
        return self::getRandomWithChance(
            array_keys($data),
            array_values($data)
        );
    }

    /**
     * @param array $values
     * @param array $chances
     * @return mixed
     */
    private static function getRandomWithChance(array $values, array $chances)
    {
        $sum = 0;
        $result = null;

        do {
            foreach ($values as $i => $value)
            {
                $sum += $chances[$i];
                if (self::random(0, $sum) < $chances[$i]) {
                    $result = $value;
                }
            }
        } while (is_null($sum));

        return $result;
    }

    public static function random(int $min, int $max): int
    {
        return rand($min, $max);
    }
}