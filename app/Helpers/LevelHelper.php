<?php


namespace App\Helpers;


class LevelHelper
{
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public const BASE = 8; //базовое количество опыта
    public const STEP = 4; //сколько будет прибавлено каждый уровень
    public const FACTOR = 6; //на сколько будет умножен результат (в 1 часе 6 раз по 10 минут)
    public const FACTOR_EXP = 10; //сколько равен 1 опыт

    /**
     * Сколько нужно опыта до следующего уровня
     *
     * @param int $currentLevel
     * @return int
     */
    public static function getNextLevelExp(int $currentLevel): int
    {
        return (static::FACTOR * static::FACTOR_EXP) * (static::BASE + (static::STEP * ($currentLevel - 1)));
    }
}