<?php


namespace App\Events\Client\Settings;


use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class Google2faEvent extends ClientEvent implements Event
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * Google2faEvent constructor.
     * @param User $user
     * @param bool $enabled
     */
    public function __construct(User $user, bool $enabled)
    {
        parent::__construct($user);

        $this->enabled = $enabled;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'enabled' => $this->enabled
        ];
    }
}