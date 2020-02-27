<?php


namespace App\Events\Client\Promo;


use App\Entity\Site\User;
use App\Entity\Site\Vaucher;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class UsePromoEvent extends ClientEvent implements Event
{
    /**
     * @var Vaucher
     */
    private $vaucher;

    /**
     * UsePromoEvent constructor.
     * @param User $user
     * @param Vaucher $vaucher
     * @param array $data
     */
    public function __construct(User $user, Vaucher $vaucher)
    {
        parent::__construct($user);

        $this->vaucher = $vaucher;
    }

    /**
     * @return Vaucher
     */
    public function getVaucher(): Vaucher
    {
        return $this->vaucher;
    }

    /**
     * Если ваучер выдает валюту, то запишем это как пополнение счета
     * @return array|null - [amount, valute]
     */
    public function getReceived(): ?array
    {
        $type = config('site.vauchers.types.' . $this->vaucher->getType());
        if (!is_null($type) && $type['is_valute']) {
            $amount = $this->vaucher->getValueArray()['amount'] ?? null;
            $valute = $this->vaucher->getValueArray()['valute'] ?? null;

            return [$amount, $valute];
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'promo' => [
                'id' => $this->vaucher->getId(),
                'code' => $this->vaucher->getCode(),
                'type' => $this->vaucher->getType(),
                'reward' => $this->vaucher->getValueArray()
            ]
        ];
    }
}