<?php


namespace App\Entity\Game\GameMoney;


interface GameMoney
{
    public function getMoney(): float;

    public function setMoney(float $money): void;
}