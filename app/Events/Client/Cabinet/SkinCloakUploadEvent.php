<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Event;

class SkinCloakUploadEvent extends ClientEvent implements Event
{
    /**
     * @var string
     */
    private $type;

    /**
     * SkinCloakUploadEvent constructor.
     * @param User $user
     * @param string $type
     */
    public function __construct(User $user, string $type)
    {
        parent::__construct($user);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type
        ];
    }
}